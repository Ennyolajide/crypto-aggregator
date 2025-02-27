<div class="p-4 bg-gray-800 text-white text-center">
    <div 
        x-data="clock"
        x-init="startClock"
        class="font-mono text-2xl"
    >
        <span x-text="time"></span>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clock', () => ({
        time: new Date().toLocaleTimeString(),
        startClock() {
            setInterval(() => {
                this.time = new Date().toLocaleTimeString()
            }, 1000)
        }
    }))
})
</script>
@endpush 