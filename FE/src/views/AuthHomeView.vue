<template>
  <div>
    <AuthNavBar />
    <div class="container">
      <div class="px-md-5">
        <div>
          <div class="d-inline-block">
            <div @click="editQuestion(null, null, null, null, null)"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>{{$t('create_question')}}</span>
            </div>
          </div>
        </div>
        <span class="d-inline-block" style="margin-top: 2em; font-size: 80%; color: rgba(0,0,0,0.5)">
          {{$t('filter_several_columns')}}
        </span>
        <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="products" removableSort dataKey="id" selectionMode="single" @rowSelect="editRow">
          <template #empty>
            <div class="d-flex flex-column align-items-center">
              <lord-icon src="https://cdn.lordicon.com/ribxmuoc.json" trigger="loop" delay="700"
                colors="primary:#121331,secondary:#8b5cf6" style="width:250px;height:250px">
              </lord-icon>
              <h4>
                {{$t('no_question_find')}}
              </h4>
            </div>
          </template>
          <template #loading> {{$t('loading')}} </template>
          <Column field="question" :header="$t('question')" sortable></Column>
          <Column field="subject" :header="$t('subject')" sortable></Column>
          <Column field="created" :header="$t('created')" sortable></Column>
          <Column field="code" :header="$t('code')" sortable></Column>
          <Column field="tools" :header="$t('tools')" ></Column>
        </DataTable>
      </div>
    </div>
    <EditQuestionDialog v-model="showDialog" :title="dialogTitle" :category="dialogSubject" :id="dialogId"
      :isActive="dialogActive" :question="dialogQuestion" :type="dialogType" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import AuthNavBar from '@/components/AuthNavBar.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import EditQuestionDialog from '@/components/EditQuestionDialog.vue';
import { FilterMatchMode } from 'primevue/api';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

const showDialog = ref(false);
const dialogTitle = ref($t('new_question_creation'));
const dialogQuestion = ref('');
const dialogSubject = ref('');
const dialogActive = ref(false);
const dialogId = ref(null);
const dialogType = ref(1);

const products = ref([]);

const productsSample = ref([
  { id: 0, question: 'Ako sa po čínsky povie "Bryndzové halušky"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '', type: 1 },
  { id: 12, question: 'Koľko je 2+2?', subject: 'Matematika', created: '01-03-2024', code: 'MATH15', tools: '', type: 2 },
  { id: 66, question: 'Odkiaľ pochádza slovo "káva"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '', type: 1 },
]);

const editRow = (event) => {
  editQuestion(event.data.id, event.data.question, event.data.category, event.data.active, event.data.type)
}

const editQuestion = (id, question, subject, active, type) => {
  if (id == null) {
    dialogTitle.value = $t('new_question_creation');
    dialogQuestion.value = '';
    dialogSubject.value = '';
    dialogActive.value = false;
    dialogId.value = null;
    dialogType.value = 1;
  } else {
    dialogTitle.value = $t('edit_question');
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
</style>
