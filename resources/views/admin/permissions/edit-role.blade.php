@extends('layouts.admin')

@section('title', 'Modifier rôle')
@section('header-title', '✏️ Modifier le rôle')
@section('header-subtitle', 'Modification des informations du rôle')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="p-4">
            <form method="POST" action="{{ route('admin.permissions.update-role', $role->id) }}">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" value="{{ $role->nom }}" class="form-control-custom" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Code</label>
                        <input type="text" value="{{ $role->code }}" class="form-control-custom" disabled>
                        <small class="text-muted">Le code ne peut pas être modifié</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control-custom" rows="3">{{ $role->description }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light px-4 rounded-3">Annuler</a>
                    <button type="submit" class="btn-primary-custom"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection