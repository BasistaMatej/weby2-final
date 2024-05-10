<template>
  <div class="w-100">
    <div class="container">
      <nav class="d-flex justify-content-end p-2">
        <span class="fw-bold btn-login" v-on:click="$router.push('/login')">{{$t('login')}}</span>
        <Button class="ml-5" @click="registrationShow">{{$t('registration')}}</Button>
        <dropdown :options="$i18n.availableLocales" v-model="$i18n.locale" optionKey="locale" class="dropdown"/>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { nextTick } from 'vue';
import { store } from '@/store';
import Button from 'primevue/button';
import { useRouter } from 'vue-router';
import Dropdown from 'primevue/dropdown';


const router = useRouter();

const registrationShow = async () => {
  const currentRoute = router.currentRoute.value;
  if (currentRoute.path === '/registration') {
    store.renderComponent = false;
    await nextTick();
    store.renderComponent = true;
  } else {
    router.push('/registration');
  }
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

.dropdown {
  margin-left: 1rem;
}
</style>
