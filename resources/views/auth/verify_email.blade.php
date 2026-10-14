@extends('layouts.app')
@section('title', 'Verificar correo electrónico')
@section('content')
<div class="container py-5"><div class="auth-card mx-auto" style="max-width:560px">
<h1 class="h3 mb-3">Verifica tu correo electrónico</h1>
<p>Enviamos un enlace a <strong>{{ auth()->user()->email }}</strong>. Abre el mensaje y confirma tu dirección para utilizar ApunteShare.</p>
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
<p class="small text-secondary">El enlace es temporal. Si no encuentras el mensaje, revisa la carpeta de spam.</p>
<form method="POST" action="{{ route('verification.send') }}">@csrf<button class="btn btn-brand w-100" type="submit">Reenviar correo de verificación</button></form>
<form method="POST" action="{{ route('apunteshare.logout') }}" class="mt-3">@csrf<button class="btn btn-outline-secondary w-100" type="submit">Cerrar sesión</button></form>
</div></div>
@endsection
