<!-- POTREBUJEM GETNUT -> Meno, Priezvisko, Email a LastLogin -->

<template>
    <div>
        <AuthNavBar />

        <Button v-if="isForgotten" id="back" icon="pi pi-arrow-left" @click="goBack" />


        <div class="d-flex justify-content-center" v-if="!isForgotten">
            <div class="d-flex align-items-center flex-column" id="box">
                <div class="d-flex justify-content-center" id="box-icon"
                    style="background: linear-gradient(to bottom, #8B5CF6ee 0%, #8B5CF6ee 50%, transparent 50%, transparent 100%);">

                    <lord-icon id="icon" src="https://cdn.lordicon.com/xcxzayqr.json" trigger="hover"
                        colors="primary:#692CF3,secondary:#f4c89c" style="width:14rem;height:14rem;">
                    </lord-icon>

                </div>

                <div class="w-100" v-if="!isFetchingData">
                  <h1 class="display-4 text-center mb-1">{{ $t('hello') }} {{ user.name }}!</h1>

                  <div class="content-box">
                      <p class="header-p">{{ $t('entitlement_level') }}</p>
                      <p class="content-p">{{ userRole }}</p>
                  </div>


                  <div class="content-box">
                      <p class="header-p">{{ $t('email_address') }}</p>
                      <p class="content-p">{{ user.email }}</p>
                  </div>

                  <div class="content-box">
                      <p class="header-p">{{ $t('last_activity') }}</p>
                      <p class="content-p">{{ user.last_login }}</p>
                  </div>

                  <div class="d-flex flex-column justify-content-center">
                    <span class="fw-bold btn-login text-center" @click="lostPassword">{{ $t('forgotten_password') }}</span>


                    <lord-icon src="https://cdn.lordicon.com/xyboiuok.json" trigger="loop" state="morph-heart" delay="1000"
                        colors="primary:#a866ee" style="width:100%;height:45px">
                    </lord-icon>
                  </div>
                </div>

                <div v-else class="mt-5">
                  <lord-icon
                      src="https://cdn.lordicon.com/unukghxb.json"
                      trigger="loop"
                      stroke="bold"
                      state="loop-spin"
                      colors="primary:#ffffff,secondary:#7c3aed"
                      style="width:8em;height:8em">
                  </lord-icon>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center flex-column" v-if="isForgotten">

            <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
                <h1 id="header-change" class="roboto-black h1 text-center">{{ $t('up_change_password') }}</h1>
                <h4 class="text-center">{{ $t('up_user') }}</h4>
            </div>

            <div class="change-password">
                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password v-model="password" @click="checkPasswords" :invalid="!passwordsMatch"
                        :placeholder="$t('password')" toggleMask />
                </InputGroup>

                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password v-model="confirmPassword" @click="checkPasswords" :invalid="!passwordsMatch"
                        :placeholder="$t('confirm_password')" toggleMask />
                </InputGroup>

                <Button @click="resetPassword($t('lang_id'))" type="submit" label="Registrácia">{{ $t('change_password') }}<lord-icon
                        v-if="!isLoading" src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover"
                        colors="primary:#ffffff" style="width:2em;height:2em;margin-left:1em;">
                    </lord-icon><lord-icon v-else src="https://cdn.lordicon.com/lqxfrxad.json" trigger="loop"
                        delay="200" colors="primary:#ffffff" style="width:2em;height:2em;margin-left: 1em;">
                    </lord-icon></Button>
            </div>
            <Toast />
        </div>
    </div>
</template>

<script setup>
import AuthNavBar from '@/components/AuthNavBar.vue';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { ref, watch, onMounted, computed } from 'vue';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { auth_fetch } from '@/utils';

const isForgotten = ref(false);
const password = ref(null)
const confirmPassword = ref(null)
const passwordsMatch = ref(true)
const isFormSubmitted = ref(false)
const isLoading = ref(false);
const toast = useToast();
const userName = ref('');
const user = ref([]);
const isFetchingData = ref(false);

const userRole = computed(() => {
    switch (user.value.auth_level) {
        case 2:
            return 'Admin';
        case 1:
            return 'Basic';
        case -1:
            return 'Blocked';
        default:
            return 'Unknown';
    }
});






