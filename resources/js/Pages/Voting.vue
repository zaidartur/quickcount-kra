<script setup>
import { defineProps } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import Vote from '@/Pages/Voting/View.vue'

const datas = defineProps({
        apps: Object,
        users: Object,
        desa: Object,
    })
</script>

<template>
    <app-layout :apps="datas.apps">
        <Vote :users="datas.users" :desa="datas.desa" />
    </app-layout>
</template>

<style scoped lang="scss">
    
</style>