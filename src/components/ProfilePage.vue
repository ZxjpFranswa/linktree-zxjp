<template>
  <div>
    <!-- Header with theme toggle and create button -->
    <div class="d-flex justify-end align-center w-100 pa-4">
      <v-btn icon variant="text" @click="toggleTheme">
          <v-icon>{{ theme.global.current.value.dark ? 'mdi-weather-night' : 'mdi-weather-sunny' }}</v-icon>
      </v-btn>
      <v-btn 
        color="grey-darken-3" 
        class="ml-2 text-white"
        @click="navigateToEditor"
      >
        + Create / Edit
      </v-btn>
    </div>

    <!-- Main Content -->
    <v-container class="fill-height d-flex flex-column justify-center align-center">
      <div class="text-center">
        <!-- Profile Picture -->
        <v-avatar size="120" class="mb-4">
          <v-img
            :src="profile.avatar || 'https://via.placeholder.com/150'"
            alt="Profile"
            cover
          ></v-img>
        </v-avatar>

        <!-- Username -->
        <h1 class="text-h5 font-weight-bold mb-2">@{{ profile.username }}</h1>
        
        <!-- Bio -->
        <p class="text-body-1 text-grey-darken-1 mb-6" style="max-width: 400px;">
          {{ profile.bio }}
        </p>

        <!-- Social Icons -->
        <div class="d-flex justify-center mb-6">
          <v-btn
            v-for="(social, index) in profile.socialLinks"
            :key="index"
            :href="social.url"
            target="_blank"
            icon
            variant="text"
            size="large"
            class="mx-2"
          >
            <v-icon>{{ social.icon }}</v-icon>
          </v-btn>
        </div>

        <v-divider class="my-4"></v-divider>

        <!-- Footer -->
        <p class="text-caption text-grey">
          Zxjp
        </p>
      </div>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTheme } from 'vuetify';
import { useRouter } from 'vue-router';
import { useProfileStore } from '../stores/profile';

const theme = useTheme();
const router = useRouter();
const profileStore = useProfileStore();

// Use the profile from the store
const profile = profileStore.profile;

// Navigate to editor page
const navigateToEditor = () => {
  router.push('/editorpage');
};

// Load profile when component mounts
onMounted(() => {
  profileStore.loadProfile();
});

// Toggle between light and dark theme
const toggleTheme = () => {
  theme.global.name.value = theme.global.current.value.dark ? 'light' : 'dark';
};
</script>

<style scoped>
/* Add any custom styles here */
.v-avatar {
  border: 3px solid #f5f5f5;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

/* Ensure proper theming */
:deep(.v-application) {
  background-color: #ffffff !important;
}

/* For dark theme */
:deep(.v-theme--dark) {
  background-color: #121212 !important;
}

/* Fix for v-container fill height */
.v-container {
  min-height: calc(100vh - 64px);
  display: flex;
  flex-direction: column;
  justify-content: center;
}
</style>
