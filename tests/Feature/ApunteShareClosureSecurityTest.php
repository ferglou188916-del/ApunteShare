<?php

namespace Tests\Feature;

use App\Models\AcademicArea;
use App\Models\Comment;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Favorite;
use App\Models\Rating;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApunteShareClosureSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_home_but_not_private_catalog(): void
    {
        $this->get('/')->assertOk();
        $this->get('/materiales')->assertRedirect('/login');
    }

    public function test_suspended_account_cannot_log_in(): void
    {
        $user = User::factory()->create(['role' => 'reader', 'is_active' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_existing_session_is_revoked_when_account_is_suspended(): void
    {
        $user = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        $this->actingAs($user)->get('/mi-perfil')->assertOk();

        $user->forceFill(['is_active' => false])->save();

        $this->get('/mi-perfil')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_suspended_api_request_returns_forbidden_and_revokes_session(): void
    {
        $user = User::factory()->create(['role' => 'reader', 'is_active' => false]);

        $this->actingAs($user)->getJson('/mi-perfil')
            ->assertForbidden()
            ->assertJsonPath('message', 'La cuenta se encuentra suspendida.');

        $this->assertGuest();
    }

    public function test_public_registration_cannot_create_an_admin(): void
    {
        $this->post('/registro', [
            'name' => 'Prueba Registro',
            'email' => 'registro-ejemplo@example.test',
            'password' => 'clave-segura-123',
            'password_confirmation' => 'clave-segura-123',
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'registro-ejemplo@example.test']);
    }

    public function test_regular_users_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($user)->get('/panel-admin')->assertForbidden();
    }

    public function test_reader_cannot_publish_documents(): void
    {
        $user = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($user)->get('/compartir-material')->assertForbidden();
        $this->post('/compartir-material', [])->assertForbidden();
    }

    public function test_contributor_cannot_upload_invalid_file_or_more_than_ten_megabytes(): void
    {
        $user = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        [$area, $type] = $this->catalog();
        $attributes = [
            'title' => 'Documento de prueba',
            'description' => 'Comprobación de validaciones',
            'academic_area_id' => $area->id,
            'document_type_id' => $type->id,
            'topic' => 'Pruebas',
            'academic_cycle' => '02-2026',
        ];

        $this->actingAs($user)->post('/compartir-material', $attributes + [
            'file' => UploadedFile::fake()->create('invalido.exe', 10, 'application/octet-stream'),
        ])->assertSessionHasErrors('file');

        $this->post('/compartir-material', $attributes + [
            'file' => UploadedFile::fake()->create('demasiado-grande.pdf', 10241, 'application/pdf'),
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseCount('documents', 0);
    }

    public function test_student_cannot_edit_or_delete_another_students_document(): void
    {
        $owner = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        $other = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        $document = $this->publication($owner);

        $this->actingAs($other)->get("/materiales/{$document->id}/editar")->assertForbidden();
        $this->put("/materiales/{$document->id}", [])->assertForbidden();
        $this->delete("/materiales/{$document->id}")->assertForbidden();

        $this->assertDatabaseHas('documents', ['id' => $document->id, 'deleted_at' => null]);
    }

    public function test_hidden_document_is_not_readable_or_downloadable_by_another_user(): void
    {
        $owner = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        $reader = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $document = $this->publication($owner, 'hidden');

        $this->actingAs($reader)->get("/materiales/{$document->id}")->assertNotFound();
        $this->get("/materiales/{$document->id}/descargar")->assertNotFound();
        $this->get("/materiales/{$document->id}/vista-previa")->assertNotFound();
    }

    public function test_reader_can_interact_with_a_published_document(): void
    {
        $owner = User::factory()->create(['role' => 'contributor', 'is_active' => true]);
        $reader = User::factory()->create(['role' => 'reader', 'is_active' => true]);
        $document = $this->publication($owner);

        $this->actingAs($reader)->post("/materiales/{$document->id}/favorito")->assertRedirect();
        $this->post("/materiales/{$document->id}/comentar", ['body' => 'Material útil'])->assertRedirect();
        $this->post("/materiales/{$document->id}/calificar", ['score' => 5])->assertRedirect();
        $this->post("/materiales/{$document->id}/reportar", [
            'reason' => 'Clasificación incorrecta',
            'details' => 'La categoría debe revisarse.',
        ])->assertRedirect();

        $this->assertTrue(Favorite::where('user_id', $reader->id)->where('document_id', $document->id)->exists());
        $this->assertTrue(Comment::where('user_id', $reader->id)->where('document_id', $document->id)->exists());
        $this->assertTrue(Rating::where('user_id', $reader->id)->where('score', 5)->exists());
        $this->assertTrue(Report::where('user_id', $reader->id)->where('status', 'pending')->exists());
    }

    private function catalog(): array
    {
        $area = AcademicArea::firstOrCreate(
            ['name' => 'Ingeniería y Tecnología'],
            ['slug' => 'ingenieria-y-tecnologia', 'is_active' => true]
        );
        $type = DocumentType::firstOrCreate(['name' => 'Apuntes'], ['is_active' => true]);

        return [$area, $type];
    }

    private function publication(User $owner, string $status = 'published'): Document
    {
        [$area, $type] = $this->catalog();

        return Document::create([
            'user_id' => $owner->id,
            'academic_area_id' => $area->id,
            'document_type_id' => $type->id,
            'title' => 'Apuntes de prueba',
            'description' => 'Publicación de pruebas automatizadas',
            'university' => 'Universidad Don Bosco',
            'academic_cycle' => '02-2026',
            'topic' => 'Matemáticas',
            'original_filename' => 'apuntes.pdf',
            'file_path' => 'academic-documents/apuntes.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'status' => $status,
            'views_count' => 0,
        ]);
    }
}
