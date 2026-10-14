<?php

namespace App\Http\Controllers;

use App\Models\AcademicArea;
use App\Models\Comment;
use App\Models\Document;
use App\Models\DocumentDownload;
use App\Models\DocumentType;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user() && $request->user()->is_active && $request->user()->role === 'admin', 403);
    }

    public function dashboard(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.dashboard', [
            'usersCount' => User::count(),
            'documentsCount' => Document::count(),
            'viewsCount' => Document::sum('views_count'),
            'downloadsCount' => DocumentDownload::count(),
            'pendingCount' => Report::where('status', 'pending')->count(),
            'storageBytes' => Document::sum('file_size'),
            'recentReports' => Report::with(['document', 'user'])->latest()->limit(5)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.users', ['users' => User::query()
            ->when($request->filled('q'), fn ($q) => $q->where(function ($inner) use ($request) {
                $term = $request->input('q');
                $inner->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%");
            }))->latest()->paginate(15)->withQueryString()]);
    }

    public function toggleUser(Request $request, User $user)
    {
        $this->ensureAdmin($request);
        abort_if($user->id === $request->user()->id, 422, 'No puedes suspender tu propia cuenta.');
        abort_if($user->role === 'admin', 422, 'No se puede suspender a otro administrador desde este panel.');
        $user->is_active = ! $user->is_active;
        $user->save();
        return back()->with('status', 'El estado de la cuenta fue actualizado.');
    }

    public function documents(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.documents', ['documents' => Document::with(['user', 'academicArea', 'documentType'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->input('q').'%'))
            ->latest()->paginate(15)->withQueryString()]);
    }

    public function documentStatus(Request $request, Document $document)
    {
        $this->ensureAdmin($request);
        $data = $request->validate(['status' => ['required', Rule::in(['published', 'hidden'])]]);
        $document->update(['status' => $data['status']]);
        return back()->with('status', 'Estado de publicación actualizado.');
    }

    public function reports(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.reports', ['reports' => Report::with(['document', 'user', 'reviewer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()->paginate(15)->withQueryString()]);
    }

    public function resolveReport(Request $request, Report $report)
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'status' => ['required', Rule::in(['reviewed', 'dismissed'])],
            'resolution_notes' => ['required', 'string', 'max:2000'],
            'hide_document' => ['sometimes', 'boolean'],
        ]);
        DB::transaction(function () use ($request, $report, $data) {
            $report->update([
                'status' => $data['status'],
                'resolution_notes' => $data['resolution_notes'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
            if ($request->boolean('hide_document') && $data['status'] === 'reviewed') {
                $report->document()->update(['status' => 'hidden']);
            }
        });
        return back()->with('status', 'Reporte atendido correctamente.');
    }

    public function categories(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.categories', [
            'areas' => AcademicArea::withCount('documents')->orderBy('name')->get(),
            'types' => DocumentType::withCount('documents')->orderBy('name')->get(),
        ]);
    }

    public function addCategory(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'kind' => ['required', Rule::in(['area', 'type'])],
            'name' => ['required', 'string', 'max:100'],
        ]);
        $name = trim($data['name']);
        if ($data['kind'] === 'area') {
            $request->validate(['name' => ['unique:academic_areas,name']]);
            AcademicArea::create(['name' => $name, 'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)), 'is_active' => true]);
        } else {
            $request->validate(['name' => ['unique:document_types,name']]);
            DocumentType::create(['name' => $name, 'is_active' => true]);
        }
        return back()->with('status', 'Categoría creada.');
    }

    public function toggleCategory(Request $request, string $kind, int $id)
    {
        $this->ensureAdmin($request);
        abort_unless(in_array($kind, ['area', 'type'], true), 404);
        $category = $kind === 'area' ? AcademicArea::findOrFail($id) : DocumentType::findOrFail($id);
        $category->is_active = ! $category->is_active;
        $category->save();
        return back()->with('status', 'Estado de categoría actualizado.');
    }

    public function comments(Request $request)
    {
        $this->ensureAdmin($request);
        return view('admin.live.comments', ['comments' => Comment::with(['user', 'document'])->latest()->paginate(15)]);
    }

    public function toggleComment(Request $request, Comment $comment)
    {
        $this->ensureAdmin($request);
        $comment->is_visible = ! $comment->is_visible;
        $comment->save();
        return back()->with('status', 'Visibilidad de comentario actualizada.');
    }

    public function deleteUser(Request $request, User $user)
    {
        $this->ensureAdmin($request);
        abort_if($user->id === $request->user()->id || $user->role === 'admin', 422, 'No se puede eliminar una cuenta administrativa.');

        $references = [
            ['documents', 'user_id'], ['reports', 'user_id'], ['reports', 'reviewed_by'],
            ['comments', 'user_id'], ['favorites', 'user_id'], ['ratings', 'user_id'],
            ['document_views', 'user_id'], ['document_downloads', 'user_id'],
        ];
        foreach ($references as [$table, $column]) {
            if (DB::table($table)->where($column, $user->id)->exists()) {
                return back()->withErrors(['delete' => 'No se puede eliminar esta cuenta porque tiene actividad asociada. Primero revisa sus publicaciones y registros. Puedes suspenderla.']);
            }
        }

        try {
            DB::transaction(function () use ($user) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
                $user->delete();
            });
        } catch (QueryException $exception) {
            return back()->withErrors(['delete' => 'La cuenta tiene referencias adicionales y no puede eliminarse todavía.']);
        }
        return back()->with('status', 'Cuenta eliminada definitivamente.');
    }

    public function deleteDocument(Request $request, int $document)
    {
        $this->ensureAdmin($request);
        $record = Document::withTrashed()->findOrFail($document);
        $disk = Storage::disk('local');
        $original = $record->file_path;
        $moved = false;
        $temporary = 'academic-documents-trash/'.Str::uuid().'-'.basename($original);

        if ($original && $disk->exists($original)) {
            if (! $disk->move($original, $temporary)) {
                return back()->withErrors(['delete' => 'No se pudo preparar el archivo para su eliminación.']);
            }
            $moved = true;
        }

        try {
            DB::transaction(fn () => $record->forceDelete());
        } catch (\Throwable $exception) {
            if ($moved) {
                $disk->move($temporary, $original);
            }
            throw $exception;
        }

        if ($moved && ! $disk->delete($temporary)) {
            return back()->withErrors(['delete' => 'Publicación eliminada de la base de datos, pero no pudo borrarse el archivo temporal del disco. Revisa academic-documents-trash.']);
        }
        return back()->with('status', 'Publicación y archivo físico eliminados definitivamente.');
    }

    public function deleteReport(Request $request, Report $report)
    {
        $this->ensureAdmin($request);
        $report->delete();
        return back()->with('status', 'Reporte eliminado definitivamente.');
    }

    public function deleteComment(Request $request, Comment $comment)
    {
        $this->ensureAdmin($request);
        $comment->delete();
        return back()->with('status', 'Comentario eliminado definitivamente.');
    }

    public function deleteCategory(Request $request, string $kind, int $id)
    {
        $this->ensureAdmin($request);
        abort_unless(in_array($kind, ['area', 'type'], true), 404);
        $category = $kind === 'area' ? AcademicArea::findOrFail($id) : DocumentType::findOrFail($id);

        $column = $kind === 'area' ? 'academic_area_id' : 'document_type_id';
        if (Document::withTrashed()->where($column, $category->id)->exists()) {
            return back()->withErrors(['delete' => 'No se puede eliminar una categoría asociada a publicaciones, aunque estén retiradas. Puedes desactivarla.']);
        }
        if ($kind === 'area' && DB::table('subjects')->where('academic_area_id', $category->id)->exists()) {
            return back()->withErrors(['delete' => 'No se puede eliminar el área porque contiene asignaturas.']);
        }

        try {
            $category->delete();
        } catch (QueryException $exception) {
            return back()->withErrors(['delete' => 'La categoría tiene dependencias y no puede eliminarse todavía.']);
        }
        return back()->with('status', 'Categoría eliminada definitivamente.');
    }
}
