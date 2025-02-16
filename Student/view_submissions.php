SELECT 
    s.student_id,
    s.firstname,
    s.lastname,
    sas.submission_id,
    sas.submitted_at,
    sas.points,
    sas.feedback,
    sas.security_violation,
    sas.violation_type,
    sas.status,
    sas.remarks,
    sas.time_spent,
    COUNT(sa.answer_id) as total_answers,
    SUM(sa.is_correct) as correct_answers

// ... (in the HTML section where security violation is displayed)
<?php if ($submission['security_violation']): ?>
    <div class="alert alert-warning mt-2">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Quiz auto-submitted: <?php echo htmlspecialchars($submission['violation_type']); ?>
    </div>
<?php endif; ?> 