<template>
  <Dialog v-model:visible="visible" modal :header="props.title" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">
      <div class="flex align-items-center gap-3 mb-2">
        <label for="name" class="font-semibold w-6rem" style="min-width:15%">{{ $t('name') }}</label>
        <InputText id="name" class="flex-auto mx-3" autocomplete="off" :invalid="!isNameValid" style="min-width: 80%"
          v-model="name" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="surname" class="font-semibold w-6rem" style="min-width:15%">{{ $t('surname') }}</label>
        <InputText id="surname" class="flex-auto mx-3" autocomplete="off" :invalid="!isSurnameValid"
          style="min-width: 80%" v-model="surname" />
      </div>
      <div class="flex align-items-center gap-3 mb-2">
        <label for="email" class="font-semibold w-6rem" style="min-width:15%">Email</label>
        <InputText id="email" class="flex-auto mx-3" autocomplete="off" :invalid="!isEmailValid" style="min-width: 80%"
          v-model="email" />
      </div>

      <div class="flex align-items-center gap-3 mb-2">
        <label for="password" class="font-semibold w-6rem" style="min-width:15%">Heslo</label>
        <Password id="password" class="flex-auto mx-3" :invalid="!isValidPassword" autocomplete="off"
          style="min-width: 80%" v-model="password" />
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
        <Button type="button" severity="secondary" @click="visible = false">{{ $t('cancel') }}</Button>
        <Button type="button" @click="saveUser" class="mx-3">{{ $t('save') }}</Button>
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
import Password from "primevue/password";
import { auth_fetch } from '@/utils';

const toast = useToast();

const selectedStatus = ref({ name: 'Basic', id: 1 });
const name = ref('');
const surname = ref('');
const email = ref('');
const lang_id = ref('');
const id = ref(null);
const password = ref(null);
const isEmailValid = ref(true);
const isValidPassword = ref(true);
const visible = defineModel();
const isNameValid = ref(true);
const isSurnameValid = ref(true);
const props = defineProps(['title', 'name', 'surname', 'email', 'id', 'status', 'lang_id', 'password']);


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

  } else {
    statuses.value[0].name = 'Blokovaný';
    statuses.value[1].name = 'Normálny';
    statuses.value[2].name = 'Administrátor';

  }
}

const showError = (errorMessage) => {
  toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
};

const showSuccess = (errorMessage) => {
  toast.add({ severity: 'success', summary: 'Success Message', detail: errorMessage, life: 3000 });
};

setStatuses(lang_id.value);
const saveUser = async () => {
  if (isEmailValid.value == true && isNameValid.value == true && isSurnameValid.value == true && isValidPassword.value == true) {

    if (id.value === null) {
      try {
        const response = await auth_fetch(`/user`, "POST", { "name": name.value, "surname": surname.value, "password": password.value, "email": email.value, "status": selectedStatus.value.id });
        showSuccess("User created succesfully!");
        name.value = null;
        surname.value = null;
        email.value = null;
        password.value = null;
      } catch (error) {
        showError(error);
      }
    } else {
      try {
        console.log(id.value);
        const response = await auth_fetch(`/user/${id.value}`, "PUT", { "name": name.value, "surname": surname.value, "password": password.value, "email": email.value, "status": selectedStatus.value.id });
        showSuccess("User created succesfully!");
        name.value = null;
        surname.value = null;
        email.value = null;
        password.value = null;
        isEmailValid.value = true;
        isValidPassword.value = true;
        isNameValid.value = true;
        isSurnameValid.value = true;
      } catch (error) {
        showError(error);
      }
    }
  }

  console.log('Save user');
}


const validateName = () => {
  const nameSurnameRegex = /^[a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ]+(([',. -][a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ ])?[a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ]*)*$/g;
  if (name.value) {
    isNameValid.value = nameSurnameRegex.test(name.value);
  } else {
    isNameValid.value = false;
  }
}

const validateSurname = () => {
  const nameSurnameRegex = /^[a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ]+(([',. -][a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ ])?[a-zA-ZáäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ]*)*$/g;
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


watch([email], () => {
  validateEmail();
})

watch([password], () => {
  checkPasswords();
})

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
  () => props.password,
  () => {
    password.value = props.password
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
    selectedStatus.value = statuses.value.filter(i => i.id == props.status)
    if (selectedStatus.value.length > 0) {
      selectedStatus.value = selectedStatus.value[0];
    }
    console.log(props.status);
    console.log(selectedStatus.value);
  }
);



</script>

<style>
input.p-password-input {
  min-width: 100%;
}

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
