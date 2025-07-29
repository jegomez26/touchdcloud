import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT, // Use the non-secure port
    // wssPort: import.meta.env.VITE_REVERB_PORT ?? 443, // Keep commented for non-secure
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https', // IMPORTANT: This will now be 'false'
    disableStats: true, // Optional
    // enabledTransports: ['ws'], // Explicitly prefer ws if you want
});
