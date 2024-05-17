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

      <div v-if="type.value === 1" class="mt-3">
        <div v-for=" (option, index) in answers" class="flex align-items-center gap-3 mb-2" :key="index">
          <label :for="'option-' + index" class="font-semibold w-6rem" style="min-width:15%">{{ index + 1 }}.
            {{ $t('option') }}</label>
          <InputText :id="'option-' + index" class="flex-auto mx-3" autocomplete="off" style="min-width: 70%"
            v-model="answers[index].answer_text" />
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
import { auth_fetch } from '@/utils';

const toast = useToast();

const selectedCategory = ref(null);
const answers = ref([{ answer_text: '', answer_id: null }]);
const isActive = ref(false);
const questionText = ref('');
const lang_id = ref('');
const id = ref(null);
const type = ref({ name: 'S otvorenou odpoveďou', value: 1 });
const options = ref([{ name: 'S otvorenou odpoveďou', value: 0 }, { name: 'S možnosťami', value: 1 }]);

const categories = ref([]);

const visible = defineModel();
const props = defineProps(['title', 'question', 'category', 'isActive', 'id', 'type', 'lang_id']);

const setTypeAndOptionsName = (lang_id) => {
  if (lang_id === "en") {
    type.value.name = 'With an open answer';
    options.value[0].name = 'With an open answer';
    options.value[1].name = 'With options';
  } else  {
    type.value.name = 'S otvorenou odpoveďou';
    options.value[0].name = 'S otvorenou odpoveďou';
    options.value[1].name = 'S možnosťami';
  }
}

setTypeAndOptionsName(lang_id.value);

const deleteOption = (index) => {
  if (answers.value.length <= 2) {
    if (lang_id.value === "en") {
      toast.add({ severity: 'warn', summary: 'Information', detail: 'You must choose at least 2 options.', life: 4500 });
    } else  {
      toast.add({ severity: 'warn', summary: 'Informácia', detail: 'Musíte si vybrať aspoň 2 možnosti.', life: 4500 });
    }

    return;
  }
  answers.value.splice(index, 1);
}

const addOption = () => {
  answers.value.push({ answer_text: '', answer_id: null });
}

const saveQuestion = () => {
  if (!questionText.value) {
    if (lang_id.value === "en") {
      toast.add({ severity: 'warn', summary: 'Information', detail: 'The question must not be empty.', life: 4500 });
    } else  {
      toast.add({ severity: 'error', summary: 'Informácia', detail: 'Otázka nesmie byť prázdna.', life: 4500 });
    }

    return;
  }

  if (type.value.value === 2) {
    if (answers.value.length < 2) {
      if (lang_id.value === "en") {
        toast.add({ severity: 'warn', summary: 'Information', detail: 'You must choose at least 2 options.', life: 4500 });
      } else  {
        toast.add({ severity: 'warn', summary: 'Informácia', detail: 'Musíte si vybrať aspoň 2 možnosti.', life: 4500 });
      }

      return;
    }
  }

  console.log('Save question');
}

const fetchAllSubjects = async () => {
  const res = await auth_fetch('/subject');
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    const data = await res.json();

    categories.value = data.subjects.map(subject => ({ name: subject }));
  }
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
    () => props.lang_id,
    () => {
      lang_id.value = props.lang_id
      setTypeAndOptionsName(lang_id.value);
      setCategories(lang_id.value);
    }
);

watch(
  () => props.isActive,
  () => {
    isActive.value = props.isActive === 1 ? true : false;
  }
);


watch(
  () => props.category,
  () => {
    categories.value.map(option => {
      if (option.name === props.category) {
        selectedCategory.value = option;
      }
    });
  }
);

watch(
  () => props.type,
  () => {
    options.value.map(option => {
      if (option.value === props.type) {
        type.value = option;
        if(option.value == 1)
          fetchAnswers();
      }
    });
  }
);

onMounted(() => {
  fetchAllSubjects();
});

const fetchAnswers = async () => {
  if (id.value !== null) {
    const res = await auth_fetch(`/question/answers/${id.value}`)

    if (res.status === 200) {
      const data = await res.json();
      answers.value = data.answers;
    } else if(res.status === 204) {
      answers.value = [];
      if(lang_id.value == "en") {
        toast.add({ severity: 'info', summary: 'Information', detail: 'No answers found.', life: 4500 });
      } else {
        toast.add({ severity: 'info', summary: 'Informácia', detail: 'Nenašli sa žiadne odpovede.', life: 4500 });
      }
    } else {
      if (lang_id.value === "en") {
        toast.add({ severity: 'error', summary: 'Error', detail: 'An error occurred while fetching answers.', life: 4500 });
      } else  {
        toast.add({ severity: 'error', summary: 'Chyba', detail: 'Pri načítavaní odpovedí sa vyskytla chyba.', life: 4500 });
      }
    }
  }
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
