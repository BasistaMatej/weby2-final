<template>
  <div>
      <div class="card">
          <h1 class="text-center">{{ questionText }}</h1>
          <div class="flex flex-column gap-3">
              <div class="box-line" v-for="ansver of ansvers" :key="ansver.answer_id">
                  <RadioButton v-model="selectedAnsvers" name="ansver" :value="ansver.answer_id"  />
                  <label :for="ansver.answer_id">{{ ansver.answer_text }}</label>
              </div>
          </div>
          <Button @click="submitAnsver" type="submit" label="submit">{{ $t('confirm') }} <lord-icon
                  src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" colors="primary:#ffffff"
                  style="width:2em;height:2em;margin-left:1em;">
              </lord-icon></Button>
      </div>
      <Toast />
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import RadioButton from 'primevue/radiobutton';

const toast = useToast();
const questionText = ref('');
const props = defineProps(['question', 'answers']);
const emits = defineEmits(['button-clicked']);
const ansvers = ref([]);
const selectedAnsvers = ref([]);

onMounted(() => {
  questionText.value = props.question;
  ansvers.value = props.answers;
});

watch(
  () => props.question,
  () => {
    questionText.value = props.question;
  }
);

watch(
  () => props.answers,
  () => {
    ansvers.value = props.answers;
  }
);

watch([selectedAnsvers], () => {
    console.log(selectedAnsvers.value); //ZAKOMENTOVAT
})

const submitAnsver = async () => {
  if (selectedAnsvers.value) {
    emits('button-clicked', selectedAnsvers.value);
    showSuccess();
  } else {
    showError("Vyberte aspoň jednu možnosť!");
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
