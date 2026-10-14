@extends('layouts.app')
@section('title', 'Administrar categorías')
@section('content')
<section class="container page py-5"><h1 class="page-title">Gestión de categorías</h1><p class="lead-muted">Las categorías desactivadas dejan de estar disponibles al publicar.</p>@include('admin.live.nav')
<div class="panel mb-3"><h2 class="h5">Agregar categoría</h2><form method="POST" action="{{ route('admin.categories.add') }}" class="d-flex flex-wrap gap-2">@csrf<select name="kind" class="form-select" style="max-width:180px"><option value="area">Área académica</option><option value="type">Tipo de material</option></select><input class="form-control" style="max-width:300px" name="name" maxlength="100" placeholder="Nombre de categoría" required><button class="btn btn-brand">Agregar</button></form></div>
@foreach([['Áreas académicas', 'area', $areas], ['Tipos de documento', 'type', $types]] as $section)
<div class="panel mb-3"><h2 class="h5">{{ $section[0] }}</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Documentos</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
@forelse($section[2] as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->documents_count }}</td><td>{{ $category->is_active ? 'Activa' : 'Inactiva' }}</td><td><form method="POST" action="{{ route('admin.categories.toggle', [$section[1], $category->id]) }}">@csrf @method('PATCH')<button class="btn btn-outline-brand btn-sm">{{ $category->is_active ? 'Desactivar' : 'Activar' }}</button></form><form method="POST" action="{{ route('admin.categories.delete', [$section[1], $category->id]) }}" class="mt-2" onsubmit="return confirm('Eliminar esta categoría definitivamente? Solo se permite si no tiene publicaciones o asignaturas asociadas.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>@empty<tr><td colspan="4">Sin categorías.</td></tr>@endforelse
</tbody></table></div></div>@endforeach
</section>
@endsection
