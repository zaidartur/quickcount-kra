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
            { label: 'DPT', icon: 'pi pi-users', to: '/daftar-pemilih-tetap' },
        ]
    },
    {
        label: 'SETTING',
        items: [
            { label: 'Data User', icon: 'pi pi-users', to: '/data-user' },
            { label: user.name, icon: 'pi pi-user', to: '/profile' }
        ]
    }
]);

// conditional
if (user.level < 2) {
    model.value[2].items.unshift(
        { label: 'Setting', icon: 'pi pi-cog', to: '/setting' },
    )
}
if (user.level < 3) {
    // model.value[0].items.push(
    //     { label: 'Statistik', icon: 'pi pi-database', to: '/statistik' },
    // )
    model.value[1].items.unshift(
        { label: 'Data Wilayah', icon: 'pi pi-map', to: '/data-wilayah' },
    )
}
if (user.level > 1) {
    model.value[0].items.push(
        { label: 'Suara Masuk', icon: 'pi pi-envelope', to: '/suara-masuk' },
    )
}
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
