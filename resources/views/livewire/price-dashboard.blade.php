<div class="min-h-screen bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Connection Status -->
        <div 
            x-data="{ show: true }"
            x-init="
                window.Echo.connector.pusher.connection.bind('state_change', (states) => {
                    //console.log('Connection state:', states.current);
                    show = states.current !== 'connected';
                });
                // Set initial state
                show = window.Echo.connector.pusher.connection.state !== 'connected';
            "
            x-show="show"
            x-cloak
            class="fixed top-4 right-4 px-6 py-3 bg-white rounded-lg shadow-xl z-50 border-2 border-red-500"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
        >
            <div class="flex items-center space-x-4">
                <svg class="animate-spin h-5 w-5 text-red-500" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                </svg>
                <span class="font-medium text-red-500 text-base tracking-wide">
                    &nbsp; &nbsp; Connecting
                </span>
            </div>
        </div>

        <!-- Last Update Time -->
        <div class="mb-6 text-right text-gray-600" 
            x-data="{ 
                lastUpdateTime: Date.now(),
                updateDisplay() {
                    const seconds = Math.round((Date.now() - this.lastUpdateTime) / 1000);
                    this.$refs.timeDisplay.textContent = `${seconds} seconds ago`;
                },
                init() {
                    this.updateDisplay();
                    setInterval(() => this.updateDisplay(), 1000);
                }
            }"
            @price-updated.window="lastUpdateTime = Date.now()"
        >
            Last update: <span x-ref="timeDisplay">0 seconds ago</span>
        </div>

        <!-- Loading State -->
        <div x-data="{ isLoading: true }" 
            x-init="isLoading = !$wire.prices || Object.keys($wire.prices).length === 0"
            x-show="isLoading"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Fetching market data...</p>
        </div>

        <!-- Price Grid -->
        <div x-data="pricesData()" 
            x-show="Object.keys(prices).length > 0"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-init="init()">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <template x-for="price in prices" :key="price.symbol">
                    <div 
                        x-data="{ highlight: false }"
                        x-init="
                            $watch('price', () => {
                                highlight = true;
                                setTimeout(() => highlight = false, 1000);
                            });
                        "
                        class="bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300"
                        :class="{
                            'ring-2 ring-green-400 bg-green-50': highlight && price.trend === 'up',
                            'ring-2 ring-red-400 bg-red-50': highlight && price.trend === 'down'
                        }"
                    >
                        <!-- Header -->
                        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
                            <h3 class="text-lg font-semibold" x-text="price.symbol"></h3>
                            <div class="flex items-center space-x-2">
                            <template x-if="price.trend === 'up'">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd" />
                                </svg>
                            </template>
                            <template x-if="price.trend === 'down'">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm3.707 8.707l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L9 10.586V7a1 1 0 112 0v3.586l1.293-1.293a1 1 0 111.414 1.414z" clip-rule="evenodd" />
                                </svg>
                            </template>

                                <template x-if="price.trend === 'neutral'">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                            </div>
                        </div>

                        <!-- Price Info -->
                        <div class="p-4">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-lg" x-text="'$' + new Intl.NumberFormat('en-US').format(parseFloat(price.price).toFixed(2))"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">24h Change</span>
                                    <div :class="price.daily_change_percentage >= 0 ? 'text-green-600' : 'text-red-600'">
                                        <span x-text="price.daily_change_percentage.toFixed(2) + '%'"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-500">Previous Price</span>
                                    <div class="font-mono" x-text="'$' + new Intl.NumberFormat('en-US').format(parseFloat(price.previous_price).toFixed(8))"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500">24h Low</span>
                                    <div class="font-mono" x-text="'$' + new Intl.NumberFormat('en-US').format(parseFloat(price.lowest).toFixed(2))"></div>
                                </div>
                                <div>
                                    <span class="text-gray-500">24h High</span>
                                    <div class="font-mono" x-text="'$' + new Intl.NumberFormat('en-US').format(parseFloat(price.highest).toFixed(2))"></div>
                                </div>
                            </div>

                            <!-- Exchanges -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <template x-for="exchange in price.exchanges">
                                    <span class="px-2 py-1 bg-gray-100 rounded-full text-xs" x-text="exchange"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Chart Section -->
            <div class="mt-8 p-6 bg-white rounded-xl shadow-lg" x-show="Object.keys(prices || {}).length > 0">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Market Overview</h2>
                <div id="chart" class="mt-4"></div>
            </div>
        </div>

        <!-- No Data State -->
        <div x-data="{ noData: true }" 
            x-init="noData = !$wire.prices || Object.keys($wire.prices).length === 0"
            x-show="noData"
            class="text-center py-12">
            <p class="text-gray-600">No market data available at the moment.</p>
        </div>
    </div>
