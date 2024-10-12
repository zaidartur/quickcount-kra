<script setup>

const config = defineProps({
    apps: Object
})
</script>

<template>
    <div class="layout-footer">
        <b>{{ config.apps?.app_name }}</b> by <span v-if="!config.apps?.app_website">{{ config.apps?.app_pemilik }}</span>
        <a :href="config.apps?.app_website" target="_blank" rel="noopener noreferrer" class="text-primary font-bold hover:underline" v-if="config.apps?.app_website && config.apps?.app_pemilik">{{ config.apps?.app_pemilik }}</a>
        &copy;{{ config.apps?.app_tahun }}
    </div>
</template>
