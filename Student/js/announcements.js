$(document).ready(function() {
    loadAnnouncements();

    // Simplified loadAnnouncements function to match your PHP
    function loadAnnouncements() {
        $.get('get_announcements.php', function(response) {
            $('#announcementsList').html(response);
            
            // Highlight new announcements if coming from notification
            const urlParams = new URLSearchParams(window.location.search);
            const highlightId = urlParams.get('highlight');
            if (highlightId) {
                const element = document.getElementById('announcement-' + highlightId);
                if (element) {
                    element.classList.add('highlight');
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }).fail(function() {
            showEmptyState();
        });
    }

    // Show empty state
    function showEmptyState() {
        $('#announcementsList').html(`
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <h4>No Announcements Yet</h4>
                <p>Check back later for updates from your teachers.</p>
            </div>
        `);
    }

    // Optional: Add filter functionality
    $('.announcement-type-filter').change(function() {
        const type = $(this).val();
        $('.announcement-card').each(function() {
            if (type === '' || $(this).hasClass(type + '-announcement')) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Refresh announcements periodically (every 5 minutes)
    setInterval(loadAnnouncements, 300000);
});