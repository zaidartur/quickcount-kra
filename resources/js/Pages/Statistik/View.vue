<script setup>
import { defineProps, ref, onMounted  } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Socket, io } from 'socket.io-client';
import { useToast } from 'primevue/usetoast';
import Paginator from 'primevue/paginator';
import ProgressBar from 'primevue/progressbar';
import Divider from 'primevue/divider';
import Chart from 'primevue/chart';
import Toolbar from 'primevue/toolbar';
import MeterGroup from 'primevue/metergroup';
import Card from 'primevue/card';

onMounted(() => {
    // chartData.value = setChartData();
    // chartOptions.value = setChartOptions();
    window.addEventListener('resize', onResize)
})

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    paslon: Object,
    kec: Object,
    desa: Object,
    statKec: Object,
})

const chartData = ref()
const chartOptions = ref()
const kecamatan = ref(new Array())
const desas = ref(new Array())
const paslons = ref(new Array())
const meterSuara = ref(new Array())
const totalSeluruhSuara = ref(0)
const suaraSah = ref(0)
const suaraTidakSah = ref(0)
const dataDetail = ref(new Array())
const detailDialog = ref(false)
const headerDetail = ref(null)
const dataProgress = ref(new Array())

const socket = io('http://localhost:3000', {
    withCredentials: true,
})
// const socket = io('https://qcws.caturnus.com', {
//     withCredentials: true,
// })

const initData = () => {
    kecamatan.value = []
    paslons.value = []
    dataProgress.value = []
    totalSeluruhSuara.value = 0
    suaraSah.value = 0
    suaraTidakSah.value = 0

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
    if (datas.statKec) {
        datas.statKec.map((sk) => {
            totalSeluruhSuara.value = totalSeluruhSuara.value + sk.total
            suaraTidakSah.value = suaraTidakSah.value + sk.invalid
            dataProgress.value.push(sk)
        })
    }
    suaraSah.value = totalSeluruhSuara.value - suaraTidakSah.value
    meterSuara.value = [
        { label: 'Suara Sah', color: '#34d399', value: suaraSah, icon: 'pi pi-check' },
        { label: 'Suara Tidak Sah', color: '#ef4444', value: suaraTidakSah, icon: 'pi pi-times' },
    ]
}
 
initData() 
// console.log('progress', dataProgress.value)
const paginator = ref(0)
const index = ref(0)
const sideBar = 280
const wdth = ref(window.innerWidth - sideBar)
const filtered_data = ref(dataProgress.value.slice(paginator.value, (paginator.value + 8)))
const clr = ['#eab308', '#8b5cf6', '#3b82f6', '#f97316', '#f59e0b', '#10b981', '#14b8a6', '#84cc16']
const clrLabel = ['yellow', 'violet', 'blue', 'orange', 'amber', 'emerald', 'teal', 'lime']
// random color
// const custom_clr = ['emerald', 'green', 'lime', 'red', 'orange', 'amber', 'yellow', 'teal', 'cyan', 'purple']
// const randomIndex = (nm) => {return Math.floor(Math.random() * custom_clr.length * nm)}
// const randomValue = (nm) => {
//     return custom_clr[randomIndex(nm)]
// }

socket.on('get-paslon', (ps) => {
    dataProgress.value.some((fd, f) => {
        if (fd.kec_id === ps.kec) {
            if (ps.uuid === 'invalid') {
                fd.invalid = fd.invalid + ps.vote
                suaraTidakSah.value = suaraTidakSah.value + ps.vote
            } else {
                fd.paslons.some((fp, i) => {
                    if (fp.uuid === ps.uuid) {
                        fd.paslons[i].voting = fp.voting + ps.vote
                        suaraSah.value = suaraSah.value + ps.vote
                        return true
                    }
                })
            }

            // update total suara
            const total = parseInt(fd.total) + ps.vote
            dataProgress.value[f].total = total
            totalSeluruhSuara.value = totalSeluruhSuara.value + ps.vote
            return true
        }
    })

    // update on graph
    if (dataDetail.value.kec_id === ps.kec) {
        let number = null
        chartData.value.uuid.some((id, i) => {
            if (id === ps.uuid) {
                number = i
                return true
            }
        })
        if (number && number >= 0) {
            chartData.value.datasets[0].data[number] = parseInt(chartData.value.datasets[0].data[number]) + ps.vote
        }

        // update data desa
        _detailKecamatan(ps.kec)
    }
})

