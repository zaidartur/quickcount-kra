<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

import AppMenuItem from './AppMenuItem.vue';

const user = usePage().props.auth.user;
const roles = (lvl) => {
    const _lvl = parseInt(lvl)
    if (_lvl === 0) {
        return 'SUPER ADMIN'
    } else if (_lvl === 1) {
        return 'ADMIN KABUPATEN'
    } else if (_lvl === 2) {
        return 'ADMIN KECAMATAN'
    } else if (_lvl === 3) {
        return 'ADMIN DESA'
    } else if (_lvl === 4) {
        return 'ADMIN TPS'
    } else {
        return ''
    }
}
const model = ref([
    {
        label: 'MAIN MENU',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/dashboard' },
            // { 
            //     label: 'Statistik', 
            //     icon: 'pi pi-database',
            //     items: [
            //         {
            //             label: 'Grafik',
            //             icon: 'pi pi-chart',
            //             to: '/statistik/grafik'
            //         },
            //         {
            //             label: 'Tabel',
            //             icon: 'pi pi-chart',
            //             to: '/statistik/tabel'
            //         },
            //     ]
            // },
            { label: 'Statistik', icon: 'pi pi-chart-line', to: '/statistik' },
            { label: 'Tabel', icon: 'pi pi-database', to: '/tabel-statistik' },
        ]
    },
    {
        label: 'MASTER DATA',
        items: []
    },
    {
        label: 'SETTING',
        items: [
            { label: 'Data User', icon: 'pi pi-users', to: '/data-user' },
            // { label: user.name, icon: 'pi pi-user', to: '/profile' }
        ]
    },
    {
        label: roles(user.level),
        items: [
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
if (user.level < 2) {
    // model.value[0].items.push(
    //     { label: 'Statistik', icon: 'pi pi-database', to: '/statistik' },
    // )
    model.value[1].items.unshift(
        { label: 'DPT', icon: 'pi pi-users', to: '/daftar-pemilih-tetap' },
    )
    model.value[1].items.unshift(
        { label: 'Data Wilayah', icon: 'pi pi-map', to: '/data-wilayah' },
    )
}
if (user.level > 1) {
    model.value[1].items.push(
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
