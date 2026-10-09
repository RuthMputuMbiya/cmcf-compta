@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')
@section('header-title', '👥 Utilisateurs')
@section('header-subtitle', 'Gestion des comptes utilisateurs')

@section('admin-content')
<div class="fade-up">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <a href="{{ route('admin.users.create') }}" class="btn-primary-custom"><i class="fas fa-plus-circle me-2"></i>Nouvel utilisateur</a>
        <div class="d-flex gap-2">
            <input type="text" id="searchUser" class="form-control-custom" style="width: 250px;" placeholder="🔍 Rechercher...">
            <select id="roleFilter" class="form-control-custom" style="width: 150px;">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                <option value="{{ $role->code }}">{{ $role->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr><th>Matricule</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr data-role="{{ $user->role->code }}" data-name="{{ strtolower($user->name) }}">
                        <td><strong class="text-primary">{{ $user->matricule }}</strong></td>
                        <td>{{ $user->name }} {{ $user->prenom }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->telephone ?? '-' }}</td>
                        <td><span class="badge-info">{{ $user->role->nom }}</span></td>
                        <td>@if($user->actif)<span class="badge-success">Actif</span>@else<span class="badge-danger">Inactif</span>@endif</span>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-primary" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-warning" title="Modifier"><i class="fas fa-edit"></i></a>
                                @if($user->role->code != 'AD')
                                <button onclick="toggleStatus({{ $user->id }}, '{{ $user->actif ? 'désactiver' : 'activer' }}')" class="text-info bg-transparent border-0" title="Changer statut"><i class="fas fa-toggle-{{ $user->actif ? 'on' : 'off' }}"></i></button>
                                <button onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')" class="text-danger bg-transparent border-0" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $users->links() }}</div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('searchUser')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        document.querySelectorAll('#usersTable tbody tr').forEach(r => {
            r.style.display = r.dataset.name?.includes(term) ? '' : 'none';
        });
    });
    document.getElementById('roleFilter')?.addEventListener('change', function() {
        let role = this.value;
        document.querySelectorAll('#usersTable tbody tr').forEach(r => {
            r.style.display = !role || r.dataset.role === role ? '' : 'none';
        });
    });
    
    function toggleStatus(id, action) {
        if(confirm(`${action} cet utilisateur ?`)) window.location.href = `/admin/users/${id}/toggle`;
    }
    function confirmDelete(id, name) {
        if(confirm(`Supprimer l'utilisateur "${name}" ?`)) window.location.href = `/admin/users/${id}/delete`;
    }
</script>
@endpush
@endsection