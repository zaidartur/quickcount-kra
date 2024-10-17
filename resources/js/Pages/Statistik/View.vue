<script setup>
import { defineProps, ref, onMounted  } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Socket, io } from 'socket.io-client';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import Paginator from 'primevue/paginator';
import ProgressBar from 'primevue/progressbar';
import Divider from 'primevue/divider';
import Chart from 'primevue/chart';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    paslon: Object,
    kec: Object,
    desa: Object,
})

const kecamatan = ref(new Array())
const desas = ref(new Array())
const paslons = ref(new Array())
const detailDialog = ref(false)
const headerDetail = ref(null)

const socket = io('http://localhost:3000', {
    withCredentials: true,
})

const initData = () => {
    kecamatan.value = []
    paslons.value = []

    if (datas.kec) {
        datas.kec.map((val, i) => {
            kecamatan.value.push(val)
        }) 
    }
    if (datas.paslon) {
        datas.paslon.map((ps) => {
            paslons.value.push(ps)
        })
    }
}
 
initData() 

const paginator = ref(0)
const filtered_data = ref(kecamatan.value.slice(paginator.value, (paginator.value + 6)))

// random color
// const custom_clr = ['emerald', 'green', 'lime', 'red', 'orange', 'amber', 'yellow', 'teal', 'cyan', 'purple']
// const randomIndex = (nm) => {return Math.floor(Math.random() * custom_clr.length * nm)}
// const randomValue = (nm) => {
//     return custom_clr[randomIndex(nm)]
// }

socket.on('connection', (socket) => {
    //
})

const filtered = (e) => {
    paginator.value = e.first
    filtered_data.value = kecamatan.value.slice(paginator.value, (paginator.value + 6))
}

const mouseOnCard = (e) => {
    let cardId = 'card_' + e.toString()
    const dialogElement = document.getElementById(cardId)
    if (!dialogElement.classList.contains('is-hover')) {
        dialogElement.classList.add('is-hover') 
    } 
}

const mouseOutCard = (e) => {
    const cardId = 'card_' + e.toString()
    const dialogElement = document.getElementById(cardId)
    dialogElement.classList.remove('is-hover') 
}

const _detail = (val) => {
    headerDetail.value = 'Detail Kecamatan ' + val.kec_name
    detailDialog.value = true
}

const test = (e) => {
    console.log(e)
}

// example data
onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});
const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
    const documentStyle = getComputedStyle(document.body);

    return {
        labels: ['A', 'B', 'C'],
        datasets: [
            {
                data: [540, 325, 702],
                backgroundColor: [documentStyle.getPropertyValue('--p-cyan-500'), documentStyle.getPropertyValue('--p-orange-500'), documentStyle.getPropertyValue('--p-gray-500')],
                hoverBackgroundColor: [documentStyle.getPropertyValue('--p-cyan-400'), documentStyle.getPropertyValue('--p-orange-400'), documentStyle.getPropertyValue('--p-gray-400')]
            }
        ]
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    usePointStyle: true,
                    color: textColor
                }
            }
        }
    };
};
</script>

<template>
    <Head title="Statistik" />

    <div>
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4 cursor-pointer" v-for="camat in filtered_data">
                <div class="card mb-0" :id="'card_'+camat.id" @mouseover="mouseOnCard(camat.id)" @mouseout="mouseOutCard(camat.id)" @click="_detail(camat)">
                <!-- <div class="card mb-0 col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4 cursor-pointer" v-for="camat in filtered_data"> -->
                    <div class="flex justify-between mb-4">
                        <div>
                            <!-- <span class="block text-muted-color font-medium mb-4">Orders</span> -->
                            <div class="text-surface-900 dark:text-surface-0 font-semibold text-2xl">
                                {{ camat.kec_name }}
                            </div>
                        </div>
                        <div class="flex items-center justify-center bg-primary-100 dark:bg-blue-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-envelope text-primary-500 !text-xl"></i>
                        </div>
                    </div>
                    <div class="mb-3" v-for="psl in paslons">
                        <span class="font-semibold">
                            {{ psl.nama_paslon }}
                        </span>
                        <ProgressBar :value="Math.floor(Math.random() * 100)" :id="`bar_${camat.kec_id}_${psl.uuid_paslon}`"></ProgressBar>
                    </div>
                    <div class="mb-3">
                        <span class="font-semibold">
                            Suara Tidak Sah
                        </span>
                        <ProgressBar :value="Math.floor(Math.random() * 100)" class="red"></ProgressBar>
                    </div>
                    <!-- <div class="mb-3">
                        <span class="font-semibold">24 new </span>
                        <ProgressBar :value="45" :class="randomValue(2)"></ProgressBar>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="w-full mt-5">
            <Paginator 
                v-model="kecamatan" 
                :rows="6" 
                :totalRecords="kecamatan.length" 
                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink" 
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Kecamatan" 
                @page="filtered($event)"
            />
        </div>

        <Dialog v-model:visible="detailDialog" maximizable modal :header="headerDetail" :style="{ width: '75rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div class="flex flex-col md:flex-row md:w-12/12">
                <div class="flex flex-col md:w-5/12">
                    <div class="card mb-0">
                        <div class="flex justify-between mb-4">
                            <div>
                                <div class="text-surface-900 dark:text-surface-0 font-semibold text-2xl">
                                    Lorem
                                </div>
                            </div>
                            <!-- <div class="flex items-center justify-center bg-primary-100 dark:bg-blue-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                                <i class="pi pi-envelope text-primary-500 !text-xl"></i>
                            </div> -->
                        </div>
                        <div class="mb-3 w-full" v-for="pas in paslons">
                            <span class="font-semibold">
                                {{ pas.nama_paslon }}
                            </span>
                            <ProgressBar :value="Math.floor(Math.random() * 100)" class="w-full"></ProgressBar>
                        </div>
                        <div class="mb-3">
                            <span class="font-semibold">
                                Suara Tidak Sah
                            </span>
                            <ProgressBar :value="Math.floor(Math.random() * 100)" class="red w-full"></ProgressBar>
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" class="!hidden md:!flex"></Divider>
                <div class="flex flex-col md:w-7/12 w-full items-center justify-center text-center">
                    <Chart type="pie" :data="chartData" :options="chartOptions" class="w-full md:w-[30rem]" />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<style lang="scss">
    .is-hover {
        box-shadow: 0 0 10px rgba(180, 180, 180, 0.5);
    }

    .emerald .p-progressbar-value {
        // background-color: #d4d4d4;
        background: #10b981;
    }
    .green .p-progressbar-value {
        background: #22c55e;
    }
    .lime .p-progressbar-value {
        background: #84cc16;
    }
    .red .p-progressbar-value {
        background: #ef4444;
    }
    .orange .p-progressbar-value {
        background: #f97316;
    }
    .amber .p-progressbar-value {
        background: #f59e0b;
    }
    .yellow .p-progressbar-value {
        background: #eab308;
    }
    .teal .p-progressbar-value {
        background: #14b8a6;
    }
    .cyan .p-progressbar-value {
        background: #06b6d4;
    }
    .purple .p-progressbar-value {
        background: #0ea5e9;
    }
</style>