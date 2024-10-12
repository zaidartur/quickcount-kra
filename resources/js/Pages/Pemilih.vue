<script setup>
    import AppLayout from "@/Layouts/AppLayout.vue";
    import Pemilih  from "@/Pages/Dpt/View.vue";

    const apps = defineProps({
        apps: Object,
        dpt: Object,
        kec: Object,
        desa: Object,
    })
</script>

<template>
    <app-layout :apps="apps.apps">
        <Pemilih :dpt="apps.dpt" :kec="apps.kec" :desa="apps.desa" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    