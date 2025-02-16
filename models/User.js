const mongoose = require('mongoose');

const userSchema = new mongoose.Schema({
  // ... your existing fields
  isTemporaryPassword: {
    type: Boolean,
    default: false
  }
});

module.exports = mongoose.model('User', userSchema);