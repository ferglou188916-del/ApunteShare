@extends('layouts.app')
@section('title', 'Iniciar sesión')
@section('content')
<div class="container py-5"><div class="auth-wrap">
  <div class="auth-copy"><div class="eyebrow">COMUNIDAD UDB</div><h1>Únete a la comunidad estudiantil que comparte conocimiento</h1><p>Comparte tus mejores apuntes, accede a otros recursos y organiza tu material de estudio.</p><div class="checkline">✓ Material organizado y disponible</div><div class="checkline">✓ Acceso gratuito a recursos académicos</div><div class="checkline">✓ Plataforma colaborativa</div></div>
  <div class="auth-card"><h2 class="h4 text-center mb-4">Iniciar sesión</h2>
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('apunteshare.login.submit') }}">@csrf
      <div class="mb-3"><label for="email" class="form-label">Correo electrónico</label><input id="email" name="email" type="email" class="form-control" autocomplete="email" value="{{ old('email') }}" required></div>
      <div class="mb-3"><label for="password" class="form-label">Contraseña</label><input id="password" name="password" type="password" class="form-control" autocomplete="current-password" required></div>
      <div class="mb-3"><label class="small"><input name="remember" type="checkbox" value="1"> Recordarme</label></div>
      <button class="btn btn-brand w-100" type="submit">Iniciar sesión</button>
    </form>
    <div class="small text-center mt-3">¿No tienes cuenta? <a class="link-brand" href="{{ route('apunteshare.register') }}">Regístrate</a></div>
    <p class="small text-muted text-center mt-2"><a class="link-brand" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
  </div>
</div></div>
@endsection
