@extends('layouts.app')
@section('title', 'Administración')
@section('content')
<section class="container page py-5"><div class="eyebrow">COMUNIDAD ACADÉMICA</div><h1 class="page-title">Panel de administración</h1><p class="lead-muted">Información actualizada desde la base de datos.</p>
@include('admin.live.nav')
<div class="row g-3 mb-4">
@foreach([['Usuarios', $usersCount], ['Documentos', $documentsCount], ['Visualizaciones', $viewsCount], ['Descargas', $downloadsCount], ['Reportes pendientes', $pendingCount], ['Almacenamiento registrado', number_format($storageBytes / 1048576, 2).' MB']] as $metric)
<div class="col-md-4"><div class="stat-card h-100"><div class="text-secondary">{{ $metric[0] }}</div><div class="stat-value">{{ $metric[1] }}</div></div></div>
@endforeach
</div><div class="panel"><h2 class="h5">Reportes recientes</h2><div class="table-responsive"><table class="table"><thead><tr><th>Material</th><th>Motivo</th><th>Estado</th></tr></thead><tbody>
@forelse($recentReports as $report)<tr><td>{{ $report->document?->title ?? 'Publicación no disponible' }}</td><td>{{ $report->reason }}</td><td>{{ $report->status }}</td></tr>@empty<tr><td colspan="3">No hay reportes todavía.</td></tr>@endforelse
</tbody></table></div><a href="{{ route('admin.reports') }}" class="btn btn-brand btn-sm">Gestionar reportes</a></div>
<p class="small text-secondary mt-3">El almacenamiento corresponde a la suma de tamaños registrados en la base de datos, no a una medición del espacio total del disco.</p>
</section>
@endsection
