@extends('layouts.app')
@section('title', 'Administrar reportes')
@section('content')
<section class="container page py-5"><h1 class="page-title">Gestión de reportes</h1><p class="lead-muted">Revisión y resolución de reportes académicos.</p>@include('admin.live.nav')
<form method="GET" class="d-flex gap-2 mb-3"><select name="status" class="form-select" style="max-width:250px"><option value="">Todos</option>@foreach(['pending', 'reviewed', 'dismissed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>@endforeach</select><button class="btn btn-brand">Filtrar</button></form>
@forelse($reports as $report)<div class="panel mb-3"><div class="d-flex justify-content-between flex-wrap gap-2"><strong>{{ $report->document?->title ?? 'Publicación no disponible' }}</strong><span>{{ $report->status }}</span></div><div class="small text-secondary">Reportó: {{ $report->user?->name }} · {{ $report->created_at?->format('d/m/Y H:i') }}</div><p class="mt-2 mb-1"><strong>Motivo:</strong> {{ $report->reason }}</p><p>{{ $report->details }}</p>
@if($report->status === 'pending')<form method="POST" action="{{ route('admin.reports.resolve', $report) }}">@csrf @method('PATCH')<textarea class="form-control mb-2" name="resolution_notes" placeholder="Resolución del reporte" required maxlength="2000"></textarea><div class="d-flex flex-wrap gap-2 align-items-center"><select name="status" class="form-select" style="max-width:190px" required><option value="reviewed">Revisado</option><option value="dismissed">Descartado</option></select><label class="small"><input type="checkbox" name="hide_document" value="1"> Ocultar publicación si se marca como revisado</label><button class="btn btn-brand btn-sm">Guardar resolución</button></div></form>
@else<p class="small"><strong>Resolución:</strong> {{ $report->resolution_notes }} · {{ $report->reviewer?->name }}</p>@endif
<form method="POST" action="{{ route('admin.reports.delete', $report) }}" class="mt-3" onsubmit="return confirm('Eliminar este reporte definitivamente?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar reporte</button></form>
</div>@empty<div class="panel">No hay reportes.</div>@endforelse
<div class="mt-3">{{ $reports->links() }}</div></section>
@endsection
