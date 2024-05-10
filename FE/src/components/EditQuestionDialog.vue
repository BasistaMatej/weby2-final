<template>
  <Dialog v-model:visible="visible" modal :header="props.title" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">
      <!-- <span class="p-text-secondary block mb-5"></span> -->
      <div class="flex align-items-center gap-3 mb-2">
        <label for="question" class="font-semibold w-6rem" style="min-width:15%">{{$t('question')}}</label>
        <InputText id="question" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%"
          v-model="questionText" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="category" class="font-semibold w-6rem" style="min-width:15%">{{$t('subject')}}</label>
        <Dropdown v-model="selectedCategory" :options="categories" filter optionLabel="name" id="category"
          :placeholder="$t('select_category')" class="w-full md:w-14rem mx-3" style="min-width: 80%">
          <template #value="slotProps">
            <div v-if="slotProps.value" class="flex align-items-center">
              <div>{{ slotProps.value.name }}</div>
            </div>
            <span v-else>
              {{ slotProps.placeholder }}
            </span>
          </template>
          <template #option="slotProps">
            <div class="flex align-items-center">
              <div>{{ slotProps.option.name }}</div>
            </div>
          </template>
        </Dropdown>
      </div>
      <div class="flex align-items-center align-content-center gap-3 mb-2">
        <label for="isActive" class="font-semibold w-6rem" style="min-width:15%">{{$t('active?')}}</label>
        <InputSwitch v-model="isActive" id="isActive" class="mx-3" />
      </div>
      <div class="flex align-items-center align-content-center gap-3 mb-2">
        <label for="type" class="font-semibold w-6rem" style="min-width:15%">{{$t('type')}}</label>
        <SelectButton class="d-inline-flex mx-3" v-model="type" :options="options" aria-labelledby="basic" id="type"
          optionLabel="name" />
      </div>

      <div v-if="type.value === 2" class="mt-3">
        <div v-for=" (option, index) in answers" class="flex align-items-center gap-3 mb-2" :key="index">
          <label :for="'option-' + index" class="font-semibold w-6rem" style="min-width:15%">{{ index + 1 }}.
            {{ $t('option') }}</label>
          <InputText :id="'option-' + index" class="flex-auto mx-3" autocomplete="off" style="min-width: 70%"
            v-model="answers[index].text" />
          <div class="d-inline-block p-2 options-button" @click="deleteOption(index)">
            <lord-icon src="https://cdn.lordicon.com/drxwpfop.json" trigger="hover" style="width:2em;height:2em"
              colors="primary:#121331,secondary:#8b5cf6" stroke="bold" class="pt-2">
            </lord-icon>
          </div>
        </div>
        <div class="d-flex mb-3 button">
          <div style="min-width: 15%"></div>
          <div class="d-flex flex-columns align-items-center align-content-center p-2 table-link mx-3" @click="addOption">
            <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
              style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
            </lord-icon>
            <span>{{ $t('add_option') }}</span>
          </div>
        </div>
      </div>

      <div class="flex justify-content-end gap-2 modal-buttons">
        <Button type="button" severity="secondary" @click="visible = false">{{$t('cancel')}}</Button>
        <Button type="button"  @click="saveQuestion" class="mx-3">{{$t('save')}}</Button>
      </div>
    </div>
    <Toast />
  </Dialog>
</template>

<script setup>
import { ref, defineModel, defineProps, onMounted, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';
import SelectButton from 'primevue/selectbutton';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";

const toast = useToast();

const selectedCategory = ref(null);
const answers = ref([{ text: '', id: null }]);
const isActive = ref(false);
const questionText = ref('');
const id = ref(null);
const type = ref({ name: 'S otvorenou odpoveďou', value: 1 });
const options = ref([{ name: 'S otvorenou odpoveďou', value: 1 }, { name: 'S možnosťami', value: 2 }]);

const visible = defineModel();
const props = defineProps(['title', 'question', 'category', 'isActive', 'id', 'type']);

const deleteOption = (index) => {
  if (answers.value.length <= 2) {
    toast.add({ severity: 'warn', summary: 'Informácia', detail: 'Musíte si vybrať aspoň 2 možnosti.', life: 4500 });
    return;
  }
  answers.value.splice(index, 1);
}

const addOption = () => {
  answers.value.push({ text: '', id: null });
}

const categories = ref([
  { name: 'Jazyky', id: 1 },
  { name: 'Matematika', id: 2 },
  { name: 'História', id: 3 },
  { name: 'Panda', id: 4 },
  { name: 'Klokan', id: 88 },
  { name: 'IT', id: 102 }
]);

const saveQuestion = () => {
  if (!questionText.value) {
    toast.add({ severity: 'error', summary: 'Informácia', detail: 'Otázka nesmie byť prázdna.', life: 4500 });
    return;
  }

  if (type.value.value === 2) {
    if (answers.value.length < 2) {
      toast.add({ severity: 'warn', summary: 'Informácia', detail: 'Musíte si vybrať aspoň 2 možnosti.', life: 4500 });
      return;
    }
  }

  console.log('Save question');
}

watch(
  () => props.question,
  () => {
    questionText.value = props.question
  }
);

watch(
  () => props.id,
  () => {
    id.value = props.id
  }
);

watch(
  () => props.isActive,
  () => {
    isActive.value = props.isActive
  }
);

watch(
  () => props.category,
  () => {
    selectedCategory.value = props.category
  }
);

watch(
  () => props.type,
  () => {
    console.log('Type', type.value)
    options.value.map(option => {
      if (option.value === props.type) {
        type.value = option;
        fetchAnswers();
      }
    });
  }
);

onMounted(() => {
  fetchCategories();
});

const fetchAnswers = () => {
  if (id.value !== null) {
    // fetch('https://api.example.com/answers/' + id.value)
    //   .then(response => response.json())
    //   .then(data => {
    //     answers.value = data;
    //   });
  }
}

const fetchCategories = () => {
  // fetch('https://api.example.com/categories')
  //   .then(response => response.json())
  //   .then(data => {
  //     categories.value = data;
  //   });
}

</script>

<style>
.p-dialog-header-close {
  border-radius: 50%;
}

.p-dialog-header-close svg {
  margin: 0.5em;
}

.modal-buttons button {
  border-radius: 0.7em;
}
</style>

<style scoped>
.options-button,
button {
  cursor: pointer;
}
</style>
