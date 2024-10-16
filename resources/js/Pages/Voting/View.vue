<script setup>
import { defineProps, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';


const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    users: Object,
    desa: Object,
})


</script>

<template>
    <Head title="Suara Masuk" />
</template>