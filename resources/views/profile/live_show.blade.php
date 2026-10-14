@extends('layouts.app')
@section('title', 'Mi perfil')
@section('content')
<div class="container py-5">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div><span class="text-uppercase text-info fw-bold small">Mi actividad académica</span><h1 class="mb-1">Mi perfil</h1><div class="text-secondary">Consulta tus aportes y movimientos en ApunteShare.</div></div>
    <span class="badge bg-light text-dark border p-3">{{ ['reader' => 'Estudiante que consulta', 'contributor' => 'Estudiante colaborador', 'admin' => 'Administrador'][auth()->user()->role] ?? 'Cuenta' }}</span>
  </div>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
  @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
  <div class="row g-3 mb-4">
    @foreach([['Publicaciones', $publications->count()],['Descargas registradas', $downloads->count()],['Favoritos', $favorites->count()],['Reportes enviados', $reports->count()]] as $stat)
      <div class="col-6 col-lg-3"><div class="material-card h-100"><div class="fs-2 fw-bold" style="color:#7144b5">{{ $stat[1] }}</div><div class="text-secondary small">{{ $stat[0] }}</div></div></div>
    @endforeach
  </div>
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="auth-card mb-4" id="publicaciones"><h2 class="h4 mb-3">Mis publicaciones</h2>
        @forelse($publications as $document)
          <div class="border-bottom py-3 d-flex justify-content-between flex-wrap gap-2"><div><div class="fw-semibold">{{ $document->title }}</div><div class="small text-secondary">{{ $document->created_at->format('d/m/Y') }} · {{ $document->academic_cycle }} · {{ $document->status }} · {{ $document->views_count }} visualizaciones</div></div><a class="link-brand" href="{{ route('documents.show', $document) }}">Ver material →</a></div>
        @empty <p class="text-secondary mb-0">Todavía no has publicado materiales.</p> @endforelse
      </div>
      <div class="auth-card mb-4" id="descargas"><h2 class="h4 mb-3">Mi historial de descargas</h2><div class="small text-secondary mb-2">Se registran las descargas realizadas desde esta actualización.</div>
        @forelse($downloads as $download)
          <div class="border-bottom py-3"><div class="fw-semibold">{{ $download->document?->title ?? 'Material no disponible' }}</div><div class="small text-secondary">{{ $download->created_at->format('d/m/Y H:i') }}</div></div>
        @empty <p class="text-secondary mb-0">No hay descargas registradas todavía.</p> @endforelse
      </div>
      <div class="auth-card mb-4" id="favoritos"><h2 class="h4 mb-3">Materiales guardados</h2>
        @forelse($favorites as $favorite)
          @if($favorite->document)<div class="border-bottom py-3 d-flex justify-content-between flex-wrap gap-2"><span>{{ $favorite->document->title }}</span><a class="link-brand" href="{{ route('documents.show', $favorite->document) }}">Abrir →</a></div>@endif
        @empty <p class="text-secondary mb-0">Todavía no tienes materiales guardados.</p> @endforelse
      </div>
      <div class="auth-card" id="reportes"><h2 class="h4 mb-3">Reportes enviados</h2>
        @forelse($reports as $report)
          <div class="border-bottom py-3"><div class="fw-semibold">{{ $report->document?->title ?? 'Material no disponible' }}</div><div class="small text-secondary">{{ $report->reason }} · {{ $report->status }} · {{ $report->created_at->format('d/m/Y H:i') }}</div></div>
        @empty <p class="text-secondary mb-0">Todavía no has enviado reportes.</p> @endforelse
      </div>
    </div>
    <div class="col-lg-4"><div class="auth-card"><h2 class="h4 mb-3">Información personal</h2>
      <form method="POST" action="{{ route('apunteshare.profile.update') }}">@csrf @method('PUT')
        <label for="name" class="form-label">Nombre completo</label><input id="name" name="name" class="form-control mb-3" required maxlength="255" value="{{ old('name', auth()->user()->name) }}">
        <label for="email" class="form-label">Correo electrónico</label><input id="email" name="email" type="email" class="form-control mb-3" required maxlength="255" value="{{ old('email', auth()->user()->email) }}">
        <button class="btn btn-brand w-100" type="submit">Guardar cambios</button>
      </form></div></div>
  </div>
</div>
@endsection
