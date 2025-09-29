// Auto-hide da mensagem após 5 segundos
setTimeout(function() {
    const alert = document.getElementById('mensagem-alert');
    if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 5000);