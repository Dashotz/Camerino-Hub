let sessionTimeout;
const SESSION_TIMEOUT = 7200000; // 2 hours in milliseconds
const WARNING_TIME = 300000; // 5 minutes before timeout

function resetSessionTimer() {
    clearTimeout(sessionTimeout);
    sessionTimeout = setTimeout(showTimeoutWarning, SESSION_TIMEOUT - WARNING_TIME);
}

function showTimeoutWarning() {
    Swal.fire({
        title: 'Session Expiring',
        text: 'Your session will expire in 5 minutes. Would you like to stay logged in?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Stay Logged In',
        cancelButtonText: 'Logout',
        timer: WARNING_TIME,
        timerProgressBar: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Extend session
            fetch('update_session.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resetSessionTimer();
                    } else {
                        window.location.href = 'logout.php';
                    }
                });
        } else {
            window.location.href = 'logout.php';
        }
    });
}

// Initialize session timer
document.addEventListener('DOMContentLoaded', () => {
    resetSessionTimer();
});

// Reset timer on user activity
['click', 'touchstart', 'mousemove', 'keypress'].forEach(event => {
    document.addEventListener(event, () => {
        resetSessionTimer();
    });
}); 