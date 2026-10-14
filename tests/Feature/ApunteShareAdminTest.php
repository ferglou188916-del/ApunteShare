<?php

namespace Tests\Feature;

use App\Models\AcademicArea;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\Document;
use App\Models\Report;
use App\Models\Comment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApunteShareAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_cannot_access_admin_sections(): void
    {
        $reader = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($reader)->get('/panel-admin/usuarios')->assertForbidden();
        $this->actingAs($reader)->get('/panel-admin/reportes')->assertForbidden();
    }

    public function test_admin_can_view_dashboard_and_manage_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $reader = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($admin)->get('/panel-admin')->assertOk();
        $this->actingAs($admin)->get('/panel-admin/usuarios')->assertOk();
        $this->actingAs($admin)->patch('/panel-admin/usuarios/'.$reader->id.'/estado')->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $reader->id, 'is_active' => false]);
    }

    public function test_admin_cannot_suspend_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->patch('/panel-admin/usuarios/'.$admin->id.'/estado')->assertStatus(422);
    }

    public function test_admin_can_manage_catalog_categories(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->actingAs($admin)->post('/panel-admin/categorias', ['kind' => 'type', 'name' => 'Formulario'])->assertRedirect();
        $type = DocumentType::where('name', 'Formulario')->firstOrFail();
        $this->actingAs($admin)->patch('/panel-admin/categorias/type/'.$type->id)->assertRedirect();
        $this->assertDatabaseHas('document_types', ['id' => $type->id, 'is_active' => false]);
    }

    public function test_admin_can_remove_comment_and_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $area = AcademicArea::create(['name' => 'Pruebas', 'slug' => 'pruebas', 'is_active' => true]);
        $type = DocumentType::create(['name' => 'Prueba', 'is_active' => true]);
        $document = Document::create([
            'user_id' => $student->id, 'academic_area_id' => $area->id,
            'document_type_id' => $type->id, 'title' => 'Documento', 'topic' => 'Tema',
            'original_filename' => 'test.pdf', 'file_path' => 'academic-documents/test.pdf',
            'mime_type' => 'application/pdf', 'file_size' => 100, 'status' => 'published',
        ]);
        $comment = Comment::create(['user_id' => $student->id, 'document_id' => $document->id, 'body' => 'Comentario', 'is_visible' => true]);
        $report = Report::create(['user_id' => $student->id, 'document_id' => $document->id, 'reason' => 'Otro', 'status' => 'pending']);
        $this->actingAs($admin)->delete(route('admin.comments.delete', $comment))->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
        $this->actingAs($admin)->delete(route('admin.reports.delete', $report))->assertRedirect();
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    public function test_admin_document_deletion_cleans_storage(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $area = AcademicArea::create(['name' => 'Pruebas', 'slug' => 'pruebas', 'is_active' => true]);
        $type = DocumentType::create(['name' => 'Prueba', 'is_active' => true]);
        Storage::disk('local')->put('academic-documents/prueba.pdf', 'contenido');
        $document = Document::create([
            'user_id' => $student->id, 'academic_area_id' => $area->id,
            'document_type_id' => $type->id, 'title' => 'Documento', 'topic' => 'Tema',
            'original_filename' => 'prueba.pdf', 'file_path' => 'academic-documents/prueba.pdf',
            'mime_type' => 'application/pdf', 'file_size' => 9, 'status' => 'published',
        ]);
        $this->actingAs($admin)->delete(route('admin.documents.delete', $document))->assertRedirect();
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing('academic-documents/prueba.pdf');
    }

    public function test_category_with_document_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $area = AcademicArea::create(['name' => 'Pruebas', 'slug' => 'pruebas', 'is_active' => true]);
        $type = DocumentType::create(['name' => 'Prueba', 'is_active' => true]);
        Document::create([
            'user_id' => $student->id, 'academic_area_id' => $area->id,
            'document_type_id' => $type->id, 'title' => 'Documento', 'topic' => 'Tema',
            'original_filename' => 'prueba.pdf', 'file_path' => 'academic-documents/prueba.pdf',
            'mime_type' => 'application/pdf', 'file_size' => 9, 'status' => 'published',
        ]);
        $this->actingAs($admin)->delete(route('admin.categories.delete', ['area', $area->id]))->assertSessionHasErrors('delete');
        $this->assertDatabaseHas('academic_areas', ['id' => $area->id]);
    }

    public function test_cannot_delete_admin_account_and_can_delete_empty_student(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $student = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($admin)->delete(route('admin.users.delete', $admin))->assertStatus(422);
        $this->actingAs($admin)->delete(route('admin.users.delete', $student))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }
}
