import React, { createContext, useContext, useCallback } from 'react';

const EventBusContext = createContext();

export function EventBusProvider({ children }) {
    const listeners = new Map();

    const emit = useCallback((event, data) => {
        if (listeners.has(event)) {
            listeners.get(event).forEach(callback => callback(data));
        }
    }, []);

    const on = useCallback((event, callback) => {
        if (!listeners.has(event)) {
            listeners.set(event, new Set());
        }
        listeners.get(event).add(callback);

        return () => {
            const eventListeners = listeners.get(event);
            if (eventListeners) {
                eventListeners.delete(callback);
            }
        };
    }, []);

    const value = {
        emit,
        on
    };

    return (
        <EventBusContext.Provider value={value}>
            {children}
        </EventBusContext.Provider>
    );
}

export function useEventBus() {
    const context = useContext(EventBusContext);
    if (!context) {
        throw new Error('useEventBus must be used within an EventBusProvider');
    }
    return context;
}