const resetPassword = async (req, res) => {
  try {
    const user = await User.findOne({ email: req.body.email });
    const tempPassword = generateTemporaryPassword(); // Your password generation function
    
    user.password = await bcrypt.hash(tempPassword, 10);
    user.isTemporaryPassword = true; // Set flag
    await user.save();
    
    // Send email with temporary password
    // ...
  } catch (error) {
    // Error handling
  }
};

const changePassword = async (req, res) => {
  try {
    const user = req.user;
    const newPassword = req.body.newPassword;
    
    user.password = await bcrypt.hash(newPassword, 10);
    user.isTemporaryPassword = false; // Reset flag after password change
    await user.save();
    
    req.flash('success', 'Password changed successfully');
    res.redirect('/dashboard');
  } catch (error) {
    // Error handling
  }
}; 