</div>

<!-- Load ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<!-- Load moment.js before your scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<script>
function pricesData() {
    return {
        prices: @json($prices),
        chart: null,
        colorCache: {},
        chartData: {},

        init() {
            window.pricesComponent = this;
            this.initializeChartData();
            this.$nextTick(() => {
                this.initializeChart();
                this.listenForPriceUpdates();
            });
        },

        initializeChartData() {
            const initialTime = new Date().getTime();
            Object.entries(this.prices).forEach(([symbol, data]) => {
                this.chartData[symbol] = [{
                    x: initialTime,
                    y: parseFloat(data.price)
                }];
            });
        },

        getSymbolColor(symbol) {
            if (this.colorCache[symbol]) return this.colorCache[symbol];
            const hue = Math.random() * 360;
            const color = `hsl(${hue}, 70%, 50%)`;
            this.colorCache[symbol] = color;
            return color;
        },

        initializeChart() {
            const series = Object.entries(this.chartData).map(([symbol, data]) => ({
                name: symbol,
                data: data
            }));

            const options = {
                series,
                chart: {
                    type: 'line',
                    height: 600,
                    animations: {
                        enabled: false // Initially disable animations for faster loading
                    },
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    }
                },
                stroke: {
                    curve: 'straight',
                    width: 2
                },
                colors: series.map(s => this.getSymbolColor(s.name)),
                dataLabels: {
                    enabled: true,
                    offsetY: -5,
                    style: {
                        fontSize: '10px',
                        fontWeight: 'normal'
                    },
                    background: {
                        enabled: true,
                        padding: 2,
                        borderRadius: 2
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        datetimeUTC: false,
                        format: 'HH:mm:ss'
                    }
                },
                yaxis: {
                    logarithmic: true,
                    labels: {
                        formatter: (value) => '$' + new Intl.NumberFormat('en-US').format(value.toFixed(2))
                    },
                    tickAmount: 8
                },
                legend: {
                    position: 'right',
                    fontSize: '11px'
                },
                tooltip: {
                    enabled: true,
                    shared: true,
                    x: {
                        format: 'HH:mm:ss'
                    },
                    y: {
                        formatter: (value) => '$' + new Intl.NumberFormat('en-US').format(value.toFixed(2))
                    }
                },
                markers: {
                    size: 4
                }
            };

            this.chart = new ApexCharts(document.querySelector("#chart"), options);
            this.chart.render().then(() => {
                // Enable animations after initial render
                this.chart.updateOptions({
                    chart: {
                        animations: {
                            enabled: true,
                            easing: 'linear',
                            dynamicAnimation: {
                                speed: 1000
                            }
                        }
                    }
                });
            });
        },

        updateChart() {
            const currentTime = new Date().getTime();

            // Update chartData with new values
            Object.entries(this.prices).forEach(([symbol, data]) => {
                if (!this.chartData[symbol]) {
                    this.chartData[symbol] = [];
                }
                
                this.chartData[symbol].push({
                    x: currentTime,
                    y: parseFloat(data.price)
                });

                // Keep only last 30 points for better performance
                if (this.chartData[symbol].length > 30) {
                    this.chartData[symbol] = this.chartData[symbol].slice(-30);
                }
            });

            // Update chart with new series data
            const updatedSeries = Object.entries(this.chartData).map(([symbol, data]) => ({
                name: symbol,
                data: data
            }));

            this.chart.updateSeries(updatedSeries, true);
        },

        listenForPriceUpdates() {
            window.Echo.channel('prices')
                .listen('.PriceUpdated', (eventData) => {
                    console.log('PriceUpdated', eventData);
                    this.updatePrice(eventData);
                    this.updateChart();
                    window.dispatchEvent(new CustomEvent('price-updated'));
                });
        },

        updatePrice(eventData) {
            let symbolKey = eventData.symbol;
            
            // Update or create price data
            this.prices[symbolKey] = {
                ...eventData,
                previous_price: this.prices[symbolKey]?.price || eventData.price,
                exchanges: eventData.exchanges || this.prices[symbolKey]?.exchanges || []
            };

            // Force Alpine to recognize the change
            this.prices = { ...this.prices };
            
            // Dispatch event to hide loading state
            window.dispatchEvent(new CustomEvent('price-updated'));

            // Initialize chart if this is the first data
            if (Object.keys(this.prices).length === 1 && !this.chart) {
                this.initializeChartData();
                this.initializeChart();
            }

            // Initialize chart data for new symbols
            if (!this.chartData[symbolKey]) {
                this.chartData[symbolKey] = [];
            }

            // Update the chart
            this.updateChart();
        }
    };
}
</script>