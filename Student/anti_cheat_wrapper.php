<?php
session_start();
require_once('../db/dbConnector.php');

// Define constants
define('MAX_VIOLATIONS', 3);

if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$quiz_link = $_GET['quiz_link'] ?? '';
$quiz_id = $_GET['quiz_id'] ?? '';
$fullscreen_required = $_GET['fullscreen'] ?? '0';
$prevent_tab_switch = $_GET['prevent_tab'] ?? '0';
$quiz_title = $_GET['title'] ?? 'Quiz';

if (empty($quiz_link) || empty($quiz_id)) {
    die("Invalid parameters");
}
?>

<!DOCTYPE html>
<html lang="en" oncontextmenu="return false">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="../images/light-logo.png">
    <title><?php echo htmlspecialchars($quiz_title); ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #f5f5f5;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .anti-cheat-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .status-bar {
            background: #e9ecef;
            padding: 10px;
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
        }
        .quiz-frame {
            flex: 1;
            border: none;
            width: 100%;
            pointer-events: auto; /* Enable iframe interaction */
            z-index: 1;
        }
        .quiz-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Put overlay behind iframe */
            pointer-events: none; /* Make overlay non-interactive */
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
        .warning {
            color: red;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div id="warning-overlay">
        <h1>⚠️ Warning!</h1>
        <p>Please return to fullscreen mode to continue the quiz</p>
        <button onclick="enterFullscreen()" class="btn btn-light">Return to Fullscreen</button>
    </div>

    <div class="anti-cheat-container">
        <div class="status-bar">
            <span>Anti-Cheat Status: </span>
            <span id="cheat-status">Active</span>
            <span> | Violations: </span>
            <span id="violation-count">0/<?php echo MAX_VIOLATIONS; ?></span>
            <?php if ($fullscreen_required): ?>
            <span> | Fullscreen: </span>
            <span id="fullscreen-status">Required</span>
            <?php endif; ?>
            <span id="warning-message" class="warning"></span>
        </div>

        <div style="position: relative; flex: 1;">
            <iframe id="quiz-frame" 
                    class="quiz-frame" 
                    src="<?php echo htmlspecialchars($quiz_link); ?>" 
                    allowfullscreen
                    sandbox="allow-same-origin allow-scripts allow-forms allow-popups allow-modals">
            </iframe>
            <div id="quiz-overlay" class="quiz-overlay"></div>
        </div>
    </div>

    <script>
        let violations = 0;
        const MAX_VIOLATIONS = 3;
        let isFullscreen = false;
        let lastFocusTime = Date.now();

        function updateStatus() {
            document.getElementById('violation-count').textContent = `${violations}/${MAX_VIOLATIONS}`;
            document.getElementById('cheat-status').textContent = violations > 0 ? 'Violations Detected' : 'Active';
            document.getElementById('cheat-status').style.color = violations > 0 ? 'red' : 'green';
        }

        function exitQuiz(reason) {
            logViolation(reason);
            window.location.href = 'student_quizzes.php?violation=' + encodeURIComponent(reason);
        }

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
            isFullscreen = true;
        }

        // Force fullscreen on start
        document.addEventListener('DOMContentLoaded', function() {
            enterFullscreen();
        });

        // Detect tab switching and window blur
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                violations++;
                updateStatus();
                if (violations >= MAX_VIOLATIONS) {
                    exitQuiz('tab_switching');
                }
            }
        });

        window.onblur = function() {
            const currentTime = Date.now();
            if (currentTime - lastFocusTime > 500) {  // Prevent false positives
                violations++;
                updateStatus();
                if (violations >= MAX_VIOLATIONS) {
                    exitQuiz('window_blur');
                }
            }
        };

        window.onfocus = function() {
            lastFocusTime = Date.now();
        };

        // Fullscreen detection
        document.addEventListener('fullscreenchange', function() {
            if (!document.fullscreenElement) {
                document.getElementById('warning-overlay').style.display = 'block';
                violations++;
                updateStatus();
                if (violations >= MAX_VIOLATIONS) {
                    exitQuiz('fullscreen_exit');
                }
            }
        });

        // Prevent keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Prevent Alt+Tab
            if (e.altKey && e.key === 'Tab') {
                e.preventDefault();
                violations++;
                updateStatus();
            }
            
            // Prevent other shortcuts
            if (
                e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74 || e.keyCode === 67)) || // Ctrl+Shift+I/J/C
                (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                e.altKey || // Any Alt combinations
                e.key === 'F11' || // F11
                e.key === 'Escape' // Escape
            ) {
                e.preventDefault();
                violations++;
                updateStatus();
                if (violations >= MAX_VIOLATIONS) {
                    exitQuiz('keyboard_shortcut');
                }
                return false;
            }
        });

        // Prevent context menu
        window.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            violations++;
            updateStatus();
        });

        // Detect DevTools
        setInterval(function() {
            const threshold = 160;
            if (
                window.outerWidth - window.innerWidth > threshold ||
                window.outerHeight - window.innerHeight > threshold
            ) {
                violations++;
                updateStatus();
                if (violations >= MAX_VIOLATIONS) {
                    exitQuiz('devtools_detected');
                }
            }
        }, 1000);

        // Prevent copy/paste
        document.addEventListener('copy', e => e.preventDefault());
        document.addEventListener('paste', e => e.preventDefault());
        document.addEventListener('cut', e => e.preventDefault());

        // Disable text selection
        document.addEventListener('selectstart', e => e.preventDefault());

        // Log violations to server
        async function logViolation(type) {
            try {
                await fetch('log_violation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        quiz_id: <?php echo $quiz_id; ?>,
                        violation_type: type,
                        timestamp: new Date().toISOString()
                    })
                });
            } catch (error) {
                console.error('Failed to log violation');
            }
        }

        // Update status periodically
        setInterval(updateStatus, 1000);
    </script>
</body>
</html> 