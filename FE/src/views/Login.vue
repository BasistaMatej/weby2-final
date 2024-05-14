<template>
    <div>
        <div>
            <DefaultNavBar />
            <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
                <h1 class="roboto-black h1 text-center">{{ $t('up_login') }}</h1>
                <h4 class="text-center">{{ $t('up_ex_user') }}</h4>
            </div>
        </div>

        <Button v-if="isForgotten && !isEmailFormSubmitted" id="back" icon="pi pi-arrow-left" @click="goBack" />

        <div class="d-flex align-items-center flex-column">
            <div class="box" v-if="!isFormSubmitted && !isForgotten && !isEmailFormSubmitted">
                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-at"></i>
                    </InputGroupAddon>
                    <InputText v-model="email" @click="validateEmail" :invalid="!isEmailValid" placeholder="Email"
                        name="email" />
                </InputGroup>

                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password v-model="password" @click="checkPasswords" :invalid="!isValidPassword"
                        :placeholder="$t('password')" toggleMask :feedback="false" />
                </InputGroup>

                <div id="button-box" class="d-flex">
                    <span class="fw-bold btn-login text-center" @click="lostPassword">{{ $t('forgotten_password')
                        }}</span>
                    <Button @click="submitForm" type="submit" label="Registrácia"> {{ $t('login') }} <lord-icon
                            v-if="!isLoading" src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover"
                            colors="primary:#ffffff" style="width:2em;height:2em;margin-left:1em;">
                        </lord-icon><lord-icon v-else src="https://cdn.lordicon.com/lqxfrxad.json" trigger="loop"
                            delay="200" colors="primary:#ffffff" style="width:2em;height:2em;margin-left: 1em;">
                        </lord-icon></Button>
                </div>
            </div>

            <div class="box" v-if="isForgotten && !isEmailFormSubmitted">
                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-at"></i>
                    </InputGroupAddon>
                    <InputText v-model="email" @click="validateEmail" :invalid="!isEmailValid" placeholder="Email"
                        name="email" />
                </InputGroup>

                <div id="button-box" class="d-flex">

                    <Button @click="submitFormPassword" type="submit" label="Registrácia">{{ $t('submit') }} <lord-icon
                            v-if="!isLoading" src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover"
                            colors="primary:#ffffff" style="width:2em;height:2em;margin-left:1em;">
                        </lord-icon><lord-icon v-else src="https://cdn.lordicon.com/lqxfrxad.json" trigger="loop"
                            delay="200" colors="primary:#ffffff" style="width:2em;height:2em;margin-left: 1em;">
                        </lord-icon></Button>

                </div>
            </div>

            <div class="box p-3 text-center" v-if="isFormSubmitted && !isEmailFormSubmitted">
                <h1 class="mt-4">{{ $t('one_more_step') }}</h1>
                <lord-icon src="https://cdn.lordicon.com/kddybgok.json" trigger="loop" delay="200"
                    colors="primary:#8b5cf6" style="width:8rem;height:15rem">
                </lord-icon>
            </div>

            <div class="box p-3 text-center" v-if="isEmailFormSubmitted">
                <h1 class="mt-4">{{ $t('password_reset_email_confirm') }}</h1>
                <lord-icon src="https://cdn.lordicon.com/nzixoeyk.json" trigger="loop" delay="500"
                    colors="primary:#8b5cf6" style="width:8rem;height:15rem">
                </lord-icon>
            </div>
        </div>
        <Toast />
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import DefaultNavBar from '../components/DefaultNavBar.vue';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { useRouter, useRoute } from 'vue-router';
import { setLocalStorage } from '@/utils';

const router = useRouter();
const password = ref(null)
const isValidPassword = ref(true);
const email = ref(null);
const isEmailValid = ref(true);
const toast = useToast();
const isFormSubmitted = ref(false);
const isLoading = ref(false);
const isForgotten = ref(false);
const isEmailFormSubmitted = ref(false);
const route = useRoute();
const message = ref(route.query.message);

watch([email], () => {
    validateEmail();
})

watch([password], () => {
    checkPasswords();
})

const goBack = () => {
    console.log("clicked!!!");
    isForgotten.value = false;
    isEmailFormSubmitted.value = false;
    isFormSubmitted.value = false;
}

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



//WORK-IN-PROGRESS => ZMENA HESLA
const submitFormPassword = async () => {
    isLoading.value = true;
    if (isEmailValid.value == true) {
        const response = await fetch('http://node17.webte.fei.stuba.sk:5151/change-password/request-reset', {
            method: 'POST',

            body: JSON.stringify({
                email: email.value
            })
        });

        if (!response.ok) {
            isLoading.value = false;
            const data = await response.json();
            showError(data.error);
        } else {
            isEmailFormSubmitted.value = true;
            showSuccess();
        }
    }
    isLoading.value = false;
}

const submitForm = async () => {
    isLoading.value = true;
    if (isEmailValid.value == true && isValidPassword.value == true) {
        const response = await fetch('http://node17.webte.fei.stuba.sk:5151/login', {
            method: 'POST',
            body: JSON.stringify({
                email: email.value,
                password: password.value
            })
        });

        if (!response.ok) {
            isLoading.value = false;
            const data = await response.json();
            showError(data.error);
        } else {
            const data = await response.json();
            setLocalStorage('accessToken', data.accessToken);
            setLocalStorage('refreshToken', data.refreshToken);
            isFormSubmitted.value = true;
            setTimeout(() => {
                router.push('/auth');
            }, 1500);
        }
    }
    isLoading.value = false;
}

const showSuccess = (successMessage) => {
    toast.add({ severity: 'success', summary: 'Success', detail: successMessage, life: 5000 });
};

const showError = (errorMessage) => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

//NEROZUMIEM PRECO NEZOBRAZI TOAST SO SPRAVOU :(
/*if (message.value) {
    //console.log(message.value);
    //console.log(message.value);
    showSuccess(message.value);
    toast.add({ severity: 'success', summary: 'Success', detail: message.value, life: 5000 });
    //showSuccess(message.value);
}*/

const lostPassword = () => {
    isForgotten.value = true;
}
</script>

<style scoped>
#back {
    position: absolute;
    top: 0;
    left: 0;
    margin: .5rem;
}

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

.btn-login {
    transition: 0.3s ease;
    padding: 0.8rem 1rem;
    color: var(--primary-color)
}

.btn-login:hover {
    color: var(--secondary-color);
    cursor: pointer;
}

@media (max-width: 430px) {
    #button-box {
        flex-direction: column;
    }
}
</style>