@extends('layouts.app')
@section('title', 'Mis favoritos')
@section('content')
<section class="container page py-5">
    <div class="eyebrow">MI BIBLIOTECA</div>
    <h1 class="page-title">Mis favoritos</h1>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        @php
            $visibleFavorites = $favorites->filter(fn ($favorite) => $favorite->document && $favorite->document->status === 'published');
        @endphp

        @forelse($visibleFavorites as $favorite)
            <div class="col-md-6 col-xl-4">
                <div class="material-card h-100">
                    <div class="doc-type">{{ $favorite->document->documentType?->name }}</div>
                    <h2 class="h5 mt-3">{{ $favorite->document->title }}</h2>
                    <a href="{{ route('documents.show', $favorite->document) }}" class="link-brand">Ver documento →</a>
                    <form method="POST" action="{{ route('documents.favorite', $favorite->document) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-brand btn-sm">Quitar de favoritos</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Aún no has guardado materiales.</p>
                <a class="link-brand" href="{{ route('documents.index') }}">Explorar materiales →</a>
            </div>
        @endforelse
    </div>
</section>
@endsection
