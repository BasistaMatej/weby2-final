<template>
  <div class="w-100">
    <div class="container">
      <nav v-if="!isMobile" class="d-flex justify-content-end p-2">
        <span class="fw-bold btn-login" v-on:click="$router.push('/login')">{{ $t('login') }}</span>
        <Button class="ml-5" @click="registrationShow" >{{ $t('registration') }}</Button>
        <Button class="ml-5 btn-home" @click="$router.push('/')" outlined  >{{$t('home')}}</Button>
        <dropdown :options="$i18n.availableLocales" v-model="$i18n.locale" optionKey="locale" class="dropdown" />
      </nav>


      <nav v-else class="navbar navbar-expand-lg bg-body-tertiary w-100" id="mainNav">
        <div class="container-fluid w-100 mb-4">
          <a class="navbar-brand"><strong></strong></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span></span>☰</button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <Router-link class="nav-link" to="/">{{$t('home')}}</Router-link>
              </li>
              <li class="nav-item">
                <dropdown :options="$i18n.availableLocales" v-model="$i18n.locale" optionKey="locale"
                  class="dropdown" />
              </li>
            </ul>
          </div>
        </div>
      </nav>



    </div>
  </div>
</template>

<script setup>
import {nextTick, ref} from 'vue';
import Button from 'primevue/button';
import { useRouter } from 'vue-router';
import Dropdown from 'primevue/dropdown';
import '@/assets/dropdown.css';
import {store} from "@/store.js";

const router = useRouter();
const isMobile = ref(window.innerWidth < 500);

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth < 500;
});

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
.navbar-toggler {
  border: none;
  color: black;
}

.navbar-toggler:focus {
  text-decoration: none;
  outline: 0;
  box-shadow: 0 0 0 0;
}

.nav-link {
  color: black;
}

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

.dropdown,.btn-home {
  margin-left: 1rem;
}

@media (max-width: 500px) {
  .dropdown {
    margin-left: 0;
  }
}
</style>
