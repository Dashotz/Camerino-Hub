document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    const filterButtons = document.querySelectorAll('[data-filter]');
    const activityCards = document.querySelectorAll('.activity-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            const filterType = this.getAttribute('data-filter');
            
            // Show/hide cards based on filter
            activityCards.forEach(card => {
                if (!card) return; // Skip if card is null
                
                const cardType = card.getAttribute('data-type');
                const cardStatus = card.getAttribute('data-status');
                const cardElement = card.closest('.col-md-6'); // Get the parent column

                if (filterType === 'all') {
                    cardElement.style.display = cardStatus === 'active' ? '' : 'none';
                } else if (filterType === 'archived') {
                    cardElement.style.display = cardStatus === 'archived' ? '' : 'none';
                } else {
                    cardElement.style.display = (cardType === filterType && cardStatus === 'active') ? '' : 'none';
                }
            });
        });
    });

    // Set initial filter to 'all'
    const allFilter = document.querySelector('[data-filter="all"]');
    if (allFilter) {
        allFilter.click();
    }
});

// Archive/Restore Activity
function archiveActivity(activityId, currentStatus) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${currentStatus === 'active' ? 'archive' : 'restore'} this activity?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('activity_id', activityId);
            formData.append('action', currentStatus === 'active' ? 'archive' : 'restore');

            fetch('handlers/toggle_activity_status.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update activity');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            });
        }
    });
}

// Delete Activity
function deleteActivity(activityId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('activity_id', activityId);

            fetch('handlers/delete_activity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete activity');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error'
                });
            });
        }
    });
}
