<template>
    <div>
        <DefaultNavBar />
        <Toast />
        <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
            <h1 id="header-change" class="roboto-black h1 text-center">{{ $t('up_reset_password') }}</h1>
            <h4 class="text-center">{{ $t('up_user') }}</h4>
        </div>

        <div class="d-flex align-items-center flex-column" v-if="!isForgotten && !everythingOkay">
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

                <Button @click="submitForm" type="submit" label="Registrácia">{{ $t('change_password') }}<lord-icon
                        v-if="!isLoading" src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover"
                        colors="primary:#ffffff" style="width:2em;height:2em;margin-left:1em;">
                    </lord-icon><lord-icon v-else src="https://cdn.lordicon.com/lqxfrxad.json" trigger="loop"
                        delay="200" colors="primary:#ffffff" style="width:2em;height:2em;margin-left: 1em;">
                    </lord-icon></Button>
            </div>
        </div>

        <div class="d-flex justify-content-center">
            <div class="box p-3" v-if="everythingOkay">
                <lord-icon src="https://cdn.lordicon.com/utpmnzxz.json" trigger="loop" delay="2000"
                    colors="primary:#7c3aed" style="width:200px;height:200px">
                </lord-icon>
                <h1 class="roboto-black text-center"> {{ $t('change_password_success') }}</h1>
                <p class="text-center">
                    {{ $t('success_email_validation') }}
                    <router-link to="/login" class="router-link">Log in</router-link>
                </p>
            </div>
        </div>

    </div>
</template>

<script setup>
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Password from 'primevue/password';
import Button from 'primevue/button';
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import DefaultNavBar from '../components/DefaultNavBar.vue';

const route = useRoute();
const isForgotten = ref(false);
const password = ref(null)
const confirmPassword = ref(null)
const passwordsMatch = ref(true)
const isFormSubmitted = ref(false)
const isLoading = ref(false);
const toast = useToast();
const everythingOkay = ref(false);

watch([password, confirmPassword], () => {
    checkPasswords();
})

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

const showError = (errorMessage) => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

const submitForm = async () => {
    isLoading.value = true;
    if (passwordsMatch.value == true) {
        const response = await fetch('http://node17.webte.fei.stuba.sk:5151/change-password/reset-password', {
            method: 'POST',
            body: JSON.stringify({
                password: password.value
            }),
            headers: {
                'AUTHORIZATION': 'Bearer ' + route.params.token
            }
        });

        if (!response.ok) {
            isLoading.value = false;
            const data = await response.json();
            console.log(data.error);
            showError(data.error);
        } else {
            password.value = null;
            confirmPassword.value = null;
            isFormSubmitted.value = true;
            everythingOkay.value = true;
        }
    }
    isLoading.value = false;
}

</script>

<style scoped>
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

.p-inputgroup {
    margin: 1rem !important;
    width: calc(100% - 2rem) !important;
}

Button {
    border-radius: 1em;
    margin-bottom: 1rem;
}

#header-change {
    color: #8b5cf6;
    margin-top: 0;
    margin-bottom: .5rem;
}

h4 {
    text-decoration: underline dotted #8B5CF6aa;
    color: #8B5CF6ee;
}

@media (max-width: 930px) {
    .change-password {
        width: 80%;
    }
}
</style>