<template>
  <div>
    <AuthNavBar />
    <div class="container">
      <div class="px-md-5">
        <div>
          <div class="d-inline-block">
            <div @click="editQuestion(null, null, null, null, null, null)"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{ $t('create_question') }}</span>
            </div>
          </div>
          <div class="d-inline-block mx-2" v-if="authLevel == 1">
            <div @click="addNewSubject"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>Pridať predmet</span>
            </div>
          </div>
          <div class="d-inline-block mx-2" v-if="authLevel == 2">
            <div @click="editSubjectDialog = true"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/wuvorxbv.json" state="hover-line" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>Editovať predmety</span>
            </div>
          </div>
        </div>
        <span class="d-inline-block" style="margin-top: 2em; font-size: 80%; color: rgba(0,0,0,0.5)">
          {{ $t('filter_several_columns') }}
        </span>
        <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="products" removableSort dataKey="id" selectionMode="single"
          @rowSelect="(event) => editRow(event, $t('lang_id'))">
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
          <Column field="template_question_text" :header="$t('question')" sortable></Column>
          <Column field="subject_name" :header="$t('subject')" sortable></Column>
          <Column field="created" :header="$t('created')" sortable></Column>
          <Column field="code" :header="$t('code')" sortable></Column>
          <Column field="tools" :header="$t('tools')">
            <template #body="slotProps">
              <div class="d-flex">
                <Button class="row-buttons" @click="deleteItem(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/nqtddedc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button v-if="slotProps.data.active == 1" class="row-buttons" @click="closeItem(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/mwikjdwh.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button v-if="slotProps.data.active == 0" class="row-buttons" @click="activateItem(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/aklfruoc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <!-- SPRAVIT VIF || MODAL NA QR -->
                <Button class="row-buttons" @click="copyItem(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/rmkahxvq.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
              </div>
              <div class="qr-backdrop" v-if="isActiveQr && isActiveRow == slotProps.data.template_question_id">
                <div id="qr-box" class="d-flex">
                  <h1>Pripoj sa do hry</h1>
                  <QRCodeVue3 :width="200" :height="200" :value="`http://localhost:5173/${slotProps.data.code}`" />
                  <Button id="button-modal" class="mt-2" @click="isActiveQr = false">Zavrieť</Button>
                </div>
              </div>

            </template>
          </Column>

        </DataTable>


      </div>
    </div>
    <EditQuestionDialog v-model="showDialog" :title="dialogTitle" :category="dialogSubject" :id="dialogId"
      :isActive="dialogActive" :question="dialogQuestion" :type="dialogType" :lang_id="$t('lang_id')" />
    <AddSubjectDialog v-model="addNewSubjectDialog" />
    <EditSubjectDialog v-model="editSubjectDialog" />
  </div>


</template>

<script setup>
import { onMounted, ref } from 'vue';
import AuthNavBar from '@/components/AuthNavBar.vue';
import AddSubjectDialog from '@/components/AddSubjectDialog.vue';
import EditSubjectDialog from '@/components/EditSubjectDialog.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import EditQuestionDialog from '@/components/EditQuestionDialog.vue';
import { FilterMatchMode } from 'primevue/api';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';
import Button from 'primevue/button';
import { auth_fetch, getLocalStorage } from '@/utils';
//import QrCode from '../components/QrCode.vue'
import QRCodeVue3 from "qrcode-vue3";
import Dialog from 'primevue/dialog';
const slotProps = ref([]);


const addNewSubjectDialog = ref(false);
const editSubjectDialog = ref(false);
const showDialog = ref(false);
const dialogTitle = ref('Vytvorenie novej otázky');
const dialogQuestion = ref('');
const dialogSubject = ref('');
const dialogActive = ref(false);
const dialogId = ref(null);
const dialogType = ref(1);
const lang_id = ref('');
const authLevel = ref(1);
const isActiveQr = ref(false);
const products = ref([]);
const isActiveRow = ref(0);

const productsSample = ref([
  { id: 0, question: 'Ako sa po čínsky povie "Bryndzové halušky"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '', type: 1 },
  { id: 12, question: 'Koľko je 2+2?', subject: 'Matematika', created: '01-03-2024', code: 'MATH15', tools: '', type: 2 },
  { id: 66, question: 'Odkiaľ pochádza slovo "káva"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '', type: 1 },
]);

const addNewSubject = () => {
  addNewSubjectDialog.value = true;
}

onMounted(async () => {
  authLevel.value = getLocalStorage("accessLevel");

  const response = await initialGetFetch();
  console.log(products.value);

  if (!response.ok) {
    const data = await response.json();
    //showError(data.error);
  } else {
    const data = await response.json();
    products.value = data.questions;
    console.log(products.value);
  }
});

const initialGetFetch = async () => {
  return (await auth_fetch('/question'));
}

const deleteItem = async (row) => {
  console.log('Delete item clicked!', row.template_question_id);
  const response = await auth_fetch(`/question/template_question/${row.template_question_id}`, "DELETE");


  if (!response.ok) {
    console.error('Failed to delete item', row.template_question_id);
    return;
  } else {
    console.log('Item deleted successfully', row.template_question_id); //Vymazem
    const response = await initialGetFetch();
    if (!response.ok) {
      const data = await response.json();
      //showError(data.error);
    } else {
      const data = await response.json();
      products.value = data.questions;
      console.log(products.value);
    }
  }
};

const activateItem = async (row) => {
  const response = await auth_fetch(`/question/set_active/${row.template_question_id}`, "PUT", { active: 1 });

  if (!response.ok) {
    console.error('Failed to activate item', row.template_question_id);
    return;
  } else {
    console.log('Item activated successfully', row.template_question_id); //Vymazem
    const response = await initialGetFetch();
    //visible.value = true;
    if (!response.ok) {
      const data = await response.json();
      //showError(data.error);
    } else {
      const data = await response.json();
      products.value = data.questions;
      isActiveQr.value = true;
      isActiveRow.value = row.template_question_id;
      //console.log(products.value);
    }
  }
};

const copyItem = async (row) => {
  console.log('Copy item clicked!', row.template_question_id);

  const response = await auth_fetch(`/question/question_template_copy/${row.template_question_id}`, "POST");

  if (!response.ok) {
    console.error('Failed to copy item', row.template_question_id);
    return;
  } else {
    console.log('Item copied successfully', row.template_question_id); //Vymazem
    const response = await initialGetFetch();
    if (!response.ok) {
      const data = await response.json();
      //showError(data.error);
    } else {
      const data = await response.json();
      products.value = data.questions;
      console.log(products.value);
    }
  }
};

const closeItem = async (row) => {
  console.log('Close item clicked!', row);

  const response = await auth_fetch(`/question/set_active/${row.template_question_id}`, "PUT", { active: 0 });

  if (!response.ok) {
    console.error('Failed to activate item', row.template_question_id);
    return;
  } else {
    console.log('Item closed successfully', row.template_question_id); //Vymazem
    const response = await initialGetFetch();
    if (!response.ok) {
      const data = await response.json();
      //showError(data.error);
    } else {
      const data = await response.json();
      products.value = data.questions;
      console.log(products.value);
    }
  }

};


const editRow = (event, lang) => {
  editQuestion(event.data.id, event.data.question, event.data.category, event.data.active, event.data.type, lang)
}

const editQuestion = (id, question, subject, active, type, lang) => {
  if (id == null) {
    if (lang === 'sk') {
      dialogTitle.value = 'Vytvorenie novej otázky';
    } else if (lang === 'en') {
      dialogTitle.value = 'Create a question';
    }
    dialogQuestion.value = '';
    dialogSubject.value = '';
    dialogActive.value = false;
    dialogId.value = null;
    dialogType.value = 1;
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
