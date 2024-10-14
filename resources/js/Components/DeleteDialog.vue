<script setup>
import { defineProps, ref } from 'vue';

const d = defineProps({
    label: String,
    showDialogs: {
        type: Boolean,
        default: false,
    },
    submitted: Boolean
})

const deleteDialog = ref(d.showDialogs)
console.log(d.submitted)
</script>

<template>
    <Dialog v-model:visible="d.showDialogs" :style="{ width: '450px' }" header="Confirm" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span v-if="d.label">Anda ingin menghapus data <b>{{ d.label }}</b> ?</span>
        </div>
        <template #footer>
            <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="d.showDialogs = false" :disabled="submitted" />
            <!-- <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="d.deleteProduct" :disabled="submitted" /> -->
        </template>
    </Dialog>
</template>