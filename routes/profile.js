const express = require('express');
const router = express.Router();
const Profile = require('../models/Profile');

// Default social links
const defaultSocialLinks = [
  { platform: 'Facebook', icon: 'mdi-facebook', url: 'https://facebook.com' },
  { platform: 'Instagram', icon: 'mdi-instagram', url: 'https://instagram.com' },
  { platform: 'Twitter', icon: 'mdi-twitter', url: 'https://twitter.com' },
  { platform: 'YouTube', icon: 'mdi-youtube', url: 'https://youtube.com' },
];

// Get or create profile
router.get('/', async (req, res) => {
  try {
    // For demo purposes, we'll use a default user ID
    const userId = 'default-user';
    
    let profile = await Profile.findOne({ userId });
    
    if (!profile) {
      // Create a default profile if none exists
      profile = new Profile({
        userId,
        username: 'username',
        bio: 'Welcome to my profile! Connect with me on social media.',
        avatar: '',
        socialLinks: defaultSocialLinks
      });
      await profile.save();
    }
    
    res.json(profile);
  } catch (error) {
    console.error('Error fetching profile:', error);
    res.status(500).json({ error: 'Failed to fetch profile' });
  }
});

// Update profile
router.put('/', async (req, res) => {
  try {
    const userId = 'default-user';
    const { username, bio, avatar, socialLinks } = req.body;
    
    const updateData = {};
    if (username) updateData.username = username;
    if (bio !== undefined) updateData.bio = bio;
    if (avatar !== undefined) updateData.avatar = avatar;
    if (socialLinks) updateData.socialLinks = socialLinks;
    
    const profile = await Profile.findOneAndUpdate(
      { userId },
      updateData,
      { new: true, upsert: true }
    );
    
    res.json(profile);
  } catch (error) {
    console.error('Error updating profile:', error);
    res.status(500).json({ error: 'Failed to update profile' });
  }
});

module.exports = router;
