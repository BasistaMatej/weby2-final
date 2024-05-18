<template>
    <div class="card">
        <h1 class="text-center">Aká farba je tvoja oblúbená ?</h1>
        <div class="flex flex-column gap-3">
            <div class="box-line" v-for="ansver of ansvers" :key="ansver.id">
                <Checkbox v-model="selectedAnsvers" name="ansver" :value="ansver.name" />
                <label :for="ansver.id">{{ ansver.name }}</label>
            </div>
        </div>
        <Button @click="submitAnsver" type="submit" label="submit">{{ $t('confirm') }} <lord-icon
                src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#ffffff"
                style="width:2em;height:2em;margin-left:1em;">
            </lord-icon></Button>
    </div>
    <Toast />
</template>

<script setup>
import { ref, watch } from "vue";
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const ansvers = ref([
    { name: "Zelena", id: "1" },
    { name: "Biela", id: "2" },
    { name: "Cierna", id: "3" }
]);
const selectedAnsvers = ref([]);

//NOT 100% implemented -> Treba na dobry ENDPOINT
const submitAnsver = async () => {
    try {
        //const response = auth_fetch();//TODO
        //console.log('Answers submitted successfully');
        selectedAnsvers.value = [];
        showSuccess();
    } catch (error) {
        showError(error);
        //console.error('Error submitting answers:', error);
    }
}

watch([selectedAnsvers], () => {
    console.log(selectedAnsvers.value); //ZAKOMENTOVAT
})

const showSuccess = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: "Odpoveď bola zaznamenaná!", life: 5000 });
};

const showError = (errorMessage) => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

</script>

<style scoped>
button {
    border-radius: 1rem;
    margin-bottom: 1.5rem;
}

h1 {
    margin-top: 1.5rem;
}

label {
    font-size: 1.5rem;
}

.p-checkbox {
    margin-right: .5rem;
    margin-bottom: .5rem;
}

.card {
    width: 70%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    border-radius: 2rem;
}

.box-line:last-child {
    margin-bottom: 2rem;
}

#question-box {
    display: flex;
    flex-direction: column;
    align-items: center;

}
</style>
