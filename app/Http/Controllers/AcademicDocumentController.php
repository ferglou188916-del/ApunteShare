<?php

namespace App\Http\Controllers;

use App\Models\AcademicArea;
use App\Models\Document;
use App\Models\DocumentDownload;
use App\Models\DocumentType;
use App\Models\Favorite;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\DocumentView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AcademicDocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = Document::query()->with(['user', 'academicArea', 'documentType'])
            ->where('status', 'published')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($q) use ($request) {
                $term = $request->string('q')->toString();
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('topic', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            }))
            ->when($request->filled('area'), fn ($query) => $query->where('academic_area_id', $request->input('area')))
            ->when($request->filled('type'), fn ($query) => $query->where('document_type_id', $request->input('type')))
            ->latest()->paginate(12)->withQueryString();

        return view('documents.live_index', [
            'documents' => $documents,
            'areas' => AcademicArea::where('is_active', true)->orderBy('name')->get(),
            'types' => DocumentType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->is_active && $request->user()->role === 'contributor', 403);

        return view('documents.live_upload', [
            'areas' => AcademicArea::where('is_active', true)->orderBy('name')->get(),
            'types' => DocumentType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->is_active && $request->user()->role === 'contributor', 403);

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,docx'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:3000'],
            'academic_area_id' => ['required', Rule::exists('academic_areas', 'id')->where('is_active', true)],
            'document_type_id' => ['required', Rule::exists('document_types', 'id')->where('is_active', true)],
            'topic' => ['required', 'string', 'max:160'],
            'academic_cycle' => ['required', 'string', 'max:30'],
        ]);

        $file = $request->file('file');
        $path = $file->store('academic-documents', 'local');
        try {
            $document = DB::transaction(fn () => Document::create([
                'user_id' => $request->user()->id,
                'academic_area_id' => $data['academic_area_id'],
                'document_type_id' => $data['document_type_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'topic' => $data['topic'],
                'academic_cycle' => $data['academic_cycle'],
                'university' => 'Universidad Don Bosco',
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status' => 'published',
                'views_count' => 0,
            ]));
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }
        return redirect()->route('documents.show', $document)->with('status', 'Material publicado correctamente.');
    }

    public function show(Request $request, Document $document)
    {
        abort_unless($document->status === 'published' || ($request->user() && $request->user()->is_active && ($document->user_id === $request->user()->id || $request->user()->role === 'admin')), 404);
        abort_unless($request->user()?->is_active, 403);
        $document->load(['user', 'academicArea', 'documentType', 'comments.user']);
        DocumentView::create(['user_id' => $request->user()->id, 'document_id' => $document->id]);
        $document->increment('views_count');
        $isFavorite = Favorite::where('user_id', $request->user()->id)->where('document_id', $document->id)->exists();
        $myRating = Rating::where('user_id', $request->user()->id)->where('document_id', $document->id)->first();
        $averageRating = Rating::where('document_id', $document->id)->avg('score');
        return view('documents.live_show', compact('document', 'isFavorite', 'myRating', 'averageRating'));
    }

    public function file(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active, 403);
        abort_unless($document->status === 'published' || $document->user_id === $request->user()->id || $request->user()->role === 'admin', 404);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        DocumentDownload::create(['user_id' => $request->user()->id, 'document_id' => $document->id]);
        return Storage::disk('local')->download($document->file_path, $document->original_filename);
    }

    public function preview(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active, 403);
        abort_unless($document->status === 'published' || $document->user_id === $request->user()->id || $request->user()->role === 'admin', 404);
        abort_unless($document->mime_type === 'application/pdf' || strtolower(pathinfo($document->original_filename, PATHINFO_EXTENSION)) === 'pdf', 415);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        return response()->file(Storage::disk('local')->path($document->file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function edit(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && ($document->user_id === $request->user()->id || $request->user()->role === 'admin'), 403);
        return view('documents.live_edit', compact('document') + [
            'areas' => AcademicArea::where('is_active', true)->orderBy('name')->get(),
            'types' => DocumentType::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && ($document->user_id === $request->user()->id || $request->user()->role === 'admin'), 403);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:3000'],
            'academic_area_id' => ['required', Rule::exists('academic_areas', 'id')->where('is_active', true)],
            'document_type_id' => ['required', Rule::exists('document_types', 'id')->where('is_active', true)],
            'topic' => ['required', 'string', 'max:160'],
            'academic_cycle' => ['required', 'string', 'max:30'],
        ]);
        $document->update($data);
        return redirect()->route('documents.show', $document)->with('status', 'Material actualizado.');
    }

    public function favorites(Request $request)
    {
        abort_unless($request->user()->is_active, 403);
        $favorites = Favorite::with('document.academicArea', 'document.documentType')
            ->where('user_id', $request->user()->id)->latest()->get();
        return view('documents.live_favorites', compact('favorites'));
    }

    public function toggleFavorite(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && $document->status === 'published', 403);
        $favorite = Favorite::where('user_id', $request->user()->id)->where('document_id', $document->id)->first();
        if ($favorite) {
            $favorite->delete();
            return back()->with('status', 'Material eliminado de favoritos.');
        }
        Favorite::firstOrCreate(['user_id' => $request->user()->id, 'document_id' => $document->id]);
        return back()->with('status', 'Material guardado en favoritos.');
    }

    public function report(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && $document->status === 'published', 403);
        $data = $request->validate([
            'reason' => ['required', Rule::in(['Contenido inapropiado', 'Derechos de autor', 'Clasificación incorrecta', 'Otro'])],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);
        Report::create($data + ['user_id' => $request->user()->id, 'document_id' => $document->id, 'status' => 'pending']);
        return back()->with('status', 'Reporte enviado para revisión.');
    }

    public function comment(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && $document->status === 'published', 403);
        $data = $request->validate(['body' => ['required', 'string', 'min:2', 'max:2000']]);
        Comment::create($data + ['user_id' => $request->user()->id, 'document_id' => $document->id, 'is_visible' => true]);
        return back()->with('status', 'Comentario publicado.');
    }

    public function rate(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && $document->status === 'published', 403);
        $data = $request->validate(['score' => ['required', 'integer', 'between:1,5']]);
        Rating::updateOrCreate(['user_id' => $request->user()->id, 'document_id' => $document->id], $data);
        return back()->with('status', 'Calificación guardada.');
    }

    public function destroy(Request $request, Document $document)
    {
        abort_unless($request->user()->is_active && ($document->user_id === $request->user()->id || $request->user()->role === 'admin'), 403);
        $document->delete();
        return redirect()->route('documents.index')->with('status', 'Publicación retirada.');
    }
}
