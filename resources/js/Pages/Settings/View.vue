<script setup>
import { defineProps } from 'vue';
import Paslon from './components/Paslon.vue';
import AppConfig from './components/AppConfig.vue';

const datas = defineProps({
    paslon: Object,
    config: Object,
})
</script>


<template>
    <Head title="Setting" />

    <AppConfig :app="datas.config" />
    <Paslon :lists="datas.paslon" />
</template>