<script setup>
import { defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import Vote from '@/Pages/Voting/ViewDesa.vue'
import ViewKecamatan from './Voting/ViewKecamatan.vue';
import ViewTPS from './Voting/ViewTPS.vue';

const datas = defineProps({
        apps: Object,
        users: Object,
        kec: Object,
        desa: Object,
        paslon: Object,
        mydata: Object,
        profile: Object,
        dpt: Number,
    })
const page  = usePage()
const auth = page.props.auth.user
</script>

<template>
    <Head title="Suara Masuk" />
    <app-layout :apps="datas.apps">
        <ViewTPS :users="datas.users" :kec="datas.kec" :desa="datas.desa" :paslon="datas.paslon" :mydata="datas.mydata" :role="datas.profile" v-if="auth.level === 4" />
        <Vote :users="datas.users" :kec="datas.kec" :desa="datas.desa" :paslon="datas.paslon" :mydata="datas.mydata" :role="datas.profile" :sum-d-p-t="datas.dpt" v-if="auth.level === 3" />
        <ViewKecamatan :users="datas.users" :kec="datas.kec" :desa="datas.desa" :paslon="datas.paslon" :mydata="datas.mydata" :sum_dpt="datas.dpt" v-if="auth.level === 2" />
    </app-layout>
</template>

<style scoped lang="scss">
    
</style>