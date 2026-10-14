<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ApunteShareEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_receives_verification_and_cannot_access_catalog(): void
    {
        Notification::fake();
        $this->post('/registro', [
            'name' => 'Estudiante Nuevo',
            'email' => 'nuevo@ejemplo.test',
            'role' => 'reader',
            'password' => 'Contrasena123!',
            'password_confirmation' => 'Contrasena123!',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'nuevo@ejemplo.test')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->get('/materiales')->assertRedirect(route('verification.notice'));
    }

    public function test_valid_verification_link_enables_catalog(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'reader', 'is_active' => true]);
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);
        $this->actingAs($user)->get($url)->assertRedirect(route('documents.index'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->get('/materiales')->assertOk();
    }

    public function test_invalid_verification_link_is_rejected(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'reader', 'is_active' => true]);
        $this->actingAs($user)->get('/verificar-correo/'.$user->id.'/incorrecto')->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_password_recovery_sends_notification_for_registered_address(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'recuperar@ejemplo.test', 'is_active' => true]);
        $this->post('/olvide-contrasena', ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }
}
