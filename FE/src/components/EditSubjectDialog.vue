<template>
  <Dialog v-model:visible="visible" modal header="Editovanie predmetov" :style="{ width: '50rem' }">
    <div class="d-flex flex-column">

      <div class="flex align-items-center gap-3 mb-2  modal-buttons">
        <label for="question" class="font-semibold w-6rem" style="min-width:15%">{{ $t('title') }}</label>
        <InputText id="question" class="flex-auto mx-3" autocomplete="off" style="min-width: 65%"
          v-model="subjectText" />
        <Button type="button"  @click="saveSubject($t('lang_id'))" class="mx-3">{{ $t('add') }}</Button>
      </div>

      <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="subjectData" removableSort dataKey="name" selectionMode="single">
          <template #loading> {{ $t('loading') }} </template>
          <Column field="name" :header="$t('title')" sortable >
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
                <Button @click="updateItem($t('lang_id'))"  v-if="slotProps.data.name == editingRowName">
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
                <Button @click="deleteItem(slotProps.data.name, $t('lang_id'))">
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
    <ConfirmDialog></ConfirmDialog>
    <Toast />
  </Dialog>
</template>

<script setup>
import { ref, defineModel, onMounted } from 'vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import { auth_fetch } from '@/utils';

const toast = useToast();
const confirm = useConfirm();

const subjectText = ref('');
const subjectData = ref([]);
const editingRowName = ref('');
const editingRowNew = ref('');

const visible = defineModel();

onMounted(() => {
  fetchAllSubjects('sk');
})

const editItem = (name) => {
  editingRowName.value = name;
  editingRowNew.value = name;
}

const updateItem = async (lang) => {
  if(editingRowName.value === editingRowNew.value) {
    if (lang === 'sk') {
      toast.add({ severity: 'warn', summary: 'Upozornenie', detail: 'Neboli vykonané žiadne zmeny', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'warn', summary: 'Warning', detail: 'No changes made', life: 3000 });
    }
    editingRowName.value = '';
    return;
  }

  const res = await auth_fetch('/subject', 'PUT', {'old_subject_name': editingRowName.value, 'new_subject_name': editingRowNew.value});
  if (!res.ok) {
    const data = await res.json();
    if (lang === 'sk') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Vyskytla sa chyba.', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    }

    return;
  } else {

    if (lang === 'sk') {
      toast.add({ severity: 'success', summary: 'Úspech', detail: 'Predmet aktualizovaný', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Subject updated', life: 3000 });
    }
    editingRowName.value = '';
    fetchAllSubjects(lang);
  }
}

const fetchAllSubjects = async (lang) => {
  const res = await auth_fetch('/subject');
  if (!res.ok) {
    const data = await res.json();
    if (lang === 'sk') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Vyskytla sa chyba.', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    }
    return;
  } else {
    const data = await res.json();

    subjectData.value = data.subjects.map(subject => ({ name: subject }));
  }
}

const deleteItem = async (name, lang) => {
  if (lang === 'sk') {
    confirm.require({
      message: 'Naozaj chceš odstrániť predmet?',
      header: 'Si si istý?',
      icon: 'pi pi-exclamation-triangle',
      rejectClass: 'p-button-secondary p-button-outlined',
      rejectLabel: 'Zrušiť',
      acceptLabel: 'Áno',
      accept: async () => {
        const res = await auth_fetch('/subject/'+name, 'DELETE');
        if (!res.ok) {
          const data = await res.json();
          toast.add({ severity: 'error', summary: 'Error', detail: 'Niekde sa stala chyba', life: 3000 });
          return;
        } else {
          toast.add({ severity: 'success', summary: 'Úspech', detail: 'Predmet odstránený', life: 3000 });
          fetchAllSubjects(lang);
        }
      },
      reject: () => {}
    });
  } else if (lang === 'en') {
    confirm.require({
      message: 'Do you really want to remove the subject?',
      header: 'Are you sure?',
      icon: 'pi pi-exclamation-triangle',
      rejectClass: 'p-button-secondary p-button-outlined',
      rejectLabel: 'Cancel',
      acceptLabel: 'Yes',
      accept: async () => {
        const res = await auth_fetch('/subject/'+name, 'DELETE');
        if (!res.ok) {
          const data = await res.json();
          toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong', life: 3000 });
          return;
        } else {
          toast.add({ severity: 'success', summary: 'Success', detail: 'Subject deleted', life: 3000 });
          fetchAllSubjects(lang);
        }
      },
      reject: () => {}
    });
  }

}

const saveSubject = async (lang) => {
  if (subjectText.value === '') {
    if (lang === 'sk') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Vyplňte text otázky.', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in the question text', life: 3000 });
    }
    return;
  }

  const res = await auth_fetch('/subject', 'POST', {subject_name: subjectText.value});
  if (!res.ok) {
    const data = await res.json();
    if (lang === 'sk') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Vyskytla sa chyba.', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    }

    return;
  } else {
    if (lang === 'sk') {
      toast.add({ severity: 'success', summary: 'Úspech', detail: 'Predmet uložený', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'success', summary: 'Success', detail: 'Subject saved', life: 3000 });
    }
    fetchAllSubjects(lang);
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

.p-dialog-footer button{
  border-radius: 0.7em;
}
</style>

<style scoped>
.options-button,
button {
  cursor: pointer;
}
</style>