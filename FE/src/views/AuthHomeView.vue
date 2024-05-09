<template>
  <div>
    <AuthNavBar />
    <div class="container">
      <div class="px-5">
        <div>
          <div class="d-inline-block">
            <router-link to="/create-question"
              class="d-flex flex-columns align-items-center align-content-center p-2 table-link">
              <lord-icon src="https://cdn.lordicon.com/zrkkrrpl.json" trigger="hover" stroke="bold"
                style="width:2em;height:2em" colors="primary:#121331,secondary:#8b5cf6">
              </lord-icon>
              <span>Vytvoriť otázku</span>
            </router-link>
          </div>
        </div>
        <span class="d-inline-block" style="margin-top: 2em; font-size: 80%; color: rgba(0,0,0,0.5)">Pri podržaní klávesy
          Ctrl (prip. CMD) je
          možné filtrovať
          viacero stľpov
          naraz!</span>
        <DataTable class="auth-table" stripedRows paginator :rows="50" :rowsPerPageOptions="[50, 100, 200]"
          sortMode="multiple" :value="products" removableSort dataKey="id">
          <template #empty>
            <div class="d-flex flex-column align-items-center">
              <lord-icon src="https://cdn.lordicon.com/ribxmuoc.json" trigger="loop" delay="500"
                colors="primary:#121331,secondary:#8b5cf6" style="width:250px;height:250px">
              </lord-icon>
              <h4>
                Neboli nájdene žiadne otázky. Začnite vytvorením!
              </h4>
            </div>
          </template>
          <template #loading>Načitávam</template>
          <Column field="question" header="Otázka" sortable></Column>
          <Column field="subject" header="Predmet" sortable></Column>
          <Column field="created" header="Vytvorené" sortable></Column>
          <Column field="code" header="Kód" sortable></Column>
          <Column field="tools" header="Nástroje"></Column>
        </DataTable>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import AuthNavBar from '@/components/AuthNavBar.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { FilterMatchMode } from 'primevue/api';
import ColumnGroup from 'primevue/columngroup';
import Row from 'primevue/row';

const products = ref([]);

const productsSample = ref([
  { id: 0, question: 'Ako sa po čínsky povie "Bryndzové halušky"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '' },
  { id: 12, question: 'Koľko je 2+2?', subject: 'Matematika', created: '01-03-2024', code: 'MATH15', tools: '' },
  { id: 66, question: 'Odkiaľ pochádza slovo "káva"?', subject: 'Jazyk', created: '01-03-2024', code: 'JSH15', tools: '' },
]);

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
