<script setup>
import { defineProps } from 'vue';
import Kecamatan from './Kecamatan.vue';
import Desa from './Desa.vue';

const datas = defineProps({
    wilayah: Object
})

// console.log(datas.wilayah.desa)

</script>

<template>
    <Head title="Data Wilayah" />
    <Kecamatan :kec="datas.wilayah.kec" />
    <Desa :desa="datas.wilayah.desa" />
</template>