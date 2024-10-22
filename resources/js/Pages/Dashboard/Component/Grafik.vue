<script setup>
import { useLayout } from '@/Layouts/composables/layout';
import { ProductService } from '@/Services/ProductService';
import { onMounted, ref, watch } from 'vue';
import { Socket, io } from 'socket.io-client';
import Chart from 'primevue/chart';
import Fluid from 'primevue/fluid';
import Menu from 'primevue/menu';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const { getPrimary, getSurface, isDarkTheme } = useLayout()
const data = defineProps({
    paslon: Object,
    kec: Object,
    desa: Object,
    suara: Object,
    grafik: Object,
})

const dataGrafik = ref(new Array())
const barData = ref(null)
const barOptions = ref(null)
const clr = ['#eab308', '#8b5cf6', '#3b82f6', '#f97316', '#f59e0b', '#10b981', '#14b8a6', '#84cc16']

onMounted(() => {
    setColorOptions();
});

const socket = io('http://localhost:3000', {
    withCredentials: true,
})
const initData = () => {
    dataGrafik.value = []
    data.grafik.map((dg, d) => {
        dataGrafik.value.push({
            label: dg.name,
            backgroundColor: clr[d],
            borderColor: clr[d],
            data: dg.sah,
            uuid: dg.uuid,
        })
    })
}
initData()

function setColorOptions() {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary');
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border');
    const listKecamatan = data.kec.map((dk) => {
        return dk.kec_name
    })
    barData.value = {
        labels: listKecamatan,
        datasets: dataGrafik.value
    }
    barOptions.value = {
        plugins: {
            legend: {
                labels: {
                    fontColor: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    font: {
                        weight: 500
                    }
                },
                grid: {
                    display: false,
                    drawBorder: false
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder,
                    drawBorder: false
                }
            }
        }
    }
}

socket.on('get-paslon', (gp) => {
    // console.log('get', gp)
    let index = null
    data.kec.some((dk, k) => {
        if (dk.kec_id === gp.kec) {
            index = k
            return true
        }
    })

    dataGrafik.value.some((dg,d) => {
        if (dg.uuid === gp.uuid) {
            const sum = dg.data[index] + gp.vote
            dataGrafik.value[d].data[index] = sum
            return true
        }
    })
})

socket.on('update-paslon', (up) => {
    // console.log('update', up)
    let index = null
    data.kec.some((dk, k) => {
        if (dk.kec_id === up.kec) {
            index = k
            return true
        }
    })

    dataGrafik.value.some((dg,d) => {
        if (dg.uuid === up.uuid) {
            const sum = dg.data[index] + up.vote
            dataGrafik.value[d].data[index] = sum
            return true
        }
    })
})

const formatNumber = (value) => {
    return value.toLocaleString({ style: 'number' });
}

watch(
    [getPrimary, getSurface, isDarkTheme],
    () => {
        setColorOptions();
    },
    { immediate: true }
);
</script>

<template>
    <Head title="Dashboard" />

    <Fluid class="grid grid-cols-12 gap-8">
        <div class="col-span-12 xl:col-span-12">
            <div class="card">
                <div class="font-semibold text-xl mb-4">Grafik Per Kecamatan</div>
                <Chart type="bar" :data="barData" :options="barOptions" v-if="dataGrafik.length > 0"></Chart>
                <div class="w-full text-center" v-if="dataGrafik.length < 1">
                    <h1>Belum ada data</h1>
                </div>
            </div>
        </div>
    </Fluid>
</template>
