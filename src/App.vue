<template>
  <v-app>
    <router-view />
  </v-app>
</template>

<script setup>
import { provideProfileStore } from './stores/profile';

// Provide the profile store to all child components
provideProfileStore();
</script>
