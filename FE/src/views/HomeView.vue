<template>
  <div>
    <DefaultNavBar />
    <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
      <h1 class="roboto-black">Zadajte kód</h1>
      <InputOtp v-model="code" integerOnly :length="5" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import InputOtp from 'primevue/inputotp';
import DefaultNavBar from '../components/DefaultNavBar.vue';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';


const code = ref(null)
</script>
