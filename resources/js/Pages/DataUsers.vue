<script setup>
import { defineProps } from 'vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import Users from '@/Pages/Users/View.vue'
import crypto from 'crypto'

const datas = defineProps({
    apps: Object,
    users: Object,
    kec: Object,
    desa: Object,
})

const decrypt = (data) => {
    //
}
// console.log('aa',import.meta.env.VITE_APP_KEY)
</script>

<template>
    <app-layout :apps="datas.apps">
        <Users :users="datas.users" :kec="datas.kec" :desa="datas.desa" />
    </app-layout>
</template>

<style scoped lang="scss">
    
</style>