document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const wrapper = document.querySelector('.wrapper');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            wrapper.classList.toggle('sidebar-collapsed');
        });
    }
    
    // Class Card Color Generator
    function generateRandomColor() {
        const colors = [
            '#1a73e8', '#00887a', '#8e24aa', '#d93025', '#1e8e3e',
            '#3c4043', '#f6bf26', '#e37400', '#1967d2', '#00796b'
        ];
        return colors[Math.floor(Math.random() * colors.length)];
    }
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });
    
    // Handle Create Class Form
    const createClassForm = document.querySelector('#createClassModal form');
    if (createClassForm) {
        createClassForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('create_class.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the class');
            });
        });
    }
    
    // Handle Class Card Actions
    document.querySelectorAll('.class-actions .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            const action = this.dataset.action;
            const classId = this.closest('.class-card').dataset.classId;
            
            if (action === 'archive') {
                if (confirm('Are you sure you want to archive this class?')) {
                    archiveClass(classId);
                }
            }
        });
    });
    
    function archiveClass(classId) {
        fetch('archive_class.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ class_id: classId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the class');
        });
    }
});
