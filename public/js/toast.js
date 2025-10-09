window.toast = {
    show(type, message) {
        if (!message || typeof message !== 'string') return; // Không hiển thị nếu message rỗng
        window.dispatchEvent(new CustomEvent('notification', {
            detail: { type, message }
        }));
    },
    success(message) {
        this.show('success', message);
    },
    error(message) {
        this.show('error', message);
    },
    info(message) {
        this.show('info', message);
    },
    warning(message) {
        this.show('warning', message);
    }
};