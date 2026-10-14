@extends('layouts.app')
@section('title','Recuperar contraseña')
@section('content')
<section class="container page py-5"><div class="eyebrow">COMUNIDAD ACADÉMICA</div><h1 class="page-title">Recuperar contraseña</h1><p class="lead-muted">Ingresa tu correo y recibirás las instrucciones cuando se conecte el backend.</p><div class="panel mx-auto" style="max-width:500px"><form class="demo-form"><label class="form-label">Correo electrónico</label><input class="form-control mb-3" type="email" required placeholder="correo@ejemplo.com"><button class="btn btn-brand w-100">Solicitar recuperación</button></form><a href="{{ route('demo.login') }}" class="small link-brand d-block mt-3">← Volver al inicio de sesión</a></div></section>
@endsection
