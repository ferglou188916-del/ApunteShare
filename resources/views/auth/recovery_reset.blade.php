@extends('layouts.app')
@section('title', 'Nueva contraseña')
@section('content')
<div class="container py-5"><div class="auth-card mx-auto" style="max-width:520px">
<h1 class="h3 mb-3">Establecer nueva contraseña</h1>
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('password.update') }}">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label class="form-label" for="reset-email">Correo electrónico</label><input class="form-control mb-3" id="reset-email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email">
<label class="form-label" for="reset-password">Nueva contraseña</label><input class="form-control mb-3" id="reset-password" name="password" type="password" minlength="8" required autocomplete="new-password">
<label class="form-label" for="reset-confirm">Confirmar contraseña</label><input class="form-control mb-3" id="reset-confirm" name="password_confirmation" type="password" minlength="8" required autocomplete="new-password">
<button class="btn btn-brand w-100" type="submit">Actualizar contraseña</button>
</form></div></div>
@endsection
