
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
        <OpenQuestion :question="questtionText" @button-clicked="handleButtonClick" v-if="type == 0" />
        <QuestionWithAnsvers :question="questtionText" @button-clicked="handleButtonClick" :answers="choiceAnswers" v-else />
      </div>
      <div v-else>
        <div>
          <h1 class="text-center">Odpovede</h1>
          <div class="flex flex-column gap-3">
            <div class="box-line my-1" v-for="answer in allAnswers" :key="answer.answer_id">
              <div class="flex flex-row gap-3 px-2 py-1" :style="'background: var(--primary-color); width: '+answer.count/maxAns*100+'%; border-radius: 1em'">
                <h2 style="color: #444">{{ answer.answer_id }}</h2>
                <h5 style="color: #444">{{ answer.count }}</h5>
              </div>
            </div>
          </div>
        </div>
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
import QuestionWithAnsvers from '@/components/QuestionWithAnsvers.vue';

const route = useRoute();
const router = useRouter();
const errorText = ref('');
const socket = new WebSocket('ws://localhost:9999/wss');
const started = ref(false);
const init = ref(false);
const code = ref(null);
const answered = ref(false);
const questtionText = ref('');
const type = ref(0);

const maxAns = ref(0);

const choiceAnswers = ref([]);

const myAnswer = ref('');
const allAnswers = ref([]);

socket.onopen = () => {
  
};

socket.onmessage = async (event) => {
  //console.log("WebSocket message received:", event.data);
  const data = JSON.parse(event.data);
  switch(data.type) {
    case 'initBE':
      started.value = true;
      const res = await fetch('http://localhost:5151/question/code/'+code.value);

      if(res.status != 200) {
        errorText.value = 'Kód otázky neexistuje';
        return;
      }

      const question = (await res.json()).question;

      questtionText.value = question.template_question_text;
      type.value = question.type;
      if(type.value == 1) { // with answers
        const res = await fetch('http://localhost:5151/question/answers/'+question.template_question_id);
        const answers = (await res.json()).answers;
        choiceAnswers.value = answers;
      }
      break;
    case 'RESPONSE: initPlayer':
      myAnswer.value = data.my_answer;
      let updatedAllAnswers = [];

        for (const key in data.all_answers) {
          updatedAllAnswers.push({answer_id: key, count: data.all_answers[key]});
        }
      
      updatedAllAnswers.sort((a, b) => b.count - a.count);
      maxAns.value = updatedAllAnswers[0].count;

      allAnswers.value = updatedAllAnswers;
      break;
    case 'updateAnswers':
      if(type.value === 1) {
        const obj = allAnswers.value.find(i => i.answer_id.toString() == data.answers.toString());

        if(obj) {
          obj.count += 1;
        } else {
          allAnswers.value.push({answer_id: data.answers, count: 1});
        }
        const updatedAllAnswers = allAnswers.value;
        updatedAllAnswers.sort((a, b) => b.count - a.count);
        maxAns.value = updatedAllAnswers[0].count;
      }
      break;
    case 'closeRoom':
      if(data.roomKey === code.value) {
        errorText.value = 'Hlasovanie bolo ukončené';
      }
      break;
    case 'error':
      errorText.value = data.message;
      break;
  }
};

socket.onclose = (event) => {
  //console.log("WebSocket connection closed:", event.code);
};

const sendMessage = (type, code, fields) => {
  const data = {'type': type, 'roomKey': code, ...fields};
  socket.send(JSON.stringify(data));
}

const handleButtonClick = (answer) => {
  sendMessage('initRoom', code.value);
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