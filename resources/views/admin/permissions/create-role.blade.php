@extends('layouts.admin')

@section('title', 'Nouveau rôle')
@section('header-title', '➕ Nouveau rôle')
@section('header-subtitle', 'Créer un nouveau rôle utilisateur')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="p-4">
            <form method="POST" action="{{ route('admin.permissions.store-role') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nom du rôle <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control-custom" placeholder="Ex: Superviseur" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control-custom" placeholder="Ex: SUP" required>
                        <small class="text-muted">Code unique en majuscules (2-5 caractères)</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control-custom" rows="3" placeholder="Description du rôle..."></textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-3">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light px-4 rounded-3">Annuler</a>
                    <button type="submit" class="btn-primary-custom"><i class="fas fa-save me-2"></i>Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection