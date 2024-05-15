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
          <Column field="question" :header="$t('question')" sortable></Column>
          <Column field="subject" :header="$t('subject')" sortable></Column>
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
                <Button class="row-buttons" @click="closeItem(slotProps.data)">
                  <lord-icon src="https://cdn.lordicon.com/mwikjdwh.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button class="row-buttons" @click="activateItem(slotProps.data)">
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
            </template>
          </Column>
        </DataTable>
      </div>
    </div>
    <EditQuestionDialog v-model="showDialog" :title="$t('new_question_creation')" :category="dialogSubject"
      :id="dialogId" :isActive="dialogActive" :question="dialogQuestion" :type="dialogType" :lang_id="$t('lang_id')" />
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

const products = ref([]);

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
  console.log('Delete item clicked!', row.id);


  const response = await fetch(``, {
    method: 'DELETE',
    body: JSON.stringify({
      id: row.id
    })
  });

  if (!response.ok) {
    console.error('Failed to delete item', row.id);
    return;
  } else {
    console.log('Item deleted successfully', row.id); //Vymazem
  }
};

const activateItem = (row) => {
  console.log('Activate item clicked!', row);
};

const copyItem = (row) => {
  console.log('Copy item clicked!', row);
};

const closeItem = (row) => {
  console.log('Close item clicked!', row);
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
