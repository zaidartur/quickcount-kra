<script setup>
import { defineProps } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import Statistik from "@/Pages/Statistik/View.vue";

const datas = defineProps({
    apps: Object,
    paslon: Object,
    kec: Object,
    desa: Object,
    statkec: Object,
    dpt: Number,
})
</script>

<template>
    <app-layout :apps="datas.apps">
        <Statistik :paslon="datas.paslon" :kec="datas.kec" :desa="datas.desa" :stat-kec="datas.statkec" :dpt="datas.dpt" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    