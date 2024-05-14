<template>
  <div>
    <DefaultNavBar />
    <div class="container">
      <div class="d-flex align-items-center flex-column text-center">
        <div class="box p-3">
          <lord-icon v-if="!isError" src="https://cdn.lordicon.com/aycieyht.json" trigger="loop"
            colors="primary:#7c3aed,secondary:#08a88a" style="width:200px;height:200px">
          </lord-icon>
          <lord-icon v-else src="https://cdn.lordicon.com/svpafkqj.json" trigger="in" delay="20"
            colors="primary:#000000,secondary:#7c3aed" style="width:200px;height:200px">
          </lord-icon>
          <div v-if="dataText">
            <h1 class="roboto-black">{{ dataText }}</h1>
            <div>
              <p v-if="!isError">
                {{ $t('success_email_validation') }}
                <router-link to="/login" class="router-link"> {{ $t('login') }}</router-link>
              </p>
              <p v-else>
                {{ $t('error_email_validation') }}
              </p>
            </div>
          </div>
          <p v-else>{{ $t('email_validation') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import DefaultNavBar from '../components/DefaultNavBar.vue';

const route = useRoute();
const dataText = ref(null);
const isError = ref(false);

onMounted(async () => {
  try {
    const response = await fetch('http://node17.webte.fei.stuba.sk:5151/registration', {
      method: 'GET',
      headers: {
        'AUTHORIZATION': 'Bearer ' + route.params.token
      }
    });
    const data = await response.json();

    if (!response.ok) {
      isError.value = true;
      dataText.value = data.error;
      return;
    } else {
      isError.value = false;
    }
    dataText.value = data.message;
  } catch (error) {
    isError.value = true;
    dataText.value = 'Nepodarilo sa overiť e-mail. Skúste to prosím neskôr.';
  }
});
</script>


<style scoped>
.router-link {
  color: #7c3aed;
  border-bottom: 1px solid #7c3aed;
  text-decoration: none;
  transition: 0.3s ease;
}

.router-link:hover {
  color: #222;
  border-bottom: 1px solid #222;
}
</style>