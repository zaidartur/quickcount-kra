<script setup>
import { defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Kecamatan from './Kecamatan.vue';
import Desa from './Desa.vue';

const datas = defineProps({
    wilayah: Object
})
const page  = usePage()
const auth = page.props.auth.user

// console.log(datas.wilayah.desa)

</script>

<template>
    <Head title="Data Wilayah" apps="AA" />
    <Kecamatan :kec="datas.wilayah.kec" v-if="auth.level < 2" />
    <Desa :desa="datas.wilayah.desa" :kec="datas.wilayah.kec" v-if="auth.level < 3" />
</template>