<template>
  <div>
    <AuthNavBar />
    <div class="container">
      <div class="px-md-5">
        <div>
          <div class="d-inline-block">
            <div @click="newUser(null, null, null, null, null, null, $t('lang_id'))"
                 class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                         style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{$t('create_user')}}</span>
            </div>
          </div>
        </div>
        <span class="d-inline-block" style="margin-top: 2em; font-size: 80%; color: rgba(0,0,0,0.5)">
          {{$t('filter_several_columns')}}
        </span>
        <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
                   sortMode="multiple" :value="products" removableSort dataKey="id" selectionMode="single" @rowSelect="(event) => editRow(event, $t('lang_id'))">
          <template #empty>
            <div class="d-flex flex-column align-items-center">
              <lord-icon src="https://cdn.lordicon.com/ribxmuoc.json" trigger="loop" delay="700"
                         colors="primary:#121331,secondary:#8b5cf6" style="width:250px;height:250px">
              </lord-icon>
              <h4>
                {{$t('no_user_find')}}
              </h4>
            </div>
          </template>
          <template #loading> {{$t('loading')}} </template>
          <Column field="id" header="ID" sortable></Column>
          <Column field="name" :header="$t('name')" sortable></Column>
          <Column field="surname" :header="$t('surname')" sortable></Column>
          <Column field="email" header="Email" sortable></Column>
          <Column field="status" header="Status" >
            <template #body="slotProps">
              <Tag :value="getStatusLabel(slotProps.data.status)" :severity="getSeverity(slotProps.data.status)" />
            </template>
          </Column>
          <Column field="last_login" :header="$t('last_activity')" sortable></Column>
          <Column >
            <template #body="slotProps">
              <button @click="deleteRow(slotProps.data.id)" class="btn  btn-sm">
                <lord-icon src="https://cdn.lordicon.com/drxwpfop.json" trigger="hover" style="width:2em;height:2em"
                           colors="primary:#121331,secondary:#8b5cf6" stroke="bold" class="pt-2">
                </lord-icon>
              </button>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>

    <EditUserDialog v-model="showDialog" :title="dialogTitle" :id="dialogId" :name="dialogName"
                        :sureame="dialogSurname" :email="dialogEmail" :status="dialogStatus" :last_login="dialogLast_login" :lang_id="$t('lang_id')" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import AuthNavBar from '@/components/AuthNavBar.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import EditUserDialog from '@/components/EditUserDialog.vue';
import Tag from 'primevue/tag';
import { FilterMatchMode } from 'primevue/api';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

const showDialog = ref(false);
const dialogTitle = ref('Pridanie nového používateľa');
const dialogName = ref('');
const dialogSurname = ref('');
const dialogEmail = ref('');
const dialogId = ref(null);
const dialogStatus = ref('');
const dialogLast_login = ref(null);
const lang_id = ref('');

const products = ref([]);

const productsSample = ref([
  { id: 0, name: 'Adam', surname: 'Macko', email: 'dfgh@gmail.com', status: 1, last_login: '01-03-2024' },
  { id: 10, name: 'Eva', surname: 'Macko', email: 'eva@gmail.com', status: 2, last_login: '08-03-2024' },
  { id: 110, name: 'Sisi', surname: 'Lekvar', email: 'esisi@gmail.com', status: -1, last_login: '07-12-2024' },
]);

const deleteRow = (id) => {
  products.value = products.value.filter(product => product.id !== id);
  // productsSample.value = productsSample.value.filter(productsSample => productsSample.id !== id);
}
const editRow = (event, lang) => {
  newUser(event.data.id, event.data.name, event.data.surname, event.data.email, event.data.status, event.data.last_login, lang)
}

const newUser = (id, name, surname, email, status, last_login, lang) => {
  if (id == null) {
    if (lang === 'sk') {
      dialogTitle.value = 'Pridanie nového používateľa';
    } else if (lang === 'en') {
      console.log(lang);
      dialogTitle.value = 'Add new user';
    }
    dialogId.value = null;
    dialogName.value = '';
    dialogSurname.value = '';
    dialogEmail.value = '';
    dialogStatus.value = '';
    dialogLast_login.value = null;

  } else {
    if (lang === 'sk') {
      dialogTitle.value = 'Úprava používateľa';
    } else if (lang === 'en') {
      dialogTitle.value = 'Edit user';
    }
    dialogId.value = id;
    dialogName.value = name;
    dialogSurname.value = surname;
    dialogEmail.value = email;
    dialogStatus.value = status;
    dialogLast_login.value = last_login;

  }
  showDialog.value = true;
}


const getStatusLabel = (status) => {
  switch (status) {
    case -1:
      return 'Blocked';
    case 1:
      return 'Basic';
    case 2:
      return 'Admin';
    default:
      return 'Unknown';
  }
}
const getSeverity = (status) => {
  switch (status) {
    case -1:
      return 'danger';
    case 1:
      return 'success';
    case 2:
      return 'info';
    default:
      return null;
  }
}

</script>

<style>
.auth-table {
  border-radius: 1em;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  margin-top: 0.4em;
}

.auth-table .p-datatable-wrapper {
  border-top-left-radius: 1em;
  border-top-right-radius: 1em;
}

.auth-table .p-paginator {
  border-bottom-left-radius: 1em;
  border-bottom-right-radius: 1em;
}

button.p-paginator-page {
  border-radius: 50%;
}

.p-dropdown-items-wrapper .p-dropdown-items {
  padding: 0 !important;
  margin: 0 !important;
}

.table-link {
  background: #8B5CF633;
  text-decoration: none !important;
  color: black;
  border-radius: 1em;
  transition: 0.3s ease;
}

.table-link:hover {
  background: #8B5CF666;
  color: #333;
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
}
</style>

