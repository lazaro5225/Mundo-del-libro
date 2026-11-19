@if($p->hasPages())
    <p>
        @if($p->previousPageUrl())
            <a href="{{ $p->previousPageUrl() }}">&laquo; Anterior</a>
        @endif
        Página {{ $p->currentPage() }} de {{ $p->lastPage() }}
        @if($p->nextPageUrl())
            <a href="{{ $p->nextPageUrl() }}">Siguiente &raquo;</a>
        @endif
    </p>
@endif
