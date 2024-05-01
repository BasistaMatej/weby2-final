<script setup>
import { RouterLink, RouterView } from 'vue-router'
import { store } from './store.js';
</script>

<template>
  <div>
  <!-- <header>
      <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />

      <div class="wrapper">
        <nav>
              <RouterLink to="/">Home</RouterLink>
              <RouterLink to="/about">About</RouterLink>
            </nav>
          </div>
        </header> -->

    <RouterView v-if="store.renderComponent" />
  </div>
</template>

<style scoped>
/* Some scoped style */
</style>
