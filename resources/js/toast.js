window.toast = {
    success(message) {
        window.dispatchEvent(new CustomEvent('notification', {
            detail: { type: 'success', message }
        }));
    },
    
    error(message) {
        window.dispatchEvent(new CustomEvent('notification', {
            detail: { type: 'error', message }
        }));
    },
    
    info(message) {
        window.dispatchEvent(new CustomEvent('notification', {
            detail: { type: 'info', message }
        }));
    },
    
    warning(message) {
        window.dispatchEvent(new CustomEvent('notification', {
            detail: { type: 'warning', message }
        }));
    }
};