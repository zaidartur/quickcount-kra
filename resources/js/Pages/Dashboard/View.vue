<script setup>
import { defineProps } from 'vue';
import Paslon from './Component/Paslon.vue';
import Grafik from './Component/Grafik.vue';

const datas = defineProps({
    apps: Object,
    paslon: Object,
    kec: Object,
    desa: Object,
    suara: Object,
})
</script>

<template>
    <Head title="Dashboard" />

    <Paslon :paslon="datas.paslon" :kec="datas.kec" :desa="datas.desa" :suara="datas.suara" />
    <Grafik />
</template>