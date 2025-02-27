<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Connection Status -->
        <div 
            x-data="{ show: false }"
            x-show="show"
            x-effect="show = !@entangle('connectionStatus')"
            class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-50"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
        >
            <div class="flex items-center space-x-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                <span>Reconnecting...</span>
            </div>
        </div>

        <!-- Last Update Time -->
        <div class="mb-6 text-right text-gray-600">
            Last update: {{ $lastUpdate }}
        </div>

        <!-- Price Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($prices as $price)
                <div 
                    x-data="{ highlight: false }"
                    x-init="
                        $wire.on('priceUpdated', (eventData) => {
                            if (eventData.symbol === '{{ $price['symbol'] }}') {
                                highlight = true;
                                setTimeout(() => highlight = false, 1000);
                            }
                        });
                    "
                    wire:key="{{ $price['symbol'] }}"
                    class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300"
                    :class="{
                        'ring-2 ring-green-400 bg-green-50': highlight && '{{ $price['trend'] }}' === 'up',
                        'ring-2 ring-red-400 bg-red-50': highlight && '{{ $price['trend'] }}' === 'down'
                    }"
                >
                    <!-- Header -->
                    <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                        <h3 class="text-lg font-semibold">{{ $price['symbol'] }}</h3>
                        <div class="flex items-center space-x-2">
                            @if($price['trend'] === 'up')
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                </svg>
                            @elseif($price['trend'] === 'down')
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-3.707-8.707l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 9.414V13a1 1 0 11-2 0V9.414l-1.293 1.293a1 1 0 01-1.414-1.414z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Price Info -->
                    <div class="p-4">
                        <div class="flex items-center space-x-2">
                            <span class="font-mono text-lg">
                                ${{ number_format($price['price'], 2) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">24h Change</span>
                                <div class="{{ $price['daily_change_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                    {{ number_format($price['daily_change_percentage'], 2) }}%
                                </div>
                            </div>
                            <div>
                                <span class="text-gray-500">BTC Value</span>
                                <div class="font-mono">{{ number_format($price['last_btc'], 8) }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">24h Low</span>
                                <div class="font-mono">${{ number_format($price['lowest'], 2) }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">24h High</span>
                                <div class="font-mono">${{ number_format($price['highest'], 2) }}</div>
                            </div>
                        </div>

                        <!-- Exchanges -->
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($price['exchanges'] as $exchange)
                                <span class="px-2 py-1 bg-gray-100 rounded-full text-xs">
                                    {{ $exchange }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
