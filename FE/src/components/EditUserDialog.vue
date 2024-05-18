<template>
  <Dialog v-model:visible="visible" modal :header="props.title" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">
      <div class="flex align-items-center gap-3 mb-2">
        <label for="name" class="font-semibold w-6rem" style="min-width:15%">{{$t('name')}}</label>
        <InputText id="name" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%" v-model="name" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="surname" class="font-semibold w-6rem" style="min-width:15%">{{$t('surname')}}</label>
        <InputText id="surname" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%" v-model="surname" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="email" class="font-semibold w-6rem" style="min-width:15%">Email</label>
        <InputText id="email" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%" v-model="email" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="status" class="font-semibold w-6rem" style="min-width:15%">Status</label>
        <Dropdown v-model="selectedStatus" :options="statuses" filter optionLabel="name" id="status"
                  :placeholder="$t('select_status')" class="w-full md:w-14rem mx-3" style="min-width: 80%">
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
      <div class="flex justify-content-end gap-2 modal-buttons">
        <Button type="button" severity="secondary" @click="visible = false">{{$t('cancel')}}</Button>
        <Button type="button"  @click="saveUser" class="mx-3">{{$t('save')}}</Button>
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

const selectedStatus = ref(null);
const name = ref('');
const surname = ref('');
const email = ref('');
const lang_id = ref('');
const id = ref(null);


const visible = defineModel();
const props = defineProps(['title', 'name', 'surname', 'email', 'id', 'status', 'lang_id']);


const statuses = ref([
  { name: 'Blocked', id: -1 },
  { name: 'Basic', id: 1 },
  { name: 'Admin', id: 2 }

]);

const setStatuses = (lang_id) => {
  if (lang_id === "en") {
    statuses.value[0].name = 'Blocked';
    statuses.value[1].name = 'Basic';
    statuses.value[1].name = 'Admin';

  } else  {
    statuses.value[0].name = 'Blokovaný';
    statuses.value[1].name = 'Normálny';
    statuses.value[2].name = 'Administrátor';

  }
}
setStatuses(lang_id.value);
const saveUser = () => {
  console.log('Save user');
}

watch(
  () => props.name,
  () => {
    name.value = props.name
  }
);

watch(
    () => props.surname,
    () => {
      surname.value = props.surname
    }
);

watch(
    () => props.email,
    () => {
      email.value = props.email
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
      setStatuses(lang_id.value);
    }
);



watch(
  () => props.status,
  () => {
    selectedStatus.value = props.status
  }
);



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
