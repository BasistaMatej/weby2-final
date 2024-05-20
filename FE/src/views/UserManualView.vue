<template>
  <div>
    <div>
      <UserManualNavBar />
      <div class="d-flex justify-content-center align-items-center flex-column w-100 p-3 h-100">
        <h1 class="roboto-black h1 text-center">{{ $t('up_user_manual') }}</h1>
      </div>
    </div>

    <div class="d-flex justify-content-center align-items-center">
      <div class="manual-container">
        <div class="button-container d-flex justify-content-end w-100">
          <Button class="ml-5" @click="generatePDF">{{ $t('to_pdf') }}</Button>
        </div>
        <div ref="manualContent" class="d-flex align-items-center flex-column">
          <section>
            <h4>{{ $t('up_instructions_for_non_logged_in_user') }}</h4>
            <h5>{{ $t('main_page') }}</h5>
            {{ $t('main_page_text') }}
            <h5 class="h-padding">{{ $t('registration_page') }}</h5>
            {{ $t('registration_page_text') }}
          </section>
          <section>
            <h4>{{ $t('up_instructions_for_logged_in_user') }}</h4>
            {{ $t('instructions_for_logged_in_user_text') }}
            <h5 class="h-padding">{{ $t('user_home_page') }}</h5>
            {{ $t('user_home_page_text') }}
            <h5 class="h-padding">{{ $t('profile_page') }}</h5>
            {{ $t('profile_page_text') }}
          </section>
          <section>
            <h4>{{ $t('up_instructions_for_administrator') }}</h4>
            {{ $t('instructions_for_administrator_text') }}
          </section>
          <br><br>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import UserManualNavBar from '../components/UserManualNavBar.vue';
import { useRouter } from 'vue-router';
import Button from 'primevue/button';
import html2pdf from 'html2pdf.js';

const router = useRouter();
const code = ref(null);
const manualContent = ref(null);
const isMobile = ref(window.innerWidth < 500);

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth < 500;
});

const generatePDF = () => {
  const element = manualContent.value;
  const opt = {
    margin: [1.3, 1, 1.3, 1],
    filename: 'UserManual.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true },
    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
  };

  html2pdf()
      .from(element)
      .set(opt)
      .toPdf()
      .get('pdf')
      .then(pdf => {
        const totalPages = pdf.internal.getNumberOfPages();

        for (let i = 1; i <= totalPages; i++) {
          pdf.setPage(i);
          pdf.setFontSize(10);
          pdf.text(
              `Page ${i} of ${totalPages}`,
              pdf.internal.pageSize.getWidth() / 2,
              pdf.internal.pageSize.getHeight() - 0.5,
              { align: 'center' }
          );
        }
      })
      .save();
};
</script>

<style scoped>
h4 {
  text-decoration: underline dotted #8b5cf6aa;
  color: #8b5cf6ee;
  padding-bottom: 0.25rem;
}

.h-padding {
  padding-top: 0.75rem;
}

section {
  padding-bottom: 1.7rem;
}

.manual-container {
  width: 65%;
  height: 100%;
  padding: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: left;
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
