<template>
  <Dialog v-model:visible="visible" modal :header="props.title" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">
      <!-- <span class="p-text-secondary block mb-5"></span> -->
      <div class="flex align-items-center gap-3 mb-2">
        <label for="question" class="font-semibold w-6rem" style="min-width:15%">Otázka</label>
        <InputText id="question" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%"
          v-model="questionText" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="category" class="font-semibold w-6rem" style="min-width:15%">Predmet</label>
        <Dropdown v-model="selectedCategory" :options="categories" filter optionLabel="name" id="category"
          placeholder="Výber kategóriu" class="w-full md:w-14rem mx-3" style="min-width: 80%">
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
        <label for="isActive" class="font-semibold w-6rem" style="min-width:15%">Aktívna?</label>
        <InputSwitch v-model="isActive" id="isActive" class="mx-3" />
      </div>
      <div class="flex justify-content-end gap-2 modal-buttons">
        <Button type="button" label="Zrušiť" severity="secondary" @click="visible = false"></Button>
        <Button type="button" label="Uložiť!" @click="saveQuestion" class="mx-3"></Button>
      </div>
    </div>
  </Dialog>
</template>

<script setup>
import { ref, defineModel, defineProps, onMounted, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import InputSwitch from 'primevue/inputswitch';

const selectedCategory = ref(null);
const isActive = ref(false);
const questionText = ref('');
const id = ref(null);

const visible = defineModel();
const props = defineProps(['title', 'question', 'category', 'isActive', 'id']);

const categories = ref([
  { name: 'Jazyk', id: 1 },
  { name: 'Matematika', id: 2 },
  { name: 'Historia', id: 3 },
  { name: 'Panda', id: 4 },
  { name: 'Klokan', id: 88 },
  { name: 'IT', id: 102 }
]);

const saveQuestion = () => {
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

onMounted(() => {
  fetchCategories();
});

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
