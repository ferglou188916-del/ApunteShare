@extends('layouts.app')
@section('title', $document->title)
@section('content')
<section class="container page py-5">
<a class="link-brand" href="{{ route('documents.index') }}">← Volver al catálogo</a>
@if(session('status'))<div class="alert alert-success mt-3">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger mt-3"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="row g-4 mt-1"><div class="col-lg-8">
<div class="panel"><div class="eyebrow">MATERIAL ACADÉMICO</div><h1 class="page-title">{{ $document->title }}</h1><p class="lead-muted">{{ $document->description }}</p>
@if(strtolower(pathinfo($document->original_filename, PATHINFO_EXTENSION)) === 'pdf')
<div class="border rounded p-2 mt-3" style="background:#f7f7fb"><canvas id="apunteshare-pdf" style="max-width:100%;display:block;margin:auto"></canvas><div id="pdf-state" class="text-secondary small mt-2">Cargando vista previa PDF…</div><div class="d-flex align-items-center gap-2 mt-2"><button type="button" id="pdf-prev" class="btn btn-outline-brand btn-sm">Anterior</button><span id="pdf-page" class="small">Página 1</span><button type="button" id="pdf-next" class="btn btn-outline-brand btn-sm">Siguiente</button></div></div>
<script type="module">
import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';
const canvas=document.getElementById('apunteshare-pdf'), status=document.getElementById('pdf-state');
let pdf=null,page=1,busy=false;
async function render(){if(!pdf||busy)return;busy=true;try{const p=await pdf.getPage(page),viewport=p.getViewport({scale:1.2});const context=canvas.getContext('2d');canvas.height=viewport.height;canvas.width=viewport.width;await p.render({canvasContext:context,viewport}).promise;document.getElementById('pdf-page').textContent=`Página ${page} de ${pdf.numPages}`;}finally{busy=false;}}
document.getElementById('pdf-prev').onclick=async()=>{if(page>1){page--;await render();}};
document.getElementById('pdf-next').onclick=async()=>{if(pdf&&page<pdf.numPages){page++;await render();}};
try{pdf=await pdfjsLib.getDocument({url:@json(route('documents.preview',$document)),withCredentials:true}).promise;status.textContent='Vista previa del documento';await render();}catch(e){status.textContent='No se pudo mostrar el PDF. Puedes descargar el archivo.';}
</script>
@else<div class="alert alert-light border mt-3">La vista previa interactiva está disponible para archivos PDF. Descarga el DOCX para abrirlo en tu dispositivo.</div>@endif
<div class="mt-3"><strong>Comentarios</strong><div class="mt-2">@forelse($document->comments->where('is_visible', true) as $comment)<div class="border-bottom py-2"><strong>{{ $comment->user?->name }}</strong><div>{{ $comment->body }}</div><small class="text-secondary">{{ $comment->created_at->format('d/m/Y H:i') }}</small></div>@empty<p class="text-secondary">Todavía no hay comentarios.</p>@endforelse</div>
<form method="POST" action="{{ route('documents.comment',$document) }}" class="mt-3">@csrf<textarea name="body" class="form-control mb-2" maxlength="2000" required placeholder="Escribe un comentario académico"></textarea><button class="btn btn-brand btn-sm">Comentar</button></form></div></div></div>
<div class="col-lg-4"><div class="panel"><span class="doc-type">{{ $document->documentType?->name }} · {{ $document->academicArea?->name }}</span><p class="mt-3">{{ $document->topic }} · {{ $document->academic_cycle }}</p><p>Por {{ $document->user?->name }}</p><p class="text-secondary">{{ $document->views_count }} visualizaciones · Promedio {{ $averageRating ? number_format($averageRating,1) : 'Sin calificaciones' }}</p><a class="btn btn-brand w-100 mb-2" href="{{ route('documents.download',$document) }}">Descargar archivo</a>
<form method="POST" action="{{ route('documents.favorite',$document) }}">@csrf<button class="btn btn-outline-brand w-100 mb-2">{{ $isFavorite ? '♥ Quitar de favoritos' : '♡ Guardar en favoritos' }}</button></form>
<form method="POST" action="{{ route('documents.rate',$document) }}" class="mb-3">@csrf<label class="form-label" for="score">Calificación</label><div class="d-flex gap-2"><select class="form-select" name="score" id="score" required>@for($i=1;$i<=5;$i++)<option value="{{ $i }}" @selected($myRating?->score===$i)>{{ $i }} / 5</option>@endfor</select><button class="btn btn-outline-brand">Guardar</button></div></form>
<form method="POST" action="{{ route('documents.report',$document) }}">@csrf<label class="form-label">Reportar contenido</label><select name="reason" class="form-select mb-2" required><option value="">Seleccionar motivo</option>@foreach(['Contenido inapropiado','Derechos de autor','Clasificación incorrecta','Otro'] as $reason)<option value="{{ $reason }}">{{ $reason }}</option>@endforeach</select><textarea name="details" class="form-control mb-2" maxlength="2000" placeholder="Detalles del reporte (opcional)"></textarea><button class="btn btn-outline-danger w-100">Enviar reporte</button></form>
@if(auth()->id()===$document->user_id || auth()->user()->role==='admin')<hr><a class="btn btn-outline-brand w-100 mb-2" href="{{ route('documents.edit',$document) }}">Editar información</a><form method="POST" action="{{ route('documents.destroy',$document) }}" onsubmit="return confirm('¿Retirar esta publicación?')">@csrf @method('DELETE')<button class="btn btn-outline-danger w-100">Retirar publicación</button></form>@endif</div></div></div></section>
@endsection
