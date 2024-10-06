<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

import AppMenuItem from './AppMenuItem.vue';

const user = usePage().props.auth.user;
const model = ref([
    {
        label: 'MAIN MENU',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' },
            { label: 'Statistik', icon: 'pi pi-database', to: '/statistik' },
        ]
    },
    {
        label: 'MASTER DATA',
        items: [
            { label: 'Data Wilayah', icon: 'pi pi-map', to: '/data-wilayah' },
            { label: 'DPT', icon: 'pi pi-users', to: '/daftar-pemilih-tetap' },
        ]
    },
    {
        label: 'SETTING',
        items: [
            { label: 'Setting', icon: 'pi pi-cog', to: '/setting' },
            // { label: 'Profile', icon: 'pi pi-user', to: '/setting' },
            { label: user.name, icon: 'pi pi-user', to: '/setting' }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