onMounted(async () => {
    //authLevel.value = getLocalStorage("accessLevel");

    isFetchingData.value = true;
    const response = await initialGetFetch();
    isFetchingData.value = false;

    if (!response.ok) {
        const data = await response.json();
        //showError(data.error);
    } else {
        const data = await response.json();
        user.value = data.user;
    }
});

const initialGetFetch = async () => {
    return (await auth_fetch('/user/profile'));
}


watch([password, confirmPassword], () => {
    checkPasswords();
})

const goBack = () => {
    isForgotten.value = false;
}

const lostPassword = () => {
    isForgotten.value = true;
}

const checkPasswords = () => {
    if (password.value !== confirmPassword.value || (!password.value && !confirmPassword.value)) {
        passwordsMatch.value = false;
    } else {
        if (password.value.length < 5) {
            passwordsMatch.value = false;
        } else {
            passwordsMatch.value = true;
        }
    }
}

const showSuccess = (successMessage, lang) => {
  if(lang === 'sk') {
    toast.add({ severity: 'success', summary: 'Úspech', detail: "Operácia prebehla úspešne", life: 5000 });
  } else {
    toast.add({ severity: 'success', summary: 'Success', detail: successMessage, life: 5000 });
  }
};

const showError = (errorMessage, lang) => {
  if(lang === 'sk') {
    toast.add({ severity: 'error', summary: 'Chybové hlásenie', detail: "Niečo sa pokazilo.", life: 3000 });
  } else {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
  }
};

const resetPassword = async (lang) => {
    isLoading.value = true;
    if (passwordsMatch.value == true) {
        const response = await auth_fetch('/change-password/reset-password', 'POST', { password: password.value });

        if (!response.ok) {
            isLoading.value = false;
            const data = await response.json();
            showError(data.error, lang);
        } else {
            password.value = null;
            confirmPassword.value = null;
            isFormSubmitted.value = true;
            const data = await response.json();
            showSuccess(data.message, lang);
        }
    }
    isLoading.value = false;
}

</script>

<style scoped>
#back {
    position: absolute;
    top: 0;
    left: 0;
    margin: .5rem;
}

.p-inputgroup {
    margin: 1rem !important;
    width: calc(100% - 2rem) !important;
}

h4 {
    text-decoration: underline dotted #8B5CF6aa;
    color: #8B5CF6ee;
}

.change-password {
    width: 50%;
    box-sizing: border-box !important;
    border-radius: 10px;
    box-shadow: 0 0.5em 1em #8B5CF6aa;
    display: flex;
    align-items: center;
    flex-direction: column;
    margin-top: 3em;
}

@media (max-width: 930px) {
    .change-password {
        width: 80%;
    }
}


.content-box {
    width: 70%;
    border-bottom: 1px solid #aaaaaa;
    margin-left: 15%;
    margin-right: 15%;
    margin-top: 3%;
}

.header-p {
    color: #a866ee;
    margin-bottom: 0;
    font-size: 0.9rem;
}

.content-p {
    margin-bottom: .5rem;
    font-size: 1.25rem;
}

#header-change {
    color: #8b5cf6;
    margin-top: 0;
    margin-bottom: .5rem;
}

#box-icon {
    width: 100%;
    padding-top: 2vh;
    border-top-left-radius: 2rem;
    border-top-right-radius: 2rem;
}

#icon {
    border-radius: 50%;
    overflow: hidden;
    background-color: #f1f1f1ee;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

Button {
    border-radius: 1em;
    margin-bottom: 1rem;
}

#box {
    background-color: white;
    margin-top: 5vh;
    border-radius: 2rem;
    width: 50%;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

h1 {
    color: rgba(0, 0, 0, 0.8);
    margin-top: .5rem;
    margin-bottom: 0;
}

.btn-login {
    transition: 0.3s ease;
    padding: 0.8rem 1rem;
    color: var(--primary-color)
}

.btn-login:hover {
    color: var(--secondary-color);
    cursor: pointer;
}

@media (max-width: 768px) {
    #box {
        width: 70%;
    }

    #icon {
        width: 10rem !important;
        height: 10rem !important;
    }

    .content-box {
        margin-left: 10%;
        margin-right: 10%;
        width: 80%;
    }
}

@media (max-width: 520px) {
    #box {
        width: 90%;
    }
}

@media (max-width: 480px) {
    #icon {
        width: 7rem !important;
        height: 7rem !important;
    }

    .content-p {
        font-size: 1rem;
    }
}
</style>