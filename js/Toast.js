class Toast {
    static show(msg, type = 'success', duration = 3000) {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `
            <span class="toast-icon">${type === 'success' ? '✔️' : '❌'}</span>
            <span class="toast-message">${msg}</span>
        `;
        toast.className = `toast show ${type}`;
        setTimeout(() => {
            toast.className = 'toast';
        }, duration);
    }
}
