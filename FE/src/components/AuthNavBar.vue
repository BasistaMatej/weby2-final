<template>
  <div class="w-100">
    <div class="container">
      <nav class="d-flex justify-content-end p-2">
        <span class="fw-bold btn-login" v-on:click="$router.push('/profile')">Profil</span>
        <Button class="ml-5" label="Odhlasiť sa" @click="registrationShow" />
      </nav>
    </div>
  </div>
</template>

<script setup>
import Button from 'primevue/button';
import { useRouter } from 'vue-router';

const router = useRouter();

const registrationShow = async () => {
  // delete token from browser memory
}
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
