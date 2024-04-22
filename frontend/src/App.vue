<script setup lang="ts">
import { RouterView } from 'vue-router'
import TheHeader from '@/components/TheHeader.vue'
</script>

<template>
  <header>
    <TheHeader/>
  </header>
  <div class="container-fluid mx-2">
    <RouterView />
  </div>
  
</template>

<style scoped>

</style>
