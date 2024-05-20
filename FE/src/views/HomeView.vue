<template>
  <div>
    <DefaultNavBar />
    <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
      <h1 class="roboto-black">{{$t('enter_code')}}</h1>
      <InputOtp v-model="code" :length="5" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import InputOtp from 'primevue/inputotp';
import { useRouter } from 'vue-router';
import DefaultNavBar from '../components/DefaultNavBar.vue';

const router = useRouter();
const code = ref(null)

watch(code, (newVal) => {
  if(newVal.length === 5) {
    router.push("/"+newVal);
  }
})
</script>
