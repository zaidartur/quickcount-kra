<script setup>
    import AppLayout from "@/Layouts/AppLayout.vue";
    import Wilayah  from "@/Pages/Wilayah/View.vue";
    import { defineProps } from 'vue';

    const datas = defineProps({
        kec: Object,
        desa: Object,
        apps: Object,
    })
</script>

<template>
    <app-layout :apps="datas.apps">
        <Wilayah :wilayah="datas" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    