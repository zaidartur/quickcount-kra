<script setup>
    import { defineProps } from "vue";
    import AppLayout from "@/Layouts/AppLayout.vue";
    import Setting  from "@/Pages/Settings/View.vue";

    const datas = defineProps({
        apps: Object,
        paslon: Object,
    })
</script>

<template>
    <app-layout :apps="datas.apps">
        <Setting :config="datas.apps" :paslon="datas.paslon" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    