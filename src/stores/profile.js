import { ref, provide, inject } from 'vue';

const PROFILE_STORAGE_KEY = 'userProfile';

// Create a symbol for the profile store
const profileStoreSymbol = Symbol('profileStore');

// Default profile data
const defaultProfile = {
  username: 'username',
  bio: 'Welcome to my profile! Connect with me on social media.',
  avatar: '',
  socialLinks: [
    { platform: 'Facebook', icon: 'mdi-facebook', url: 'https://facebook.com' },
    { platform: 'Instagram', icon: 'mdi-instagram', url: 'https://instagram.com' },
    { platform: 'Twitter', icon: 'mdi-twitter', url: 'https://twitter.com' },
    { platform: 'YouTube', icon: 'mdi-youtube', url: 'https://youtube.com' },
  ]
};

// Create the profile store
const createProfileStore = () => {
  const profile = ref({...defaultProfile});
  const isLoading = ref(false);
  const error = ref(null);

  // Load profile from localStorage
  const loadProfile = () => {
    try {
      const savedProfile = localStorage.getItem(PROFILE_STORAGE_KEY);
      if (savedProfile) {
        profile.value = { ...defaultProfile, ...JSON.parse(savedProfile) };
      }
    } catch (e) {
      console.error('Error loading profile:', e);
      error.value = 'Failed to load profile';
    }
  };

  // Save profile to localStorage
  const saveProfile = (newProfile) => {
    try {
      profile.value = { ...profile.value, ...newProfile };
      localStorage.setItem(PROFILE_STORAGE_KEY, JSON.stringify(profile.value));
      // Dispatch storage event to notify other tabs/windows
      window.dispatchEvent(new StorageEvent('storage', {
        key: PROFILE_STORAGE_KEY,
        newValue: JSON.stringify(profile.value)
      }));
      return true;
    } catch (e) {
      console.error('Error saving profile:', e);
      error.value = 'Failed to save profile';
      return false;
    }
  };

  // Reset profile to default
  const resetProfile = () => {
    profile.value = { ...defaultProfile };
    localStorage.removeItem(PROFILE_STORAGE_KEY);
  };

  return {
    profile,
    isLoading,
    error,
    loadProfile,
    saveProfile,
    resetProfile
  };
};

// Provide the profile store to the app
export const provideProfileStore = () => {
  const store = createProfileStore();
  provide(profileStoreSymbol, store);
  return store;
};

// Inject the profile store from a component
export const useProfileStore = () => {
  const store = inject(profileStoreSymbol);
  if (!store) {
    throw new Error('Profile store not provided. Make sure to call provideProfileStore() in a parent component.');
  }
  return store;
};

export default {
  provide: provideProfileStore,
  use: useProfileStore
};
