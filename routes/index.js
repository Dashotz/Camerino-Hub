const { checkTemporaryPassword } = require('../middleware/auth');

// Apply to all routes except static assets
app.use('*', checkTemporaryPassword);

// Or apply to specific routes
router.use(['/dashboard', '/courses', '/assignments'], checkTemporaryPassword); 