socket.on('update-paslon', (gp) => {
    dataProgress.value.some((fd, f) => {
        if (fd.kec_id === gp.kec) {
            if (gp.uuid === 'invalid') {
                fd.invalid = fd.invalid + gp.vote
                suaraTidakSah.value = suaraTidakSah.value + gp.vote
            } else {
                fd.paslons.some((fp, i) => {
                    if (fp.uuid === gp.uuid) {
                        fd.paslons[i].voting = fp.voting + gp.vote
                        suaraSah.value = suaraSah.value + gp.vote
                        return true
                    }
                })
            }

            // update total suara
            const total = parseInt(fd.total) + gp.vote
            dataProgress.value[f].total = total
            const totalSeluruh = totalSeluruhSuara.value + gp.vote
            totalSeluruhSuara.value = totalSeluruh
            return true
        }
    })

    // update on graph
    if (dataDetail.value.kec_id === gp.kec) {
        let number = null
        chartData.value.uuid.some((id, i) => {
            if (id === gp.uuid) {
                number = i
                return true
            }
        })
        if (number && number >= 0) {
            chartData.value.datasets[0].data[number] = parseInt(chartData.value.datasets[0].data[number]) + gp.vote
        }
    }

    // update data on desa
    if (desas.value && desas.value.length > 0) {
        desas.value.some((ds, d) => {
            if (ds.full_id === gp.desa && ds.kec_id === gp.kec) {
                if (gp.uuid === 'invalid') {
                    ds.invalid = parseInt(ds.invalid) + gp.vote
                } else {
                    ds.valid.some((vl, v) => {
                        if (vl.uuid === gp.uuid) {
                            ds.valid[v].point = parseInt(vl.point) + gp.vote
                        }
                    })
                }
                const total = parseInt(ds.total) + gp.vote
                desas.value[d].total = total
                return true
            }
        })
    }
})

const onResize = () => {
    wdth.value = window.innerWidth
    // wdth.value = (window.innerWidth - sideBar)
}

const filtered = (e) => {
    paginator.value = e.first
    filtered_data.value = dataProgress.value.slice(paginator.value, (paginator.value + 8))
}

function formatNumber(value) {
    // const num = parseInt(value)
    // if (num > 0 && num !== undefined) {
    //     num.toLocaleString({style: 'number'})
    // } else {
    //     return 0
    // }
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
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
    dataDetail.value = val
    // chartData.value = setChartData(dataDetail.value)
    setChartData()
    chartOptions.value = setChartOptions()
    if (val.total > 0) {
        _detailKecamatan(val.kec_id)
    } else {
        desas.value = []
    }

    detailDialog.value = true
}

const _detailKecamatan = async(_kec) => {
    const data = {
        kecamatan: _kec
    }
    await axios.post('/statistik/kecamatan', data).then((res) => {
        desas.value = []
        if (res.data.length > 0) {
            res.data.map((ds) => {
                desas.value.push(ds)
            })
        }
    })
}

const _desaMeter = (data, inv) => {
    let meter = []
    if (data.length > 0) {
        data.map((d, i) => {
            meter.push({
                label: d.name,
                color: clr[i], 
                value: d.point, 
                icon: 'pi pi-check',
                uid: d.uuid,
                tooltip: d.name + ' (' + d.point + ' suara)',
            })
        })
        meter.push({
            label: 'Suara Tidak Sah', 
            color: '#ef4444', 
            value: inv, 
            icon: 'pi pi-times',
            uid: 'invalid',
            tooltip: 'Suara Tidak Sah (' + inv + ')',
        })
    }
    return meter
}

const test = (e) => {
    console.log(e)
}

