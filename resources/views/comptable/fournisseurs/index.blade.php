@extends('layouts.comptable')

@section('title', 'Fournisseurs')
@section('header-title', '🚚 Fournisseurs')
@section('header-subtitle', 'Gestion des fournisseurs')

@section('comptable-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('comptable.fournisseurs.create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouveau fournisseur</a>
        <input type="text" id="searchFournisseur" class="form-control-custom" style="width: 250px;" placeholder="🔍 Rechercher...">
    </div>

    <div class="row g-4" id="fournisseursGrid">
        @forelse($fournisseurs as $fournisseur)
        <div class="col-md-6 col-lg-4 fournisseur-card" data-name="{{ strtolower($fournisseur->nom) }}">
            <div class="data-card h-100">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon stat-warning"><i class="fas fa-truck fa-2x"></i></div>
                        <div class="dropdown"><button class="btn btn-light rounded-circle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                            <ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('comptable.fournisseurs.edit', $fournisseur->id) }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete({{ $fournisseur->id }}, '{{ $fournisseur->nom }}')"><i class="fas fa-trash-alt me-2"></i>Supprimer</button></li></ul>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $fournisseur->nom }}</h5>
                    <p class="text-muted small mb-3">{{ $fournisseur->email ?? 'Email non renseigné' }}</p>
                    <div class="border-top pt-3"><div class="d-flex justify-content-between small mb-2"><span class="text-muted">📞 Téléphone</span><span>{{ $fournisseur->telephone ?? '-' }}</span></div></div>
                    <a href="{{ route('comptable.fournisseurs.show', $fournisseur->id) }}" class="btn btn-outline-warning w-100 mt-3 rounded-3">Voir détails <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        @empty<div class="col-12 text-center py-5"><i class="fas fa-truck fa-4x text-muted mb-3"></i><p>Aucun fournisseur</p></div>@endforelse
    </div>
    <div class="mt-4">{{ $fournisseurs->links() }}</div>
</div>

@push('scripts')
<script>
    document.getElementById('searchFournisseur')?.addEventListener('keyup', function(){
        let term=this.value.toLowerCase();
        document.querySelectorAll('.fournisseur-card').forEach(c=>{c.style.display=c.dataset.name.includes(term)?'': 'none';});
    });
    function confirmDelete(id,nom){if(confirm(`Supprimer "${nom}" ?`)) window.location.href=`/comptable/fournisseurs/${id}/delete`;}
</script>
@endpush
@endsection