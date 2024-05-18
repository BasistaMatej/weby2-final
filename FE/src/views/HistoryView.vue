<!-- Potreboval by som nejak getnut otazku na zaklade template_question_id -->

<template>

    <AuthNavBar />
    <div id="history-box" class="mt-5">
        <!-- <h1>History ID: {{ template_question_id }}</h1> -->
        <h1 class="mt-3">{{ $t('stats') }}</h1>
        <h2>{{ $t('history') }}</h2>
        <div v-if="template_question_type == 0">
            <p>Toto su otvorené odpovede</p>
            <canvas id="my-chart"></canvas>
        </div>

        <div v-else>
            <p>Tu by som potreboval Datum abo daco</p>
            <canvas v-for="(question, index) in answerData" :key="question.question_id"
                :id="'my-chart-' + index"></canvas>
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
    return (await auth_fetch(`/question/question_history/${template_question_id.value}`));
}

const createChart = (ctx, chartLabels, chartData) => {


    chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: chartLabels,
                    data: chartData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            interaction: {
                mode: 'point',
                intersect: true,
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return 'count: ' + tooltipItem.parsed.y;
                        }
                    }
                }
            }
        },
    });
};

const renderCharts = async () => {
    await nextTick();

    if (template_question_type.value == 0) {
        const ctx = document.getElementById('my-chart');
        if (ctx) {
            let chartLabels = [];
            let chartData = [];
            answerData.value.forEach(item => {
                item.answers.forEach(answer => {
                    chartLabels.push(answer.answer_text);
                    chartData.push(answer.count);
                });
            });
            createChart(ctx, chartLabels, chartData);
        }
    } else {
        answerData.value.forEach((item, index) => {
            const ctx = document.getElementById('my-chart-' + index);
            if (ctx) {
                let chartLabels = [];
                let chartData = [];
                item.answers.forEach(answer => {
                    chartLabels.push(answer.answer_text);
                    chartData.push(answer.count);
                });
                createChart(ctx, chartLabels, chartData);
            }
        });
    }
};




onMounted(async () => {
    template_question_id.value = route.params.id;
    template_question_type.value = route.params.type;
    const response = await initialGetFetch();

    if (!response.ok) {
        const data = await response.json();
    } else {
        const data = await response.json();
        answerData.value = data.questions;
        console.log(answerData.value);

        await nextTick();
        renderCharts();
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

canvas {
    margin-bottom: 1.5rem;
}
</style>
