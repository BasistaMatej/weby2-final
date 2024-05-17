<!-- Potreboval by som nejak getnut otazku na zaklade template_question_id -->

<template>

    <AuthNavBar />
    <div id="history-box" class="mt-5">
        <!-- <h1>History ID: {{ template_question_id }}</h1> -->
        <h1>Štatistika</h1>
        <h2>História</h2>
        <div v-if="template_question_type == 1">
            <p>Toto je s odpovedami</p>
            <canvas id="my-chart"></canvas>
        </div>

        <div v-else class="card flex justify-content-center">
            <h2>Otvorené odpovede</h2>
            <Listbox v-model="answers" :options="openAnswers" optionLabel="name" class="w-full md:w-14rem" />
        </div>
    </div>

</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import AuthNavBar from '@/components/AuthNavBar.vue';
import { auth_fetch } from '@/utils';
import { Chart, BarController, CategoryScale, LinearScale, BarElement, Tooltip } from 'chart.js';
import ListBox from 'primevue/listbox';

Chart.register(BarController, CategoryScale, LinearScale, BarElement, Tooltip);

const route = useRoute();
const template_question_id = ref(null);
const template_question_type = ref(null);
const answerData = ref([]);
const answers = ref();
const openAnswers = ref([]);

let chart = null;


const initialGetFetch = async () => {
    return (await auth_fetch(`/question/answers/${template_question_id.value}`));
}
onMounted(async () => {
    template_question_id.value = route.params.id;
    template_question_type.value = route.params.type;
    const response = await initialGetFetch();

    if (!response.ok) {
        const data = await response.json();
    } else {
        const data = await response.json();
        answerData.value = data.answers;
        console.log(answerData.value);

        await nextTick();
        const ctx = document.getElementById('my-chart');
        openAnswers.value = answerData.value.map(item => item.answer_text);
        if (ctx) {
            console.log("ctx");
            const chartLabels = answerData.value.map(item => item.answer_text);
            const chartData = answerData.value.map(item => item.count);

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: chartLabels,
                        data: chartData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    interaction: {
                        mode: 'point',
                        intersect: true
                    },
                }
            });
        }
    }
});
</script>

<style scoped>
#history-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 80%;
    margin: 0 auto;
    background: white;
    border-radius: 1rem;
}
</style>
