<template>
  <v-container class="fill-height" fluid :class="{ 'dark': theme.global.current.value.dark }">
    <v-row class="fill-height">
      <!-- Editor Sidebar -->
      <v-col cols="12" md="6" class="d-flex flex-column">
        <h1 class="text-h4 font-weight-bold mb-2">Profile</h1>
        <p class="text-subtitle-1 text-grey-darken-1 mb-8">
          Customize your profile with your personal information and social links.
        </p>

        <!-- Profile Information Card -->
        <v-card class="mb-6" flat>
          <v-card-text>
            <div class="d-flex align-center mb-6">
              <v-avatar size="80" class="mr-4">
                <v-img
                  v-if="formData.avatar"
                  :src="formData.avatar"
                  cover
                ></v-img>
                <v-icon v-else size="40" color="grey-lighten-1">mdi-account-circle</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 text-grey">Profile Picture</div>
                <v-btn
                  variant="outlined"
                  color="primary"
                  size="small"
                  prepend-icon="mdi-upload"
                  @click="$refs.fileInput.click()"
                >
                  Upload Image
                </v-btn>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  style="display: none"
                  @change="handleFileUpload"
                >
                <div v-if="uploadError" class="text-caption text-error mb-4 mt-2">{{ uploadError }}</div>
                <div class="text-caption text-grey mt-1">Max file size: 2MB</div>
              </div>
            </div>

            <v-text-field
              v-model="formData.username"
              label="Username"
              variant="outlined"
              density="comfortable"
              class="mb-4"
              placeholder="@yourusername"
              prepend-inner-icon="mdi-at"
              hide-details
            ></v-text-field>

            <v-textarea
              v-model="formData.bio"
              label="Bio"
              variant="outlined"
              density="comfortable"
              rows="3"
              hide-details
              class="mb-4"
            ></v-textarea>
          </v-card-text>
        </v-card>


        <!-- Social Media Links Card -->
        <v-card flat>
          <v-card-text>
            <div class="text-subtitle-1 font-weight-medium mb-4">Social Media Links</div>
            
            <v-text-field
              v-for="(link, index) in formData.socialLinks"
              :key="index"
              v-model="formData.socialLinks[index].url"
              :label="link.platform"
              variant="outlined"
              density="comfortable"
              class="mb-4"
              :prepend-inner-icon="link.icon"
              hide-details
              :placeholder="`https://${link.platform.toLowerCase()}.com/username`"
            ></v-text-field>

            <v-btn
              block
              size="large"
              color="blue-darken-3"
              class="mt-6 text-white"
              @click="saveProfile"
              :loading="isLoading"
              :disabled="isLoading"
            >
              {{ formData.user_id ? 'Update Profile' : 'Create Profile' }}
            </v-btn>
            
            <!-- Error message display -->
            <v-alert
              v-if="errorMessage"
              type="error"
              class="mt-4"
              dense
              outlined
            >
              {{ errorMessage }}
            </v-alert>
          </v-card-text>
        </v-card>
      </v-col>


      <!-- Preview Side -->
      <v-col cols="12" md="6" class="d-flex justify-center align-start pt-4">
        <v-btn
          icon
          variant="text"
          class="theme-toggle"
          @click="toggleTheme"
          :title="theme.global.current.value.dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
          <v-icon>{{ theme.global.current.value.dark ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
        </v-btn>
        <div class="preview-container" :class="{ 'dark-theme': isDark, 'light-theme': !isDark }">
          <div class="preview-content">
            <v-avatar size="120" class="mb-4">
              <v-img
                v-if="formData.avatar"
                :src="formData.avatar"
                cover
              ></v-img>
              <v-icon v-else size="60" color="grey-lighten-1">mdi-account-circle</v-icon>
            </v-avatar>

            <h2 class="text-h6 font-weight-bold mb-2">@{{ formData.username || 'username' }}</h2>
            <p class="text-body-2 text-grey-darken-1 mb-6">
              {{ formData.bio || 'Welcome to my bio' }}
            </p>

            <div class="social-preview d-flex justify-center mb-6">
              <v-btn
                v-for="(link, index) in formData.socialLinks"
                :key="index"
                :href="link.url"
                target="_blank"
                icon
                variant="text"
                size="large"
                class="mx-2"
                :disabled="!link.url"
              >
                <v-icon>{{ link.icon }}</v-icon>
              </v-btn>
            </div>


            <v-divider class="my-4"></v-divider>

            <p class="text-caption text-grey">
             Zxjp
            </p>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useTheme } from 'vuetify';
import { useRouter } from 'vue-router';
import { useProfileStore } from '../stores/profile';
import axios from 'axios';

const theme = useTheme();
const router = useRouter();
const profileStore = useProfileStore();

// API base URL - update this to match your PHP API endpoint
const API_BASE_URL = 'http://localhost/i_linktree/api/endpoints';

// Add axios interceptor to handle errors
axios.interceptors.response.use(
  response => response,
  error => {
    const message = error.response?.data?.message || 'An error occurred';
    console.error('API Error:', message);
    return Promise.reject(message);
  }
);

// Toggle between light and dark theme
const toggleTheme = () => {
  theme.global.name.value = theme.global.current.value.dark ? 'light' : 'dark';
};

// Use the profile from the store
const formData = ref({});
const isLoading = ref(false);
const errorMessage = ref('');

// Initialize form data with store data when component mounts
onMounted(() => {
  formData.value = { ...profileStore.profile.value };
});

// Handle file upload
const fileInput = ref(null);
const uploadError = ref('');

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  uploadError.value = ''; // Reset previous error
  
  if (!file) return;
  
  // Check file type
  if (!file.type.startsWith('image/')) {
    uploadError.value = 'Please upload an image file (JPEG, PNG, etc.)';
    return;
  }
  
  // Check file size (2MB max)
  const maxSize = 2 * 1024 * 1024; // 2MB in bytes
  if (file.size > maxSize) {
    uploadError.value = 'Image size should not exceed 2MB';
    return;
  }
  
  // Check image dimensions if needed
  const img = new Image();
  img.onload = function() {
    // Optional: You can add dimension checks here if needed
    // const maxDimension = 2000; // for example
    // if (this.width > maxDimension || this.height > maxDimension) {
    //   uploadError.value = `Image dimensions should not exceed ${maxDimension}x${maxDimension}px`;
    //   return;
    // }
    
    const reader = new FileReader();
    reader.onload = (e) => {
      formData.value.avatar = e.target.result;
    };
    reader.readAsDataURL(file);
  };
  
  img.src = URL.createObjectURL(file);
};

