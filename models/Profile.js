const mongoose = require('mongoose');

const socialLinkSchema = new mongoose.Schema({
  platform: { type: String, required: true },
  icon: { type: String, required: true },
  url: { type: String, required: true }
});

const profileSchema = new mongoose.Schema({
  userId: { 
    type: String, 
    required: true,
    unique: true,
    default: 'default-user' // For demo purposes
  },
  username: { 
    type: String, 
    required: true,
    trim: true,
    default: 'username'
  },
  bio: { 
    type: String, 
    default: 'Welcome to my profile! Connect with me on social media.'
  },
  avatar: { 
    type: String,
    default: ''
  },
  socialLinks: [socialLinkSchema],
  createdAt: { 
    type: Date, 
    default: Date.now 
  },
  updatedAt: { 
    type: Date, 
    default: Date.now 
  }
});

// Update the updatedAt field before saving
profileSchema.pre('save', function(next) {
  this.updatedAt = new Date();
  next();
});

const Profile = mongoose.model('Profile', profileSchema);

module.exports = Profile;
