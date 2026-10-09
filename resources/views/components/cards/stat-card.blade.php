@props([
    'title' => 'Titre',
    'value' => '0',
    'icon' => 'fas fa-chart-line',
    'color' => 'blue',
    'trend' => null,
    'trendValue' => null,
    'unit' => 'FC'
])

@php
    $colors = [
        'blue' => 'from-blue-500 to-blue-600',
        'green' => 'from-green-500 to-green-600',
        'red' => 'from-red-500 to-red-600',
        'yellow' => 'from-yellow-500 to-yellow-600',
        'purple' => 'from-purple-500 to-purple-600',
        'indigo' => 'from-indigo-500 to-indigo-600',
    ];
    
    $gradient = $colors[$color] ?? $colors['blue'];
    
    $trendClass = '';
    $trendIcon = '';
    if ($trend === 'up') {
        $trendClass = 'text-green-500';
        $trendIcon = 'fa-arrow-up';
    } elseif ($trend === 'down') {
        $trendClass = 'text-red-500';
        $trendIcon = 'fa-arrow-down';
    }
@endphp

<div class="bg-gradient-to-r {{ $gradient }} rounded-xl shadow-lg overflow-hidden transform transition hover:scale-105 duration-300">
    <div class="p-6 text-white">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <p class="text-sm opacity-90 font-medium">{{ $title }}</p>
                <p class="text-3xl font-bold mt-2">
                    {{ number_format($value, 0, ',', ' ') }}
                    @if($unit)
                        <span class="text-sm font-normal opacity-80">{{ $unit }}</span>
                    @endif
                </p>
                
                @if($trend && $trendValue)
                    <div class="flex items-center mt-2 space-x-1">
                        <i class="fas {{ $trendIcon }} text-xs {{ $trendClass }}"></i>
                        <span class="text-xs {{ $trendClass }} font-medium">{{ $trendValue }}%</span>
                        <span class="text-xs opacity-75">vs mois dernier</span>
                    </div>
                @endif
            </div>
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                <i class="{{ $icon }} text-2xl"></i>
            </div>
        </div>
    </div>
    
    <!-- Barre de progression (optionnelle) -->
    @if(isset($progress))
    <div class="bg-black bg-opacity-20 px-6 py-3">
        <div class="flex justify-between text-xs mb-1">
            <span>Objectif</span>
            <span>{{ $progress }}%</span>
        </div>
        <div class="w-full bg-white bg-opacity-20 rounded-full h-1">
            <div class="bg-white rounded-full h-1" style="width: {{ $progress }}%"></div>
        </div>
    </div>
    @endif
</div>