<script setup>
import { defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import Vote from '@/Pages/Voting/ViewDesa.vue'

const datas = defineProps({
        apps: Object,
        users: Object,
        kec: Object,
        desa: Object,
        paslon: Object,
        mydata: Object,
    })
const page  = usePage()
const auth = page.props.auth.user
</script>

<template>
    <app-layout :apps="datas.apps">
        <Vote :users="datas.users" :kec="datas.kec" :desa="datas.desa" :paslon="datas.paslon" :mydata="datas.mydata" v-if="auth.level === 3" />
    </app-layout>
</template>

<style scoped lang="scss">
    
</style>