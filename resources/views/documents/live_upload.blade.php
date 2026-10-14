@extends('layouts.app')
@section('title', 'Compartir material')
@section('content')
<section class="container page py-5">
    <div class="eyebrow">COMUNIDAD ACADÉMICA</div>
    <h1 class="page-title">Publicar nuevo material académico</h1>
    <p class="lead-muted">Comparte apuntes de elaboración propia y ayuda a otros estudiantes.</p>
    <div class="panel mx-auto" style="max-width:790px">
        @if ($errors->any())<div class="alert alert-danger"><strong>Revisa los datos:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="upload-box mb-4"><div style="font-size:2rem">↑</div><strong>Selecciona tu archivo</strong><div class="small text-secondary mb-2">PDF o DOCX · máximo 10 MB</div><input class="form-control" name="file" type="file" accept=".pdf,.docx" required></div>
            <div class="row g-3">
                <div class="col-12"><label class="form-label" for="title">Título del material</label><input id="title" name="title" class="form-control" maxlength="200" value="{{ old('title') }}" required></div>
                <div class="col-12"><label class="form-label" for="description">Descripción</label><textarea id="description" name="description" class="form-control" rows="3" maxlength="3000">{{ old('description') }}</textarea></div>
                <div class="col-md-6"><label class="form-label" for="academic_area_id">Área de estudio</label><select name="academic_area_id" id="academic_area_id" class="form-select" required><option value="">Seleccionar área</option>@foreach($areas as $area)<option value="{{ $area->id }}" @selected(old('academic_area_id') == $area->id)>{{ $area->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label" for="document_type_id">Tipo de material</label><select name="document_type_id" id="document_type_id" class="form-select" required><option value="">Seleccionar tipo</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected(old('document_type_id') == $type->id)>{{ $type->name }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label" for="topic">Asignatura o tema</label><input id="topic" name="topic" class="form-control" maxlength="160" required value="{{ old('topic') }}"></div>
                <div class="col-md-6"><label class="form-label" for="academic_cycle">Ciclo</label><select id="academic_cycle" name="academic_cycle" class="form-select" required><option value="02-2026" @selected(old('academic_cycle')==='02-2026')>02-2026</option><option value="01-2026" @selected(old('academic_cycle')==='01-2026')>01-2026</option></select></div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-outline-brand" href="{{ route('documents.index') }}">Cancelar</a><button type="submit" class="btn btn-brand">Publicar material</button></div>
        </form>
    </div>
</section>
@endsection
