<script setup>
import { defineProps } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Profile from '@/Pages/Profile/View.vue'

const data = defineProps({
    apps: Object,
    detail: Object
})
</script>

<template>
    <app-layout :apps="data.apps">
        <Profile :detail="data.detail" />
    </app-layout>
</template>
    
<style scoped lang="scss">
    
</style>
    