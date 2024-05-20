<template>
  <div class="pb-5">
    <AuthNavBar />
    <div class="container">
      <div class="px-md-5">
        <div>
          <div class="d-inline-block" v-if="authLevel == 2">
            <router-link to="/users" class="d-flex flex-columns align-items-center align-content-center p-2 table-link"
              style="background: #8B5CF6AA">
              <lord-icon src="https://cdn.lordicon.com/bjbmvfnr.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#d0bdfb">
              </lord-icon>
              <span>{{ $t('users') }}</span>
            </router-link>
          </div>
          <div class="d-inline-block mx-2">
            <div @click="editQuestion(null, null, null, null, null, $t('lang_id'))"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{ $t('create_question') }}</span>
            </div>
          </div>
          <div class="d-inline-block" v-if="authLevel == 1">
            <div @click="addNewSubject"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{ $t('add_subject') }}</span>
            </div>
          </div>
          <div class="d-inline-block" v-if="authLevel == 2">
            <div @click="editSubjectDialog = true"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" state="hover-line" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{ $t('edit_subject') }}</span>
            </div>
          </div>
        </div>
        <span class="d-inline-block" style="margin-top: 2em; font-size: 80%; color: rgba(0,0,0,0.5)">
          {{ $t('filter_several_columns') }}
        </span>
        <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="products" removableSort dataKey="id" selectionMode="single"
          @rowSelect="(event) => editRow(event, $t('lang_id'))" filterDisplay="menu" v-model:filters="filters"
          :globalFilterFields="['subject_name', 'created']">
          <template #empty>
            <div class="d-flex flex-column align-items-center">
              <lord-icon src="https://cdn.lordicon.com/ribxmuoc.json" trigger="loop" delay="700"
                colors="primary:#121331,secondary:#8b5cf6" style="width:250px;height:250px">
              </lord-icon>
              <h4>
                {{ $t('no_question_find') }}
              </h4>
            </div>
          </template>
          <template #loading> {{ $t('loading') }} </template>
          <Column field="template_question_text" :header="$t('question')"></Column>
          <Column field="subject_name" :header="$t('subject')" sortable>
            <template #filter="{ filterModel }">
              <InputText v-model="filterModel.value" type="text" class="p-column-filter"
                :placeholder="$t('search_subject')" />
            </template>
          </Column>
          <Column field="created" :header="$t('created')" dataType="date" sortable>
            <template #filter="{ filterModel }">
              <Calendar v-model="filterModel.value" dateFormat="yy-mm-dd" placeholder="yy-mm-dd" mask="9999-99-99" />
            </template>
          </Column>

          <Column field="code" :header="$t('code')"></Column>
          <Column field="tools" :header="$t('tools')">
            <template #body="slotProps">
              <div class="d-flex">
                <Button class="row-buttons" @click="deleteItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/nqtddedc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button v-if="slotProps.data.active == 1 && (slotProps.data.code)" class="row-buttons"
                  @click="closeItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/mwikjdwh.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button v-if="slotProps.data.active == 0" class="row-buttons"
                  @click="activateItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/aklfruoc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button class="row-buttons" @click="copyItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/rmkahxvq.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button v-if="slotProps.data.active == 1 && slotProps.data.code != null" class="row-buttons"
                  @click="viewQr(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/kkvxgpti.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>

                <Button class="row-buttons" @click="viewHistory(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/whrxobsb.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
              </div>
              <div class="qr-backdrop" v-if="isActiveQr && isActiveRow == slotProps.data.template_question_id">
                <div id="qr-box" class="d-flex">
                  <h1>{{ $t('join_question') }}</h1>
                  <QRCodeVue3 :width="200" :height="200" :value="`http://localhost:5173/${slotProps.data.code}`" />
                  <Button id="button-modal" class="mt-2" @click="isActiveQr = false" style="border-radius: 0.7em">{{
            $t('close') }}</Button>
                </div>
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
    <Toast />

    <EditQuestionDialog v-model="showDialog" :title="dialogTitle" :category="dialogSubject" :id="dialogId"
      :isActive="dialogActive" :question="dialogQuestion" :type="dialogType" :lang_id="$t('lang_id')" />
    <AddSubjectDialog v-model="addNewSubjectDialog" />
    <EditSubjectDialog v-model="editSubjectDialog" />
  </div>

</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import AuthNavBar from '@/components/AuthNavBar.vue';
import AddSubjectDialog from '@/components/AddSubjectDialog.vue';
import EditSubjectDialog from '@/components/EditSubjectDialog.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import EditQuestionDialog from '@/components/EditQuestionDialog.vue';
import { FilterMatchMode, FilterOperator } from 'primevue/api';
import InputText from 'primevue/inputtext';
import Calendar from 'primevue/calendar';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Button from 'primevue/button';
import { auth_fetch, getLocalStorage } from '@/utils';
import QRCodeVue3 from "qrcode-vue3";
import { useRouter } from 'vue-router';
import QuestionWithAnsvers from '@/components/QuestionWithAnsvers.vue';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const addNewSubjectDialog = ref(false);
const editSubjectDialog = ref(false);
const showDialog = ref(false);
const dialogTitle = ref('Vytvorenie novej otázky');
const dialogQuestion = ref('');
const dialogSubject = ref('');
const dialogActive = ref(false);
const dialogId = ref(null);
const dialogType = ref(10);
const lang_id = ref('');
const authLevel = ref(1);
const isActiveQr = ref(false);
const products = ref([]);
const isActiveRow = ref(0);
const router = useRouter();

