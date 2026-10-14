@extends('layouts.app')
@section('title', 'Moderar comentarios')
@section('content')
<section class="container page py-5"><h1 class="page-title">Moderación de comentarios</h1>@include('admin.live.nav')<div class="panel table-responsive"><table class="table align-middle"><thead><tr><th>Autor</th><th>Material</th><th>Comentario</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
@forelse($comments as $comment)<tr><td>{{ $comment->user?->name }}</td><td>{{ $comment->document?->title }}</td><td>{{ $comment->body }}</td><td>{{ $comment->is_visible ? 'Visible' : 'Oculto' }}</td><td><form method="POST" action="{{ route('admin.comments.toggle', $comment) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-brand">{{ $comment->is_visible ? 'Ocultar' : 'Mostrar' }}</button></form><form method="POST" action="{{ route('admin.comments.delete', $comment) }}" class="mt-2" onsubmit="return confirm('Eliminar este comentario definitivamente?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>@empty<tr><td colspan="5">No hay comentarios.</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $comments->links() }}</div></section>
@endsection