// data charts
const setChartData = () => {
    // const documentStyle = getComputedStyle(document.body);
    let color = clr.slice(0, dataDetail.value.paslons.length)
    let label = dataDetail.value.paslons.map((pp) => {
        return pp.nama
    })
    let datas = dataDetail.value.paslons.map((dt) => {
        return (dt.voting ?? 0)
    })
    let uid = dataDetail.value.paslons.map((id) => {
        return id.uuid
    })
    label.push('Suara Tidak Sah')
    datas.push(dataDetail.value.invalid)
    color.push('#ef4444')
    uid.push('invalid')

    chartData.value = {
        labels: label,
        datasets: [
            {
                data: datas,
                backgroundColor: color,
                hoverBackgroundColor: color,
            }
        ],
        uuid: uid
    }
}

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
    }
}
</script>

<template>
    <Head title="Grafik Statistik" />

    <div>
        <Toolbar class="mb-6">
            <template #start>
                <h5 class="m-3">
                    Total Suara Masuk : <span class="text-3xl">{{ formatNumber(totalSeluruhSuara) }}</span> 
                    <!-- {{ wdth }} -->
                </h5>
            </template>

            <template #end>
                <div >
                    <MeterGroup :value="meterSuara" :max="totalSeluruhSuara" class="w-full" v-tooltip.bottom="'Suara Sah : '+formatNumber(suaraSah)+' \n Tidak Sah : '+formatNumber(suaraTidakSah)" />
                </div>
            </template>
        </Toolbar>
        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 md:col-span-6 lg:col-span-4 cursor-pointer" :class="wdth >= 1920 ? 'xl:col-span-2' : 'xl:col-span-3'" v-for="(camat, c) in dataProgress">
                <div class="card mb-0" :id="'card_'+camat.id" @mouseover="mouseOnCard(camat.id)" @mouseout="mouseOutCard(camat.id)" @click="_detail(camat)">
                <!-- <div class="card mb-0 col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-4 cursor-pointer" v-for="camat in filtered_data"> -->
                    <div class="flex justify-between mb-4">
                        <div>
                            <span class="block text-muted-color font-medium mb-4">Suara masuk {{ formatNumber(camat.total) }}</span>
                            <div class="text-surface-900 dark:text-surface-0 font-semibold text-2xl">
                                {{ camat.kec_name }}
                            </div>
                        </div>
                        <div class="flex items-center justify-center bg-primary-100 dark:bg-blue-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                            <i class="pi pi-envelope text-primary-500 !text-xl"></i>
                        </div>
                    </div>
                    <div class="mb-3" v-for="(psl, p) in camat.paslons">
                        <span class="font-semibold">
                            {{ psl.nama }}
                        </span>
                        <ProgressBar :value="psl.voting > 0 ? parseFloat((psl.voting/camat.total)*100).toFixed(1) : 0" :max="100" v-tooltip.bottom="(psl.voting > 0 ? parseFloat((psl.voting/camat.total)*100).toFixed(1) : 0) + '%'" :id="`bar_${camat.kec_id}_${psl.uuid}`" :class="clrLabel[p]"></ProgressBar>
                    </div>
                    <div class="mb-3">
                        <span class="font-semibold">
                            Suara Tidak Sah
                        </span>
                        <ProgressBar :value="camat.invalid > 0 ? (parseFloat(camat.invalid/camat.total)*100).toFixed(1) : 0" v-tooltip.bottom="(camat.invalid > 0 ? (parseFloat(camat.invalid/camat.total)*100).toFixed(1) : 0) + '%'" class="red"></ProgressBar>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="w-full mt-5">
            <Paginator 
                v-model="kecamatan" 
                :rows="8" 
                :totalRecords="kecamatan.length" 
                template="FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink" 
                currentPageReportTemplate="Menampilkan data ke {first} sampai {last} dari {totalRecords} Kecamatan" 
                @page="filtered($event)"
            />
        </div> -->

        <Dialog v-model:visible="detailDialog" maximizable modal :header="headerDetail" :style="{ width: '75rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div class="flex flex-col md:flex-row md:w-12/12 mb-5">
                <div class="flex flex-col md:w-6/12">
                    <div class="card mb-0">
                        <div class="flex justify-between mb-4">
                            <div>
                                <div class="text-surface-900 dark:text-surface-0 font-semibold text-2xl">
                                    Total suara masuk : <span class="text-3xl">{{ formatNumber(dataDetail.total) ?? 0 }}</span>
                                </div>
                            </div>
                            <!-- <div class="flex items-center justify-center bg-primary-100 dark:bg-blue-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                                <i class="pi pi-envelope text-primary-500 !text-xl"></i>
                            </div> -->
                        </div>
                        <div class="mb-3 w-full" v-for="(psl, s) in dataDetail.paslons">
                            <span class="font-semibold">
                                {{ psl.nama }} ({{ formatNumber(psl.voting) ?? 0 }} suara)
                            </span>
                            <ProgressBar :value="parseFloat((psl.voting/dataDetail.total)*100).toFixed(1)" v-tooltip.bottom="(psl.voting > 0 ? parseFloat((psl.voting/dataDetail.total)*100).toFixed(1) : 0) + '%'" :id="`bar_${dataDetail.kec_id}_${psl.uuid}`" :class="clrLabel[s]"></ProgressBar>
                        </div>
                        <div class="mb-3">
                            <span class="font-semibold">
                                Suara Tidak Sah ({{ formatNumber(dataDetail.invalid) ?? 0 }} suara)
                            </span>
                            <ProgressBar :value="(parseFloat(dataDetail.invalid/dataDetail.total)*100).toFixed(1)" v-tooltip.bottom="(dataDetail.invalid > 0 ? (parseFloat(dataDetail.invalid/dataDetail.total)*100).toFixed(1) : 0) + '%'" class="red"></ProgressBar>
                        </div>
                    </div>
                </div>
                <Divider layout="vertical" class="!hidden md:!flex"></Divider>
                <div class="flex flex-col md:w-6/12 w-full items-center justify-center text-center align-center">
                    <Chart type="pie" :data="chartData" :options="chartOptions" class="w-full text-center md:w-[25em] md:h-[25em]" v-if="dataDetail.total > 0" />
                    <h5 v-if="dataDetail.total === 0">Belum ada data yang masuk</h5>
                </div>
            </div>
            <Divider layout="horizontal" align="center" class="!hidden md:!flex">
                <b>Data Suara Tiap Desa/Kelurahan</b>
            </Divider>
            <div class="grid grid-cols-12 gap-8" v-if="desas.length > 0">
                <div class="col-span-12 md:col-span-6 lg:col-span-3 xl:col-span-3 rounded-xl" :id="'card_'+ds.full_id" @mouseover="mouseOnCard(ds.full_id)" @mouseout="mouseOutCard(ds.full_id)" v-for="ds in desas">
                    <Card class="h-full border border-primary-300">
                        <template #title>{{ ds.name }}</template>
                        <template #subtitle>{{ formatNumber(ds.total) }} Suara</template>
                        <template #content>
                            <!-- <span class="font-bold text-xl">{{ ds.name }}</span> ({{ ds.total }} Suara) -->
                            <Divider layout="horizontal" class="!hidden md:!flex"></Divider>
                            <MeterGroup :value="_desaMeter(ds.valid, ds.invalid)" :max="ds.total" :aria-labelledby="false" class="w-full" v-if="ds.total > 0" />
                            <h5 class="text-center" v-if="ds.total === 0">Belum ada data yang masuk</h5>
                        </template>
                    </Card>
                </div>
            </div>
            <div class="grid grid-cols-12 gap-8" v-if="desas.length === 0">
                <div class="col-span-12 text-center mt-20 mb-5">
                    <i class="pi pi-info-circle !text-3xl" />
                    <h3 class="">Belum ada data yang masuk</h3>
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
    .violet .p-progressbar-value {
        background: #8b5cf6;
    }
    .blue .p-progressbar-value {
        background: #3b82f6;
    }
</style>