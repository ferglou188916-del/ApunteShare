@extends('layouts.app')
@section('title', 'Registrarse')
@section('content')
<div class="container py-5"><div class="auth-wrap">
  <div class="auth-copy"><div class="eyebrow">COMUNIDAD UDB</div><h1>Aprende y comparte junto a otros estudiantes</h1><p>Regístrate para acceder al repositorio académico y guardar tus materiales favoritos.</p><div class="checkline">✓ Recursos clasificados</div><div class="checkline">✓ Publicaciones de estudiantes</div><div class="checkline">✓ Acceso desde cualquier dispositivo</div></div>
  <div class="auth-card"><h2 class="h4 text-center mb-4">Crear cuenta</h2>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('apunteshare.register.submit') }}">@csrf
      <label for="name" class="form-label">Nombre completo</label><input id="name" name="name" class="form-control mb-3" value="{{ old('name') }}" required maxlength="255" autocomplete="name">
      <label for="email" class="form-label">Correo electrónico</label><input id="email" name="email" type="email" class="form-control mb-3" value="{{ old('email') }}" required autocomplete="email">
      <label for="role" class="form-label">Tipo de cuenta</label><select id="role" name="role" class="form-select mb-3" required><option value="reader" @selected(old('role') === 'reader')>Estudiante que consulta</option><option value="contributor" @selected(old('role') === 'contributor')>Estudiante colaborador</option></select>
      <label for="password" class="form-label">Contraseña</label><input id="password" name="password" type="password" class="form-control mb-3" required minlength="8" autocomplete="new-password">
      <label for="password_confirmation" class="form-label">Confirmar contraseña</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control mb-3" required minlength="8" autocomplete="new-password">
      <button class="btn btn-brand w-100" type="submit">Crear cuenta</button>
    </form>
    <div class="small text-center mt-3">¿Ya tienes cuenta? <a class="link-brand" href="{{ route('login') }}">Inicia sesión</a></div>
  </div>
</div></div>
@endsection
