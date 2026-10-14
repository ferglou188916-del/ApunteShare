@extends('layouts.app')
@section('title', 'Recuperar contraseña')
@section('content')
<div class="container py-5"><div class="auth-card mx-auto" style="max-width:520px">
<h1 class="h3 mb-3">Recuperar contraseña</h1>
<p class="text-secondary">Escribe tu correo para generar un enlace de recuperación.</p>
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('password.email') }}">@csrf
<label class="form-label" for="recovery-email">Correo electrónico</label>
<input id="recovery-email" class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
<button class="btn btn-brand w-100" type="submit">Solicitar enlace</button>
</form><p class="small mt-3 mb-0"><a class="link-brand" href="{{ route('login') }}">Volver a iniciar sesión</a></p>
</div></div>
@endsection
