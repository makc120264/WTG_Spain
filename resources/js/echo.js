import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

// For some reason it doesn't want to read .ENV settings.

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});


// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: '3rmcf3rbdbdrhbha5yuz',
//     wsHost: 'wtg-spain.loc',
//     wsPort: 80,
//     wssPort: 443,
//     forceTLS: true,
//     enabledTransports: ['ws', 'wss'],
//     disableStats: true,
// });
