@extends('layouts.admin')

@section('title', 'Matrice des permissions')
@section('header-title', '🗺️ Matrice des permissions')
@section('header-subtitle', 'Visualisation des accès par rôle')

@section('admin-content')
<div class="fade-up">
    <div class="data-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="min-width: 200px;">Module / Permission</th>
                        @foreach($roles as $role)
                        <th class="text-center">{{ $role->nom }}<br><small class="text-muted">{{ $role->code }}</small></th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module => $permissions)
                    <tr class="bg-light">
                        <td colspan="{{ count($roles) + 1 }}"><strong class="text-primary">{{ $module }}</strong></td>
                    </tr>
                    @foreach($permissions as $permission)
                    <tr>
                        <td class="ps-4">— {{ $permission }}</td>
                        @foreach($roles as $role)
                        <td class="text-center">
                            @php
                                $hasPermission = false;
                                if($role->code == 'AD') $hasPermission = true;
                                if($role->code == 'DR' && in_array($permission, ['view', 'export', 'dashboard'])) $hasPermission = true;
                                if($role->code == 'CO' && in_array($permission, ['view', 'create', 'edit', 'delete', 'validate', 'dashboard'])) $hasPermission = true;
                                if($role->code == 'CA' && in_array($permission, ['view', 'entree', 'sortie', 'depot', 'retrait', 'dashboard'])) $hasPermission = true;
                                if($role->code == 'VE' && in_array($permission, ['view', 'create', 'pay', 'dashboard'])) $hasPermission = true;
                            @endphp
                            @if($hasPermission)
                            <i class="fas fa-check-circle text-success fs-5"></i>
                            @else
                            <i class="fas fa-times-circle text-danger fs-5"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endpush
@endsection