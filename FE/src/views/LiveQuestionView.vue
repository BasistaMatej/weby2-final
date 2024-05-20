
<template>
  <div class="container">
    <Card v-if="errorText !== ''" class="text-center">
      <template #title>Chyba</template>
      <template #content>
          <h2 class="my-4">
            {{ errorText }}
          </h2>
          <Button @click="toHome" type="submit" label="submit" style="border-radius: 1em">
            {{ $t('home') }}
            <lord-icon
              src="https://cdn.lordicon.com/laqlvddb.json"
              trigger="hover"
              stroke="bold"
              colors="primary:#eee,secondary:#ffffff"
              style="width:2em;height:2em;margin-left:1em;"
            >
            </lord-icon>
          </Button>
      </template>
    </Card>
    <div v-else>
      <div v-if="!answered">
        <OpenQuestion question="adskljssa" @button-clicked="handleButtonClick" />
      </div>
      <div v-else>
        ODPOVEDE
      </div>
    </div>
    
  </div>
</template>

<script setup>
import OpenQuestion from '@/components/OpenQuestion.vue';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Button from 'primevue/button';
import Card from 'primevue/card';

const route = useRoute();
const router = useRouter();
const errorText = ref('');
const socket = new WebSocket('ws://localhost:9999/wss');
const started = ref(false);
const init = ref(false);
const code = ref(null);
const answered = ref(false);

const myAnswer = ref('');
const allAnswers = ref([]);

socket.onopen = () => {
  
};

socket.onmessage = (event) => {
  console.log("WebSocket message received:", event.data);
  const data = JSON.parse(event.data);
  switch(data.type) {
    case 'initBE':
      started.value = true;
      sendMessage('questionInfo', code.value);
      //sendMessage('initPlayer', code.value, {'answer': 'jano'});
      break;
    case 'RESPONSE: initPlayer':
      myAnswer.value = data.my_answer;
      allAnswers.value = data.all_answers;
      break;
    case 'updateAnswers':
      allAnswers.value.push(data.answers);
      break;
    case 'error':
      errorText.value = data.message;
      break;
  }
};

const sendMessage = (type, code, fields) => {
  const data = {'type': type, 'roomKey': code, ...fields};
  socket.send(JSON.stringify(data));
}

const handleButtonClick = (answer) => {
  sendMessage('initPlayer', code.value, {'answer': answer});
  answered.value = true;
};

onMounted(() => {
  // connect to websockets
  if(route.params.code.length !== 5) {
    errorText.value = 'Neplatný kód';
    return;
  }
  code.value = route.params.code;
});

const toHome = () => {
  router.push("/");
}

</script>