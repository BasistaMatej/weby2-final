<template>
  <div>
    <div class="card">
        <h1 class="text-center">{{ questionText }}</h1>
        <div id="box-input" class="flex flex-column gap-3">
            <InputText v-model="selectedAnsvers" :placeholder="$t('answer')" name="ansver" />
        </div>
        <Button @click="submitAnsver" type="submit" label="submit"> {{ $t('confirm') }}<lord-icon
                src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#ffffff"
                style="width:2em;height:2em;margin-left:1em;">
            </lord-icon></Button>
    </div>
    <Toast />
  </div>
</template>

<script setup>
import { ref, watch, onMounted, defineEmits } from "vue";
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';


const selectedAnsvers = ref(null);
const toast = useToast();
const props = defineProps(['question']);
const emits = defineEmits(['button-clicked']);
const questionText = ref('');

onMounted(() => {
  questionText.value = props.question;
});

watch(
  () => props.question,
  () => {
    questionText.value = props.question;
  }
);

const submitAnsver = async () => {
  if (selectedAnsvers.value) {
    emits('button-clicked', selectedAnsvers.value.trim());
    showSuccess();
  } else {
    showError("Odpoveď nesmie byť prázdna!");
  }
}

const showSuccess = () => {
    toast.add({ severity: 'success', summary: 'Success', detail: "Odpoveď bola zaznamenaná!", life: 5000 });
};

const showError = (errorMessage) => {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

</script>

<style scoped>
#box-input {
    width: 100%;
}

input {
    margin-bottom: 1.5rem;
    margin-top: 1.5rem;
    width: 60%;
    margin-right: 20%;
    margin-left: 20%;
}

button {
    border-radius: 1rem;
    margin-bottom: 1.5rem;
}

h1 {
    margin-top: 1.5rem;
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
</style>
