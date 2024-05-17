<template>
  <div>
    <div>
      <UserManualNavBar />
      <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
        <h1 class="roboto-black h1 text-center" >{{ $t('up_user_manual') }}</h1>
<!--        <h4 class="text-center">{{ $t('up_ex_user') }}</h4>-->
      </div>
    </div>

    <div class="manual-container d-flex justify-content-center align-items-center">
      <div class="button-container d-flex justify-content-end w-100">
        <Button class="ml-5" @click="generatePDF">{{$t('to_pdf')}}</Button>
      </div>
      <div ref="manualContent" class="manual-content">
        <section>
          <h3 class="roboto-black">{{$t('lang_id')}}</h3>
          {{$t('lang_id')}}
        </section>
        <section>
          {{$t('lang_id')}}
        </section>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue';
import UserManualNavBar from '../components/UserManualNavBar.vue';
import { useRouter } from 'vue-router';
import Button from "primevue/button";
import html2pdf from 'html2pdf.js';

const router = useRouter();
const code = ref(null)
const manualContent = ref(null);
const isMobile = ref(window.innerWidth < 500);

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth < 500;
});

const generatePDF = () => {
  const element = manualContent.value;
  const opt = {
    margin: 1,
    filename: 'UserManual.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
  };

  html2pdf().from(element).set(opt).save();
};
</script>

<style scoped>
h4 {
  text-decoration: underline dotted #8B5CF6aa;
  color: #8B5CF6ee;
}

.manual-container {
  width: 85%;
  height: 100%;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.manual-content {
  text-align: left;
  width: 70%;
}

.button-container {
  width: 100%;
  display: flex;
  justify-content: flex-end;
  margin-bottom: 1rem;
}

Button {
  border-radius: 1em;
  margin: 1rem;
}
</style>