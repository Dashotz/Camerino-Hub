<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['student_id']) || !isset($_GET['quiz_id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$quiz_id = $_GET['quiz_id'];
$student_id = $_SESSION['student_id'];

// Fetch quiz details
$quiz_query = "SELECT * FROM activities WHERE activity_id = ? AND type = 'quiz'";
$stmt = $db->prepare($quiz_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" oncontextmenu="return false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?> - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            margin: 0; 
            padding: 0; 
            overflow: hidden; 
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        #quiz-container { 
            width: 100vw; 
            height: 100vh; 
            pointer-events: auto;
        }
        #warning-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,0,0,0.9);
            display: none;
            z-index: 1000;
            color: white;
            text-align: center;
            padding-top: 20%;
        }
    </style>
</head>
<body>
    <div id="warning-overlay">
        <h1>⚠️ Warning!</h1>
        <p>Please return to fullscreen mode to continue the quiz</p>
        <button onclick="enterFullscreen()" class="btn btn-light">Return to Fullscreen</button>
    </div>

    <iframe id="quiz-container" 
            src="<?php echo htmlspecialchars($quiz['quiz_link']); ?>" 
            frameborder="0" 
            allowfullscreen>
    </iframe>

    <script>
    // Disable right-click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Disable keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Prevent F12
        if(e.key === 'F12') {
            e.preventDefault();
        }
        
        // Prevent Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
        if(e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) {
            e.preventDefault();
        }
        
        // Prevent Ctrl+U (view source)
        if(e.ctrlKey && e.key === 'u') {
            e.preventDefault();
        }

        // Prevent Ctrl+S (save page)
        if(e.ctrlKey && e.key === 's') {
            e.preventDefault();
        }
    });

    // Disable DevTools through console
    setInterval(function() {
        debugger;
    }, 100);

    // Clear console
    setInterval(function() {
        console.clear();
        console.log('%cStop!', 'color: red; font-size: 50px; font-weight: bold;');
        console.log('%cThis is a secure testing environment. Attempting to access developer tools is prohibited.', 
            'font-size: 20px;');
    }, 100);

    let timeLeft = <?php echo $quiz['time_limit'] * 60; ?>;
    const preventTabSwitch = <?php echo $quiz['prevent_tab_switch'] ? 'true' : 'false'; ?>;
    const fullscreenRequired = <?php echo $quiz['fullscreen_required'] ? 'true' : 'false'; ?>;
    
    function enterFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
        document.getElementById('warning-overlay').style.display = 'none';
    }

    // Additional security for iframe source
    window.onload = function() {
        const frame = document.getElementById('quiz-container');
        frame.contentWindow.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            return false;
        });
    };

    // Detect if DevTools is open
    let devtools = function() {};
    devtools.toString = function() {
        showWarning();
        return '';
    };
    
    setInterval(function() {
        console.profile(devtools);
        console.profileEnd(devtools);
    }, 100);

    function showWarning() {
        document.body.innerHTML = '<h1 style="color: red; text-align: center; margin-top: 50px;">Developer Tools detected.<br>This incident will be reported.</h1>';
        // You can also log this incident to your database
        logSecurityViolation();
    }

    async function logSecurityViolation() {
        try {
            await fetch('log_security_violation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    quiz_id: <?php echo $quiz_id; ?>,
                    violation_type: 'devtools_opened',
                    timestamp: new Date().toISOString()
                })
            });
        } catch (error) {
            console.error('Failed to log security violation');
        }
    }

    // Prevent tab switching
    if (preventTabSwitch) {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Log attempt and show warning
                console.log('Tab switch attempted');
            }
        });
    }

    // Prevent copy/paste if enabled
    if (<?php echo $quiz['prevent_copy_paste'] ? 'true' : 'false'; ?>) {
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
    }

    // Fullscreen change detection
    if (fullscreenRequired) {
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) {
                document.getElementById('warning-overlay').style.display = 'block';
            }
        });
    }

    // Timer functionality
    if (timeLeft > 0) {
        const timer = setInterval(() => {
            timeLeft--;
            if (timeLeft <= 0) {
                clearInterval(timer);
                // Submit quiz or show timeout message
                window.location.href = 'quiz_timeout.php?quiz_id=<?php echo $quiz_id; ?>';
            }
        }, 1000);
    }
    </script>

    <!-- Additional security measures -->
    <script>
    // Disable view source
    document.onkeydown = function(e) {
        if (e.ctrlKey && 
            (e.keyCode === 67 || 
             e.keyCode === 86 || 
             e.keyCode === 85 || 
             e.keyCode === 117)) {
            return false;
        } else {
            return true;
        }
    };
    </script>
</body>
</html>
