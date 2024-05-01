<template>
  <div>
    <div>
      <DefaultNavBar />
      <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
        <h1 class="roboto-black h1">PRIHLÁSENIE</h1>
        <h4>EXISTUJÚCI POUŽÍVATEĽ</h4>
      </div>
    </div>

    <div class="d-flex align-items-center flex-column">
      <div class="box">
        <InputGroup>
          <InputGroupAddon>
            <i class="pi pi-at"></i>
          </InputGroupAddon>
          <InputText v-model="email" @click="validateEmail" :invalid="!isEmailValid" placeholder="Email" name="email" />
        </InputGroup>

        <InputGroup>
          <InputGroupAddon>
            <i class="pi pi-lock"></i>
          </InputGroupAddon>
          <Password v-model="password" @click="checkPasswords" :invalid="!isValidPassword" placeholder="Password"
            toggleMask />
        </InputGroup>

        <Button @click="submitForm" type="submit" label="Prihlásenie">Prihlásiť <lord-icon
            src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#ffffff"
            style="width:2em;height:2em;margin-left:1em;">
          </lord-icon></Button>
        <Toast />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import DefaultNavBar from '../components/DefaultNavBar.vue';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const password = ref(null)
const isValidPassword = ref(true);
const email = ref(null);
const isEmailValid = ref(true);
const toast = useToast();

watch([email], () => {
  validateEmail();
})

watch([password], () => {
  checkPasswords();
})

const validateEmail = () => {
  const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
  if (email.value) {
    isEmailValid.value = emailRegex.test(email.value);
  } else {
    isEmailValid.value = false;
  }
}

const checkPasswords = () => {
  if (password.value) {
    if (password.value.length < 5) {
      isValidPassword.value = false;
    } else {
      isValidPassword.value = true;
    }
  } else {
    isValidPassword.value = false;
  }
}

const submitForm = async () => {
  if (isEmailValid.value == true && isValidPassword.value == true) {
    const response = await fetch('https://node17.webte.fei.stuba.sk/final/auth/login.php', {
      method: 'POST',
      body: JSON.stringify({
        email: email.value,
        password: password.value
      })
    });

    if (!response.ok) {
      showError();
    } else {
      const data = await response.json();
      localStorage.setItem('accessToken', data.accessToken);
      showSuccess();
    }
  }
}

const showSuccess = () => {
  toast.add({ severity: 'success', summary: 'Success Message', detail: 'Form submitted ! To activate your account, please visit your email inbox and activate your account.', life: 5000 });
};

const showError = () => {
  toast.add({ severity: 'error', summary: 'Error Message', detail: 'Form was not submitted !', life: 3000 });
};

</script>

<style scoped>
h4 {
  text-decoration: underline dotted #8B5CF6aa;
  color: #8B5CF6ee;
}

.p-inputgroup {
  margin: 1rem !important;
  width: calc(100% - 2rem) !important;
}

Button {
  border-radius: 1em;
  margin-bottom: 1rem;
}
</style>