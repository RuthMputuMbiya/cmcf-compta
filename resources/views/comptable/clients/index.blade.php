@extends('layouts.comptable')

@section('title', 'Clients')
@section('header-title', '👥 Clients')
@section('header-subtitle', 'Gestion du portefeuille clients')

@section('comptable-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('comptable.clients.create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouveau client</a>
        <input type="text" id="searchClient" class="form-control-custom" style="width: 250px;" placeholder="🔍 Rechercher...">
    </div>

    <div class="row g-4" id="clientsGrid">
        @forelse($clients as $client)
        <div class="col-md-6 col-lg-4 client-card" data-name="{{ strtolower($client->nom) }}">
            <div class="data-card h-100">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon stat-primary"><i class="fas fa-user fa-2x"></i></div>
                        <div class="dropdown"><button class="btn btn-light rounded-circle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('comptable.clients.edit', $client->id) }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete({{ $client->id }}, '{{ $client->nom }}')"><i class="fas fa-trash-alt me-2"></i>Supprimer</button></li></ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $client->nom }}</h5>
                    <p class="text-muted small mb-3">{{ $client->email ?? 'Email non renseigné' }}</p>
                    <div class="border-top pt-3"><div class="d-flex justify-content-between small mb-2"><span class="text-muted">📞 Téléphone</span><span>{{ $client->telephone ?? '-' }}</span></div></div>
                    <a href="{{ route('comptable.clients.show', $client->id) }}" class="btn btn-outline-primary w-100 mt-3 rounded-3">Voir détails <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        @empty<div class="col-12 text-center py-5"><i class="fas fa-users fa-4x text-muted mb-3"></i><p>Aucun client</p></div>@endforelse
    </div>
    <div class="mt-4">{{ $clients->links() }}</div>
</div>

@push('scripts')
<script>
    document.getElementById('searchClient')?.addEventListener('keyup', function(){
        let term=this.value.toLowerCase();
        document.querySelectorAll('.client-card').forEach(c=>{c.style.display=c.dataset.name.includes(term)?'': 'none';});
    });
    function confirmDelete(id,nom){if(confirm(`Supprimer "${nom}" ?`)) window.location.href=`/comptable/clients/${id}/delete`;}
</script>
@endpush
@endsection