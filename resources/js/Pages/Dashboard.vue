<script setup>
import { defineProps } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Dashboard  from "@/Pages/Dashboard/View.vue";

const data = defineProps({
    apps: Object,
    paslon: Object,
    kec: Object,
    desa: Object,
    alldata: Object,
    grafik: Object,
})
</script>

<template>
    <app-layout :apps="data.apps">
        <Dashboard :apps="data.apps" :paslon="data.paslon" :suara="data.alldata" :kec="data.kec" :desa="data.desa" :grafik="data.grafik" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    