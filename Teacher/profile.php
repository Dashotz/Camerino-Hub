<?php
// At the top of the file, after session_start()
if (isset($_GET['force_change']) && $_GET['force_change'] == 1) {
    echo "<script>
        $(document).ready(function() {
            Swal.fire({
                title: 'Password Change Required',
                text: 'You are using a temporary password. Please change your password to continue.',
                icon: 'warning',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            });
        });
    </script>";
}
?> 