<script setup>
import { useLayout } from '@/Layouts/composables/layout';
import { ProductService } from '@/Services/ProductService';
import { onMounted, ref, watch } from 'vue';
import Chart from 'primevue/chart';
import Menu from 'primevue/menu';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const { getPrimary, getSurface, isDarkTheme } = useLayout()
const data = defineProps({
    paslon: Object,
    kec: Object,
    desa: Object,
    suara: Object,
})

const barData = ref(null)
const barOptions = ref(null)

onMounted(() => {
    setColorOptions();
});

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
        datasets: [
            {
                label: 'My First dataset',
                backgroundColor: documentStyle.getPropertyValue('--p-primary-500'),
                borderColor: documentStyle.getPropertyValue('--p-primary-500'),
                data: [65, 59, 80, 81, 56, 55, 40, 45, 29, 78, 45, 72, 45, 29, 78, 45, 72]
            },
            {
                label: 'My Second dataset',
                backgroundColor: documentStyle.getPropertyValue('--p-primary-200'),
                borderColor: documentStyle.getPropertyValue('--p-primary-200'),
                data: [28, 48, 40, 19, 86, 27, 90, 37, 61, 54, 34, 29, 86, 27, 90, 37, 61]
            }
        ]
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

const formatCurrency = (value) => {
    return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
};

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

    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 xl:col-span-12">
            <div class="card">
                <div class="font-semibold text-xl mb-4">Grafik Per Kecamatan</div>
                <Chart type="bar" :data="barData" :options="barOptions"></Chart>
            </div>
        </div>
    </div>
</template>
