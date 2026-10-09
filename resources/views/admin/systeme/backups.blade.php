@extends('layouts.admin')

@section('title', 'Backups')
@section('header-title', '💾 Gestion des backups')
@section('header-subtitle', 'Sauvegardes de la base de données')

@section('admin-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <form method="POST" action="{{ route('admin.systeme.backup') }}">
            @csrf
            <button type="submit" class="btn-primary-custom"><i class="fas fa-database me-2"></i>Créer un backup</button>
        </form>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr><th>Nom du fichier</th><th>Taille</th><th>Date de création</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                    <tr>
                        <td><i class="fas fa-file-archive text-primary me-2"></i>{{ $backup['name'] }}</td>
                        <td>{{ $backup['size'] }}</td>
                        <td>{{ $backup['date'] }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.systeme.backup.download', $backup['name']) }}" class="text-success" title="Télécharger"><i class="fas fa-download"></i></a>
                                <button onclick="confirmDeleteBackup('{{ $backup['name'] }}')" class="text-danger bg-transparent border-0" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4">Aucun backup disponible</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDeleteBackup(name) {
        if(confirm(`Supprimer le backup "${name}" ?`)) {
            window.location.href = `/admin/systeme/backup/delete/${name}`;
        }
    }
</script>
@endpush
@endsection