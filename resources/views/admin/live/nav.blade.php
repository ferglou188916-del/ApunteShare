<div class="d-flex flex-wrap gap-2 mb-4">
<a class="btn btn-outline-brand btn-sm" href="{{ route('apunteshare.admin') }}">Panel</a>
<a class="btn btn-outline-brand btn-sm" href="{{ route('admin.users') }}">Usuarios</a>
<a class="btn btn-outline-brand btn-sm" href="{{ route('admin.documents') }}">Publicaciones</a>
<a class="btn btn-outline-brand btn-sm" href="{{ route('admin.reports') }}">Reportes</a>
<a class="btn btn-outline-brand btn-sm" href="{{ route('admin.categories') }}">Categorías</a>
<a class="btn btn-outline-brand btn-sm" href="{{ route('admin.comments') }}">Comentarios</a>
</div>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
