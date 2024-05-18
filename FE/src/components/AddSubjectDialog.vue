<template>
  <Dialog v-model:visible="visible" modal header="Pridať predmet" :style="{ width: '25rem' }">
    <div class="d-flex flex-column">
      <div class="flex align-items-center gap-3 mb-2">
        <label for="question" class="font-semibold w-6rem" style="min-width:15%">{{ $t('title') }}</label>
        <InputText id="question" class="flex-auto mx-3" autocomplete="off" style="min-width: 80%"
          v-model="subjectText" />
      </div>

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
                <Button class="row-buttons" @click="deleteItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/nqtddedc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button class="row-buttons" @click="closeItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/mwikjdwh.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
                <Button class="row-buttons" @click="activateItem(slotProps.data, $t('lang_id'))">
                  <lord-icon src="https://cdn.lordicon.com/aklfruoc.json" trigger="hover"
                    style="width:25px;height:25px">
                  </lord-icon>
                </Button>
              </div>
            </template>
          </Column>
        </DataTable>

      <div class="flex justify-content-end gap-2 modal-buttons">
        <Button type="button" severity="secondary" @click="visible = false">{{$t('cancel')}}</Button>
        <Button type="button"  @click="saveSubject($t('lang_id'))" class="mx-3">{{$t('save')}}</Button>
      </div>
    </div>
    <Toast />
  </Dialog>
</template>

<script setup>
import { ref, defineModel} from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import { useToast } from "primevue/usetoast";
import { auth_fetch } from '@/utils';

const toast = useToast();

const subjectText = ref('');

const visible = defineModel();



const saveSubject = async (lang) => {
  if (subjectText.value === '') {
    if (lang === 'sk') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Prosím, vyplňte text otázky.', life: 3000 });
    } else if (lang === 'en') {
      toast.add({ severity: 'error', summary: 'Error', detail: 'Please fill in the question text.', life: 3000 });
    }
    return;
  }

  const res = await auth_fetch('/subject', 'POST', {subject_name: subjectText.value});
  if (!res.ok) {
    const data = await res.json();
    toast.add({ severity: 'error', summary: 'Error', detail: data.error, life: 3000 });
    return;
  } else {
    toast.add({ severity: 'success', summary: 'Success', detail: 'Subject saved', life: 3000 });
    visible.value = false;
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
</style>

<style scoped>
.options-button,
button {
  cursor: pointer;
}
</style>
