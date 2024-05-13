<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { store } from './store.js';
</script>

<template>
  <div>
    <router-view v-slot="{ Component }">
      <Transition name="fade">
        <component :is="Component" />
      </Transition>
    </router-view>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
  opacity: 1;
}

.fade-leave-to {
  width: 100%;
  position: absolute;
}
</style>
