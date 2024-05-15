<template>
  <Dialog v-model:visible="visible" modal header="Editovanie predmetov" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">

      <div class="flex align-items-center gap-3 mb-2  modal-buttons">
        <label for="question" class="font-semibold w-6rem" style="min-width:15%">Názov</label>
        <InputText id="question" class="flex-auto mx-3" autocomplete="off" style="min-width: 65%"
          v-model="subjectText" />
        <Button type="button"  @click="saveSubject" class="mx-3">Pridať</Button>
      </div>

      <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="subjectData" removableSort dataKey="name" selectionMode="single">
          <template #loading> {{ $t('loading') }} </template>
          <Column field="name" header="Názov" sortable >
            <template #body="slotProps" :data-rowIndex="slotProps.rowIndex">
              <InputText v-if="slotProps.data.name == editingRowName" v-model="editingRowNew" />
              <span  v-else >
                {{ slotProps.data.name }}
              </span>
            </template>
          </Column>
          <Column field="tools" :header="$t('tools')">
            <template #body="slotProps">
              <div class="d-flex tools-buttons">
                <Button @click="updateItem()"  v-if="slotProps.data.name == editingRowName">
                  <lord-icon
                    src="https://cdn.lordicon.com/cgzlioyf.json"
                    trigger="hover"
                    stroke="bold"
                    colors="primary:#121331,secondary:#8b5cf6"
                    style="width: 1.3em;height: 1.3em">
                </lord-icon>
                </Button>
                <Button @click="editItem(slotProps.data.name)" v-else>
                  <lord-icon
                    src="https://cdn.lordicon.com/wuvorxbv.json"
                    trigger="hover"
                    stroke="bold"
                    colors="primary:#121331,secondary:#8b5cf6"
                    style="width: 1.3em;height: 1.3em">
                  </lord-icon>
                </Button>
                <Button @click="deleteItem(slotProps.data.name)">
                  <lord-icon
                    src="https://cdn.lordicon.com/drxwpfop.json"
                    trigger="hover"
                    stroke="bold"
                    colors="primary:#121331,secondary:#8b5cf6"
                    style="width: 1.3em;height: 1.3em">
                </lord-icon>
                </Button>
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
    <Toast />
  </Dialog>
</template>

<script setup>
import { ref, defineModel, onMounted } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import { auth_fetch } from '@/utils';

const toast = useToast();

const subjectText = ref('');
const subjectData = ref([]);
const editingRowName = ref('');
const editingRowNew = ref('');

const visible = defineModel();

onMounted(() => {
  fetchAllSubjects();
})

const editItem = (name) => {
  editingRowName.value = name;
  editingRowNew.value = name;
}

const updateItem = async () => {
  if(editingRowName.value === editingRowNew.value) {
    toast.add({ severity: 'warn', summary: 'Warning', detail: 'No changes made', life: 3000 });
    editingRowName.value = '';
    return;
  }

  const res = await auth_fetch('/subject', 'PUT', {'old_subject_name': editingRowName.value, 'new_subject_name': editingRowNew.value});
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Subject updated', life: 3000 });
    editingRowName.value = '';
    fetchAllSubjects();
  }
}

const fetchAllSubjects = async () => {
  const res = await auth_fetch('/subject');
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    const data = await res.json();

    subjectData.value = data.subjects.map(subject => ({ name: subject }));

    console.log(subjectData.value);
  }
}

const deleteItem = async (name) => {
  const res = await auth_fetch('/subject/'+name, 'DELETE');
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Subject deleted', life: 3000 });
    fetchAllSubjects();
  }
}

const saveSubject = async () => {
  if (subjectText.value === '') {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in the question text', life: 3000 });
    return;
  }

  const res = await auth_fetch('/subject', 'POST', {subject_name: subjectText.value});
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Subject saved', life: 3000 });
    fetchAllSubjects();
    subjectText.value = '';
  }
};
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

.tools-buttons button {
  background: transparent;
  border: none;
}
</style>

<style scoped>
.options-button,
button {
  cursor: pointer;
}
</style>