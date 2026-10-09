<div class="compte-search">
    <label class="form-label fw-semibold">Compte comptable</label>
    <select class="compte-select form-control-custom" name="compte" required>
        <option value="">Sélectionner un compte...</option>
        @foreach($comptes as $compte)
        <option value="{{ $compte->numero }}" data-type="{{ $compte->type }}">
            {{ $compte->numero }} - {{ $compte->nom }} ({{ $compte->type }})
        </option>
        @endforeach
    </select>
</div>

@push('scripts')
<script>
    // Select2 pour recherche avancée
    $('.compte-select').select2({
        placeholder: '🔍 Rechercher un compte...',
        allowClear: true,
        width: '100%',
        theme: 'bootstrap-5'
    });
</script>
@endpush