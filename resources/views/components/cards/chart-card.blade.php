@props([
    'title' => 'Graphique',
    'subtitle' => null,
    'height' => 300,
    'chartId' => 'chart-' . uniqid()
])

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="flex space-x-2">
                <button onclick="exportChart('{{ $chartId }}')" 
                        class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-download"></i>
                </button>
                <button onclick="refreshChart('{{ $chartId }}')" 
                        class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <canvas id="{{ $chartId }}" style="height: {{ $height }}px; width: 100%;"></canvas>
    </div>
    
    @if(isset($footer))
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
        {{ $footer }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    function exportChart(chartId) {
        const canvas = document.getElementById(chartId);
        const link = document.createElement('a');
        link.download = `chart-${Date.now()}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
    
    function refreshChart(chartId) {
        // Recharger les données du graphique
        location.reload();
    }
</script>
@endpush