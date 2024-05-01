<!-- _IMPORTANT_ Podstránka s registračným formulárom -->

<template>
    <div>
        <div>
            <DefaultNavBar />
            <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
                <h1 class="roboto-black h1 text-center">REGISTRÁCIA</h1>
                <h4 class="text-center">NOVÝ POUŽÍVATEĽ</h4>
            </div>
        </div>

        <div class="d-flex align-items-center flex-column">
            <div class="box" v-if="!isFormSubmitted">
                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-user"></i>
                    </InputGroupAddon>
                    <InputText v-model="name" :invalid="!isNameValid" placeholder="Name" name="name"
                        @click="validateName" />
                </InputGroup>

                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-user"></i>
                    </InputGroupAddon>
                    <InputText v-model="surname" @click="validateSurname" :invalid="!isSurnameValid"
                        placeholder="Surname" name="surname" />
                </InputGroup>

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
                    <Password v-model="password" @click="checkPasswords" :invalid="!passwordsMatch"
                        placeholder="Password" toggleMask />
                </InputGroup>

                <InputGroup>
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password v-model="confirmPassword" @click="checkPasswords" :invalid="!passwordsMatch"
                        placeholder="Confirm Password" toggleMask />
                </InputGroup>
                <Button @click="submitForm" type="submit" label="Registrácia">Registrovať <lord-icon v-if="!isLoading"
                        src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#ffffff"
                        style="width:2em;height:2em;margin-left:1em;">
                    </lord-icon><lord-icon v-else src="https://cdn.lordicon.com/lqxfrxad.json" trigger="loop"
                        delay="200" colors="primary:#ffffff" style="width:2em;height:2em;margin-left: 1em;">
                    </lord-icon></Button>
            </div>

            <div class="box p-3 text-center" v-if="isFormSubmitted">
                <h1 class="mt-4">Potvrdenie registrácie je na vašom e-maily !</h1>
                <lord-icon src="https://cdn.lordicon.com/nzixoeyk.json" trigger="loop" delay="500"
                    colors="primary:#8b5cf6" style="width:8rem;height:15rem">
                </lord-icon>
            </div>
        </div>

        <Toast />
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
const confirmPassword = ref(null)
const passwordsMatch = ref(true)
const email = ref(null);
const isEmailValid = ref(true);
const name = ref(null)
const isNameValid = ref(true)
const surname = ref(null)
const isSurnameValid = ref(true)
const toast = useToast();
const isFormSubmitted = ref(false)
const isLoading = ref(false);


watch([password, confirmPassword], () => {
    checkPasswords();
})

watch([email], () => {
    validateEmail();
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

const validateEmail = () => {
    const emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    if (email.value) {
        isEmailValid.value = emailRegex.test(email.value);
    } else {
        isEmailValid.value = false;
    }
}

const validateName = () => {
    const nameSurnameRegex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/g;
    if (name.value) {
        isNameValid.value = nameSurnameRegex.test(name.value);
    } else {
        isNameValid.value = false;
    }
}

const validateSurname = () => {
    const nameSurnameRegex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/g;
    if (surname.value) {
        isSurnameValid.value = nameSurnameRegex.test(surname.value);
    } else {
        isSurnameValid.value = false;
    }
}

watch(name, () => {
    validateName();
})

watch(surname, () => {
    validateSurname();
})

const showSuccess = (successMessage) => {
    toast.add({ severity: 'success', summary: 'Success', detail: successMessage, life: 5000 });
};

const showError = (errorMessage) => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

const submitForm = async () => {
    isLoading.value = true;
    if (isEmailValid.value == true && isNameValid.value == true && isSurnameValid.value == true && passwordsMatch.value == true) {
        const response = await fetch('https://node17.webte.fei.stuba.sk/final/registration.php', {
            method: 'POST',
            body: JSON.stringify({
                name: name.value,
                surname: surname.value,
                email: email.value,
                password: password.value
            })
        });

        if (!response.ok) {
            isLoading.value = false;
            const data = await response.json();
            showError(data.error);
        } else {
            name.value = null;
            surname.value = null;
            email.value = null;
            password.value = null;
            confirmPassword.value = null;
            isFormSubmitted.value = true;
            const data = await response.json();
            showSuccess(data.message);
        }
    }
    isLoading.value = false;
}
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