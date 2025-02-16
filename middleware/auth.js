const checkTemporaryPassword = async (req, res, next) => {
  try {
    if (!req.user) return next();
    
    const user = req.user;
    
    // Check if user has temporary password
    if (user.isTemporaryPassword) {
      if (user.role === 'student') {
        if (!req.path.includes('/student/profile/security')) {
          req.flash('warning', 'Please change your temporary password before continuing');
          return res.redirect('/student/profile/security');
        }
      }
      
      if (user.role === 'teacher') {
        if (!req.path.includes('/teacher/profile/security')) {
          req.flash('warning', 'Please change your temporary password before continuing');
          return res.redirect('/teacher/profile/security');
        }
      }
    }
    
    next();
  } catch (error) {
    console.error('Temporary password check error:', error);
    res.status(500).send('Server error');
  }
};

module.exports = {
  checkTemporaryPassword
}; 