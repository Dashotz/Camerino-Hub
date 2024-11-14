// Move these functions outside of document.ready
function editAnnouncement(id) {
    $.get('get_announcement.php', { id: id }, function(data) {
        if (data.success) {
            $('#edit_announcement_id').val(data.announcement.id);
            $('#edit_section_id').val(data.announcement.section_id).trigger('change');
            $('#edit_content').val(data.announcement.content);
            
            if (data.announcement.attachment) {
                $('#current_attachment').html(
                    `<div class="alert alert-info">
                        Current attachment: <a href="../${data.announcement.attachment}" target="_blank">View</a>
                        <br><small>Upload a new file to replace the current attachment</small>
                    </div>`
                );
            } else {
                $('#current_attachment').empty();
            }
            
            $('#editAnnouncementModal').modal('show');
        } else {
            showAlert('danger', 'Error loading announcement');
        }
    });
}

function deleteAnnouncement(id) {
    currentDeleteId = id;
    $('#deleteConfirmModal').modal('show');
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>`;
    
    $('.card').before(alertHtml);
    
    setTimeout(() => $('.alert').alert('close'), 5000);
}

// Global variable for delete functionality
let currentDeleteId = null;

$(document).ready(function() {
    loadAnnouncements();

    // Filter change handlers
    $('#sectionFilter, #subjectFilter').change(function() {
        loadAnnouncements();
    });

    // Submit announcement
    $('#submitAnnouncement').click(function() {
        const form = $('#announcementForm')[0];
        const formData = new FormData(form);

        // Disable submit button to prevent double submission
        $(this).prop('disabled', true);
        
        $.ajax({
            url: 'create_announcement.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', 'Announcement posted successfully!');
                    
                    // Reset form and close modal
                    form.reset();
                    $('#createAnnouncementModal').modal('hide');
                    
                    // Reload announcements
                    loadAnnouncements();
                } else {
                    showAlert('danger', response.error || 'Error posting announcement');
                }
            },
            error: function() {
                showAlert('danger', 'Error posting announcement');
            },
            complete: function() {
                // Re-enable submit button
                $('#submitAnnouncement').prop('disabled', false);
            }
        });
    });

    function loadAnnouncements() {
        const sectionId = $('#sectionFilter').val();
        const subjectId = $('#subjectFilter').val();

        $.ajax({
            url: 'get_announcements.php',
            type: 'GET',
            data: {
                section_id: sectionId,
                subject_id: subjectId
            },
            success: function(response) {
                if (!response || response.length === 0) {
                    $('#announcementsList').html(`
                        <div class="no-announcements">
                            <div class="empty-state">
                                <i class="fas fa-bullhorn"></i>
                                <h4>No Announcements Yet</h4>
                                <p>Create your first announcement to keep your students informed.</p>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#createAnnouncementModal">
                                    <i class="fas fa-plus"></i> Create Announcement
                                </button>
                            </div>
                        </div>
                    `);
                } else {
                    $('#announcementsList').html(response);
                }
            },
            error: function() {
                showAlert('danger', 'Error loading announcements');
            }
        });
    }

    // Move these handlers inside document.ready
    $('#updateAnnouncement').click(function() {
        const form = $('#editAnnouncementForm')[0];
        const formData = new FormData(form);

        $.ajax({
            url: 'edit_announcement.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editAnnouncementModal').modal('hide');
                    loadAnnouncements();
                    showAlert('success', 'Announcement updated successfully!');
                } else {
                    showAlert('danger', response.message || 'Error updating announcement');
                }
            },
            error: function() {
                showAlert('danger', 'Error updating announcement');
            }
        });
    });

    $('#confirmDelete').click(function() {
        if (!currentDeleteId) return;

        $.post('delete_announcement.php', { announcement_id: currentDeleteId }, function(response) {
            if (response.success) {
                $('#deleteConfirmModal').modal('hide');
                loadAnnouncements();
                showAlert('success', 'Announcement deleted successfully!');
            } else {
                showAlert('danger', response.message || 'Error deleting announcement');
            }
        });
    });

    // Handle section selection in create form
    $('#create_section_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const subjectId = selectedOption.data('subject-id');
        const subjectName = selectedOption.data('subject-name');
        
        $('#create_subject_id').val(subjectId);
        $('#create_subject_display').val(subjectName);
    });

    // Handle section selection in edit form
    $('#edit_section_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const subjectId = selectedOption.data('subject-id');
        const subjectName = selectedOption.data('subject-name');
        
        $('#edit_subject_id').val(subjectId);
        $('#edit_subject_display').val(subjectName);
    });

    // Update the filter section
    $('#sectionFilter').change(function() {
        const selectedOption = $(this).find('option:selected');
        const subjectId = selectedOption.data('subject-id');
        $('#subjectFilter').val(subjectId);
        loadAnnouncements();
    });

    // Handle announcement type change
    $('#announcement_type').change(function() {
        const type = $(this).val();
        const referenceSelector = $('.reference-selector');
        
        if (type === 'normal') {
            referenceSelector.hide();
            $('#reference_id').prop('required', false);
        } else {
            // Load relevant references based on type
            $.get('get_references.php', { type: type }, function(data) {
                if (data.success) {
                    const select = $('#reference_id');
                    select.empty();
                    select.append('<option value="">Select...</option>');
                    
                    data.references.forEach(function(ref) {
                        select.append(`<option value="${ref.id}">${ref.title}</option>`);
                    });
                    
                    referenceSelector.show();
                    $('#reference_id').prop('required', true);
                }
            });
        }
    });
});