// Prepare form data for API
const prepareFormData = () => {
  const data = {
    username: formData.value.username,
    bio: formData.value.bio || '',
    url: formData.value.socialLinks[0]?.url || '', // Using first social link as main URL
  };

  // Add profile image if available
  if (formData.value.avatar && formData.value.avatar.startsWith('data:image')) {
    data.profile_image = formData.value.avatar.split(',')[1]; // Remove data URL prefix
  }

  return data;
};

// Create or update profile
const saveProfile = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    
    const data = prepareFormData();
    let response;

    if (formData.value.user_id) {
      // Update existing user
      data.user_id = formData.value.user_id;
      response = await axios.post(`${API_BASE_URL}/update.php`, data);
    } else {
      // Create new user
      response = await axios.post(`${API_BASE_URL}/create.php`, data);
      if (response.data.user_id) {
        formData.value.user_id = response.data.user_id;
      }
    }

    // Update the shared profile store
    profileStore.saveProfile(formData.value);
    
    // Navigate to home
    router.push('/');
  } catch (error) {
    console.error('Error saving profile:', error);
    errorMessage.value = typeof error === 'string' ? error : 'Failed to save profile. Please try again.';
  } finally {
    isLoading.value = false;
  }
};

// Reset profile to default values
const resetProfile = () => {
  formData.value = {...defaultProfile};
  localStorage.removeItem('facebookProfile');
};

// Load user profile
const loadProfile = async () => {
  try {
    isLoading.value = true;
    // Check if we have a user ID in localStorage
    const savedProfile = localStorage.getItem('userProfile');
    if (savedProfile) {
      const profile = JSON.parse(savedProfile);
      formData.value = { ...defaultProfile, ...profile };
      
      // If we have a user ID, try to fetch the latest data
      if (profile.user_id) {
        // Note: You might want to implement a get user endpoint
        // const response = await axios.get(`${API_BASE_URL}/get_user.php?id=${profile.user_id}`);
        // formData.value = { ...formData.value, ...response.data };
      }
    } else {
      resetProfile();
    }
  } catch (error) {
    console.error('Error loading profile:', error);
    errorMessage.value = 'Failed to load profile';
  } finally {
    isLoading.value = false;
  }
};

// Load profile when component mounts
onMounted(() => {
  loadProfile();
});
</script>


<style scoped>
.preview-container {
  width: 100%;
  max-width: 375px;
  min-height: 600px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  transition: all 0.3s ease;
  border-radius: 12px;
  background: transparent;
}

.theme-toggle {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 10;
}

/* Ensure text is visible in both themes */
.v-theme--dark .preview-content h2,
.v-theme--dark .preview-content p,
.v-theme--dark .preview-content .v-icon {
  color: rgba(255, 255, 255, 0.87) !important;
}

.v-theme--light .preview-content h2,
.v-theme--light .preview-content p,
.v-theme--light .preview-content .v-icon {
  color: rgba(0, 0, 0, 0.87) !important;
}

/* Light theme */
.light-theme {
  background: transparent;
  color: rgba(0, 0, 0, 0.87);
}

.light-theme .text-grey {
  color: rgba(0, 0, 0, 0.6) !important;
}

/* Dark theme */
.dark-theme {
  background: transparent;
  color: rgba(255, 255, 255, 0.87);
}

.dark-theme .text-grey-darken-1 {
  color: rgba(255, 255, 255, 0.7) !important;
}

.dark-theme .text-grey {
  color: rgba(255, 255, 255, 0.5) !important;
}

.dark-theme .v-divider {
  border-color: rgba(255, 255, 255, 0.12) !important;
}

.dark-theme .v-btn--icon {
  color: rgba(255, 255, 255, 0.7) !important;
}

.dark-theme .v-btn--disabled {
  color: rgba(255, 255, 255, 0.3) !important;
}

.preview-content {
  width: 100%;
  text-align: center;
}

.social-preview {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 24px;
}

/* Responsive adjustments */
@media (max-width: 960px) {
  .preview-container {
    margin-top: 32px;
    margin-bottom: 32px;
  }
}
</style>
