<template>
  <div class="w-100">
    <div class="container">
      <nav class="d-flex justify-content-end p-2">
        <span class="fw-bold btn-login" v-on:click="$router.push('/login')">Prihlásiť sa</span>
        <router-link to="/registration">
          <Button class="ml-5" label="Registrácia" />
        </router-link>
      </nav>
    </div>
  </div>
</template>

<script setup>
//import router from '@/router';
import Button from 'primevue/button';
</script>

<style scoped>
Button {
  border-radius: 1em;
}

.btn-login {
  transition: 0.3s ease;
  padding: 0.5rem 1rem;
  color: var(--primary-color)
}

.btn-login:hover {
  color: var(--secondary-color);
  cursor: pointer;
}
</style>
