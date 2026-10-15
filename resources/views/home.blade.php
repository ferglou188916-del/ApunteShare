@extends('layouts.app')
@section('title','Inicio')
@section('content')
<div class="container"><section class="hero my-4 p-4 p-md-5"><span class="hero-label">RECURSOS ACADÉMICOS COMPARTIDOS</span><h1>Encuentra, comparte y organiza<br>tus materiales académicos</h1><p>Accede a apuntes, guías y presentaciones creadas por estudiantes. El conocimiento se comparte mejor.</p><form action="{{ route('documents.index') }}" class="hero-search"><input aria-label="Buscar materiales" placeholder="Buscar por materia, tema o documento..." name="q"><button>Buscar</button></form><div class="d-flex flex-wrap gap-2 mt-3"><a class="btn btn-light btn-sm" href="{{ route('documents.index') }}">Explorar materiales</a><a class="btn btn-outline-light btn-sm" href="{{ route('documents.create') }}">Compartir apuntes</a></div></section><section class="py-4"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="section-title">Materiales destacados</h2><p class="text-secondary mb-0">Contenido para acompañarte en tus estudios.</p></div><a class="link-brand" href="{{ route('documents.index') }}">Ver todos →</a></div><div class="row g-3">
@forelse($featuredDocuments as $document)
    <div class="col-md-6 col-xl-4">
        <article class="material-card h-100">
            <div class="d-flex gap-3">
                <div class="doc-icon">
                    {{ strtoupper(mb_substr($document->title, 0, 2)) }}
                </div>

                <div class="flex-grow-1">
                    <span class="doc-type">
                        {{ $document->documentType?->name ?? 'Material' }}
                        ·
                        {{ $document->academicArea?->name ?? 'General' }}
                    </span>

                    <h3 class="h6 mt-2">{{ $document->title }}</h3>

                    <p class="small text-secondary mb-1">
                        {{ $document->university }}
                    </p>

                    <div class="small text-secondary">
                        {{ strtoupper(pathinfo($document->original_filename, PATHINFO_EXTENSION)) }}
                        · Ciclo {{ $document->academic_cycle }}
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                <span class="small text-secondary">
                    {{ number_format($document->views_count ?? 0) }} visualizaciones
                </span>

                <a href="{{ route('documents.show', $document) }}"
                   class="small fw-semibold link-brand">
                    Ver documento →
                </a>
            </div>
        </article>
    </div>
@empty
    <div class="col-12">
        <div class="material-card text-center p-4">
            <h3 class="h5">Todavia no hay materiales publicados</h3>
            <p class="text-secondary mb-0">
                Los documentos que se publiquen apareceran aqui automaticamente.
            </p>
        </div>
    </div>
@endforelse
</div></section><section class="py-4"><h2 class="section-title">Explora por áreas de estudio</h2><div class="row g-3"><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Ingeniería y Tecnología <span class="ms-auto">→</span></a></div><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Medicina y Salud <span class="ms-auto">→</span></a></div><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Derecho y Leyes <span class="ms-auto">→</span></a></div><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Economía y Negocios <span class="ms-auto">→</span></a></div><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Arquitectura <span class="ms-auto">→</span></a></div><div class="col-md-4"><a class="category-card" href="{{ route('documents.index') }}"><span>✦</span> Ciencias Sociales <span class="ms-auto">→</span></a></div></div></section></div>
@endsection
