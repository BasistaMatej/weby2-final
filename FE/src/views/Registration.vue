<!-- _IMPORTANT_ Podstránka s registračným formulárom -->

<template>

    <div>
        <DefaultNavBar />
        <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
            <h1 class="roboto-black">Registračný formulár_</h1>
        </div>
    </div>

    <div class="d-flex align-items-center flex-column">
        <div id="box">
            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-user"></i>
                </InputGroupAddon>
                <InputText placeholder="Name" name="name" />
            </InputGroup>

            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-user"></i>
                </InputGroupAddon>
                <InputText placeholder="Surname" name="surname" />
            </InputGroup>

            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-at"></i>
                </InputGroupAddon>
                <InputText placeholder="Email" name="email" />
            </InputGroup>

            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-lock"></i>
                </InputGroupAddon>
                <Password v-model="password" :invalid="!passwordsMatch" placeholder="Password" toggleMask />
            </InputGroup>

            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-lock"></i>
                </InputGroupAddon>
                <Password v-model="confirmPassword" :invalid="!passwordsMatch" placeholder="Confirm Password"
                    toggleMask />
            </InputGroup>
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

const password = ref(null)
const confirmPassword = ref(null)
const passwordsMatch = ref(true)

watch([password, confirmPassword], () => {
    if (password.value !== confirmPassword.value) {
        //console.log("Passwords do not match!");
        passwordsMatch.value = false;
    } else {
        //console.log("Passwords match!");
        passwordsMatch.value = true;
    }
})
</script>

<style scoped>
#box {
    width: 50%;
    box-sizing: border-box !important;
    border-radius: 10px;
    border: 3px solid #8B5CF6;
    display: flex;
    align-items: center;
    flex-direction: column;
}

.p-inputgroup {
    margin: 1rem !important;
    width: calc(100% - 2rem) !important;
}
</style>