@extends('layouts.app')
@section('title', 'Administrar usuarios')
@section('content')
<section class="container page py-5"><h1 class="page-title">Gestión de usuarios</h1><p class="lead-muted">Suspender o reactivar cuentas de estudiantes.</p>@include('admin.live.nav')
<form method="GET" class="d-flex gap-2 mb-3"><input class="form-control" name="q" placeholder="Buscar por nombre o correo" value="{{ request('q') }}"><button class="btn btn-brand">Buscar</button></form>
<div class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
@forelse($users as $user)<tr><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->role }}</td><td>{{ $user->is_active ? 'Activo' : 'Suspendido' }}</td><td>
@if($user->role !== 'admin')<form method="POST" action="{{ route('admin.users.toggle', $user) }}" onsubmit="return confirm('¿Confirmas el cambio de estado de esta cuenta?')">@csrf @method('PATCH')<button class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-brand' }}">{{ $user->is_active ? 'Suspender' : 'Reactivar' }}</button></form>@else<span class="small text-secondary">Protegido</span>@endif
@if($user->role !== 'admin')
<form method="POST" action="{{ route('admin.users.delete', $user) }}" class="mt-2" onsubmit="return confirm('Eliminar definitivamente esta cuenta? Esta acción no se puede deshacer y solo se admite si no tiene actividad relacionada.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
@endif
</td></tr>@empty<tr><td colspan="5">Sin usuarios.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $users->links() }}</div></section>
@endsection
