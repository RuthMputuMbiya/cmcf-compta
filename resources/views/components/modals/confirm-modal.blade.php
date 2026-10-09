@props([
    'id' => 'confirmModal',
    'title' => 'Confirmation',
    'message' => 'Êtes-vous sûr de vouloir effectuer cette action ?',
    'confirmText' => 'Confirmer',
    'cancelText' => 'Annuler',
    'confirmColor' => 'red',
    'icon' => 'warning'
])

@php
    $confirmColors = [
        'red' => 'bg-red-600 hover:bg-red-700',
        'green' => 'bg-green-600 hover:bg-green-700',
        'blue' => 'bg-blue-600 hover:bg-blue-700',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700',
    ];
    
    $iconColors = [
        'warning' => 'bg-red-100 text-red-600',
        'success' => 'bg-green-100 text-green-600',
        'info' => 'bg-blue-100 text-blue-600',
        'question' => 'bg-yellow-100 text-yellow-600',
    ];
    
    $icons = [
        'warning' => 'fas fa-exclamation-triangle',
        'success' => 'fas fa-check-circle',
        'info' => 'fas fa-info-circle',
        'question' => 'fas fa-question-circle',
    ];
@endphp

<div id="{{ $id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0" id="{{ $id }}-content">
        <div class="p-6">
            <div class="text-center">
                <div class="w-16 h-16 {{ $iconColors[$icon] }} rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="{{ $icons[$icon] }} text-2xl"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $title }}</h3>
                <p class="text-gray-600">{{ $message }}</p>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button onclick="closeModal('{{ $id }}')" 
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg font-medium transition">
                    {{ $cancelText }}
                </button>
                <button id="{{ $id }}-confirm" 
                        class="flex-1 {{ $confirmColors[$confirmColor] }} text-white py-2 rounded-lg font-medium transition">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + '-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
    
    // Fonction utilitaire pour confirmer une suppression
    function confirmDelete(actionUrl, itemName) {
        const modal = document.getElementById('confirmModal');
        const confirmBtn = document.getElementById('confirmModal-confirm');
        const form = document.createElement('form');
        
        form.method = 'POST';
        form.action = actionUrl;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        
        confirmBtn.onclick = () => {
            document.body.appendChild(form);
            form.submit();
        };
        
        document.querySelector('#confirmModal .text-gray-600').innerHTML = 
            `Êtes-vous sûr de vouloir supprimer <strong class="font-semibold">${itemName}</strong> ? Cette action est irréversible.`;
        
        openModal('confirmModal');
    }
</script>
@endpush