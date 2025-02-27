import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    cluster: import.meta.env.VITE_REVERB_APP_CLUSTER ?? 'mt1'
});

/// Enable console logging for Pusher
window.Pusher.logToConsole = true;
// Handle connection events
// window.Echo.connector.pusher.connection.bind('connected', () => {
//     Livewire.dispatch('echo:connection-status,connected');
// });