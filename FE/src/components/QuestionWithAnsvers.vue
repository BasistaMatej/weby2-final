<template>
    <div class="card">
        <h1 class="text-center">Aká farba je tvoja oblúbená ?</h1>
        <div class="flex flex-column gap-3">
            <div class="box-line" v-for="ansver of ansvers" :key="ansver.id">
                <Checkbox v-model="selectedAnsvers" name="ansver" :value="ansver.name" />
                <label :for="ansver.id">{{ ansver.name }}</label>
            </div>
        </div>
    </div>

</template>

<script setup>
import { ref, watch } from "vue";
import Checkbox from 'primevue/checkbox';

const ansvers = ref([
    { name: "Zelena", id: "1" },
    { name: "Biela", id: "2" },
    { name: "Cierna", id: "3" },
    { name: "Modra", id: "4" }
]);
const selectedAnsvers = ref([]);

watch([selectedAnsvers], () => {
    console.log(selectedAnsvers.value);
})

</script>

<style scoped>
h1 {
    margin-top: 1.5rem;
}

label {
    font-size: 1.5rem;
}

.p-checkbox {
    margin-right: .5rem;
    margin-bottom: .5rem;
}

.card {
    width: 70%;
    margin: 0 auto;
    display: flex;
    align-items: center;
    border-radius: 2rem;
}

.box-line:last-child {
    margin-bottom: 2rem;
}

#question-box {
    display: flex;
    flex-direction: column;
    align-items: center;

}
</style>
