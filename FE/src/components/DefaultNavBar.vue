<template>
  <div class="w-100">
    <div class="container">
      <nav v-if="!isMobile" class="d-flex justify-content-end p-2">
        <div class="d-flex justify-content-between w-100">
          <div class="py-1">
            <router-link to="/auth">
              <img src="/favicon-128.png" style="max-height: 3em">
            </router-link>
          </div>
          <div>
        <span class="fw-bold btn-login" v-on:click="$router.push('/login')">{{ $t('login') }}</span>
        <Button class="ml-5" @click="registrationShow" >{{ $t('registration') }}</Button>
        <Button class="ml-5 btn-manual" @click="$router.push('/manual')" outlined  >{{ $t('user_manual') }}</Button>
        <dropdown :options="$i18n.availableLocales" v-model="$i18n.locale" optionKey="locale" class="dropdown" />
        </div>
      </div>
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
                <Router-link class="nav-link" to="/">{{ $t('home') }}</Router-link>
              </li>
              <li class="nav-item">
                <Router-link class="nav-link" to="/login">{{ $t('login') }}</Router-link>
              </li>
              <li class="nav-item">
                <span class="nav-link" @click="registrationShow">{{ $t('registration') }}</span>
              </li>
              <li class="nav-item">
                <dropdown :options="$i18n.availableLocales" v-model="$i18n.locale" optionKey="locale"
                  class="dropdown" />
              </li>
              <li class="nav-item">
                <Router-link class="nav-link" to="/manual">{{ $t('user_manual') }}</Router-link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { store } from '@/store';
import Button from 'primevue/button';
import { useRouter } from 'vue-router';
import Dropdown from 'primevue/dropdown';
import '@/assets/dropdown.css';

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

.dropdown,.btn-manual {
  margin-left: 1rem;
}

@media (max-width: 500px) {
  .dropdown {
    margin-left: 0;
  }
}
</style>
