import Echo from 'laravel-echo';

let echo = null;

export const initializeEcho = () => {
    if (!echo) {
        echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });
    }
    return echo;
};

export const emit = (channel, event, data) => {
    if (!echo) {
        echo = initializeEcho();
    }
    return echo.private(channel).whisper(event, data);
};

export const listen = (channel, event, callback) => {
    if (!echo) {
        echo = initializeEcho();
    }
    return echo.private(channel).listen(event, callback);
};

export default {
    emit,
    listen,
    initializeEcho
}; 