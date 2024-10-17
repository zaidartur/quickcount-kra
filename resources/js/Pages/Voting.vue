<script setup>
import { defineProps } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import Vote from '@/Pages/Voting/View.vue'
import { data } from 'autoprefixer';

const datas = defineProps({
        apps: Object,
        users: Object,
        kec: Object,
        desa: Object,
        paslon: Object,
    })
</script>

<template>
    <app-layout :apps="datas.apps">
        <Vote :users="datas.users" :kec="datas.kec" :desa="datas.desa" :paslon="datas.paslon" />
    </app-layout>
</template>

<style scoped lang="scss">
    
</style>