const socket = new WebSocket('ws://localhost:9999/wss');

socket.onopen = () => {
  console.log("WebSocket connected");
};

socket.onmessage = (event) => {
  console.log("WebSocket:", event.data);
};

const viewHistory = (row) => {
  router.push(`history/${row.template_question_id}/${row.type}`);
}

watch(
  () => showDialog.value,
  async () => {
    if (!showDialog.value) {
      const response = await initialGetFetch();
      if (!response.ok) {
        const data = await response.json();
      } else {
        const data = await response.json();
        products.value = data.questions;
      }
    }
  }
);

const filters = ref();

const initFilters = () => {
  filters.value = {
    subject_name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
    created: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] }
  };
};
initFilters();

const clearFilter = () => {
  initFilters();
};

const formatDate = (value) => {
  return value.toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
};

const showSuccess = (lang) => {
  if (lang === 'sk') {
    toast.add({ severity: 'success', summary: 'Úspech', detail: "Operácia vykonaná úspešne!", life: 5000 });
  } else if (lang === 'en') {
    toast.add({ severity: 'success', summary: 'Success', detail: "Operation done successfully!", life: 5000 });
  }
};

const showError = (errorMessage, lang) => {
  if(lang === 'sk') {
    toast.add({ severity: 'error', summary: 'Chybové hlásenie', detail: "Niečo sa pokazilo.", life: 3000 });
  } else {
    toast.add({ severity: 'error', summary: 'Error Message', detail: errorMessage, life: 3000 });
  }
};

const addNewSubject = () => {
  addNewSubjectDialog.value = true;
}

const viewQr = (row) => {
  isActiveQr.value = true;
  isActiveRow.value = row.template_question_id;
}

onMounted(async () => {
  authLevel.value = getLocalStorage("accessLevel");
  const response = await initialGetFetch();

  if (!response.ok) {
    const data = await response.json();
  } else {
    const data = await response.json();
    products.value = data.questions;
  }
});

const initialGetFetch = async () => {
  return (await auth_fetch('/question'));
}

const deleteItem = async (row, lang) => {
  const response = await auth_fetch(`/question/template_question/${row.template_question_id}`, "DELETE");

  if (!response.ok) {
    const data = await response.json();
    showError(data.error, lang);
  } else {
    const response = await initialGetFetch();
    showSuccess(lang);
    if (!response.ok) {
      const data = await response.json();
    } else {
      const data = await response.json();
      products.value = data.questions;
    }
  }
};

const activateItem = async (row, lang) => {
  const response = await auth_fetch(`/question/set_active/${row.template_question_id}`, "PUT", { active: 1 });

  if (!response.ok) {
    const data = await response.json();
    showError(data.error, lang);
  } else {
    const response = await initialGetFetch();
    showSuccess(lang);
    if (!response.ok) {
      const data = await response.json();
    } else {
      const data = await response.json();
      products.value = data.questions;

      // Init room ws
      const message = { 'type': 'initRoom', 'roomKey': data.questions.filter(item => item.template_question_id == row.template_question_id)[0].code };
      socket.send(JSON.stringify(message));

      isActiveQr.value = true;
      isActiveRow.value = row.template_question_id;
    }
  }
};

const copyItem = async (row, lang) => {
  const response = await auth_fetch(`/question/question_template_copy/${row.template_question_id}`, "POST");

  if (!response.ok) {
    const data = await response.json();
    showError(data.error, lang);
    return;
  } else {
    const response = await initialGetFetch();
    showSuccess(lang);
    if (!response.ok) {
      const data = await response.json();
    } else {
      const data = await response.json();
      products.value = data.questions;
    }
  }
};

const closeItem = async (row, lang) => {
  const response = await auth_fetch(`/question/set_active/${row.template_question_id}`, "PUT", { active: 0 });

  if (!response.ok) {
    const data = await response.json();
    showError(data.error, lang);
    return;
  } else {
    // Close room ws
    console.log(row.code);
    const message = { 'type': 'closeRoom', 'roomKey': row.code };
    console.log(message);
    socket.send(JSON.stringify(message));

    const response = await initialGetFetch();
    showSuccess(lang);
    if (!response.ok) {
      const data = await response.json();
    } else {
      const data = await response.json();
      products.value = data.questions;
    }
  }
};

const editRow = (event, lang) => {
  editQuestion(event.data.template_question_id, event.data.template_question_text, event.data.subject_name, event.data.active, event.data.type, lang)
}

const editQuestion = (id, question, subject, active, type, lang) => {
  if (id === null) {
    if (lang === 'sk') {
      dialogTitle.value = 'Vytvorenie novej otázky';
    } else if (lang === 'en') {
      dialogTitle.value = 'Create a question';
    }
    dialogQuestion.value = '';
    dialogSubject.value = '';
    dialogActive.value = false;
    dialogId.value = null;
    dialogType.value = 0;
  } else {
    if (lang === 'sk') {
      dialogTitle.value = 'Úprava otázky';
    } else if (lang === 'en') {
      dialogTitle.value = 'Edit question';
    }
    dialogQuestion.value = question;
    dialogSubject.value = subject;
    dialogActive.value = active;
    dialogId.value = id;
    dialogType.value = type;
  }
  showDialog.value = true;
}

</script>

<style>
.qr-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

#qr-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

#qr-box h1 {
  margin-top: 0;
  margin-bottom: 1rem;
}

#qr-box {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

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

.row-buttons {
  border-radius: 1rem;
  background: #8B5CF666;
}
</style>
