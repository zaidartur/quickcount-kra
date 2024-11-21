<script setup>
import { defineProps, ref, nextTick   } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import Tag from 'primevue/tag';
import Popover from 'primevue/popover';
import Divider from 'primevue/divider';

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

const dt = ref()
const op = ref()
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
const expandedRows = ref([])
const detailTerpilih = ref([])
const detailDesa = ref([])
const rawDesa = ref([])
const desaSelected = ref()
const headerTitle = ref('')
const expandedTPS = ref([])
const desaSelectedRaw = ref()

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

const paginator = ref(0)
const index = ref(0)
const sideBar = 280
const wdth = ref(window.innerWidth - sideBar)
const filtered_data = ref(dataProgress.value.slice(paginator.value, (paginator.value + 8)))
const clr = ['#eab308', '#8b5cf6', '#3b82f6', '#f97316', '#f59e0b', '#10b981', '#14b8a6', '#84cc16']
const clrLabel = ['yellow', 'violet', 'blue', 'orange', 'amber', 'emerald', 'teal', 'lime']

function expandAll() {
    expandedRows.value = dataProgress.value.reduce((acc, p) => (acc[p.id] = true) && acc, {});
}

function collapseAll() {
    expandedRows.value = null;
}

function expandDialog() {
    detailTerpilih.value = detailDesa.value.reduce((acc, p) => (acc[p.id] = true) && acc, {});
}

function collapseDialog() {
    detailTerpilih.value = null;
}

function expandPop() {
    expandedTPS.value = desaSelected.value.reduce((acc, p) => (acc[p.id] = true) && acc, {});
}

function collapsePop() {
    expandedTPS.value = null;
}

function formatNumber(value) {
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
}

const notps = (data) => {
    if (data < 10) {
        return `00${data}`
    } else if (data > 9 && data < 100) {
        return `0${data}`
    } else {
        return data
    }
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

const export_data = async() => {
    // const form = useForm({})
    // form.post('/tabel-statistik/export/data')
    window.open('/tabel-statistik/export/data', '_blank')
}

const detailData = async(prop) => {
    // console.log('detail', prop)
    headerTitle.value = 'Detail Kecamatan ' + prop.kec_name
    rawDesa.value = prop
    detailDesa.value = prop.desas
    detailDialog.value = true
}

const detail_tps = (event, datas) => {
    op.value.hide();
    if (desaSelectedRaw.value?.id === datas.id) {
        desaSelectedRaw.value = null;
    } else {
        desaSelected.value = datas.tps
        desaSelectedRaw.value = datas
        nextTick(() => {
            op.value.show(event);
        });
    }
    // desaSelectedRaw.value = datas
    // desaSelected.value = datas.tps
    // op.value.show(event)
    // console.log(datas.id, desaSelectedRaw.value)
}

</script>

<template>
    <Head title="Tabel Statistik" />

    <div>
        <div class="card">
            <Toolbar class="mb-6" v-if="auth.level < 2">
                <template #start>
                    <h5><i class="pi pi-database"></i> Tabel Data Suara Masuk Tiap Kecamatan</h5>
                </template>
                <template #end>
                    <!-- <Button label="Import" icon="pi pi-download" severity="success" class="mr-2" @click="openNew" outlined /> -->
                    <Button label="Export ke Excel" icon="pi pi-upload" severity="primary" @click="export_data()" :disabled="desa.length > 0 ? false : true" outlined />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:expandedRows="expandedRows"
                :value="dataProgress"
                dataKey="id"
                :paginator="true"
                :rows="20"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 20, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Kecamatan"
                scrollable 
                scrollHeight="500px"
            >
                <template #header>
                    <div class="flex flex-wrap justify-end gap-2">
                        <Button text icon="pi pi-plus" label="Buka Semua" @click="expandAll" />
                        <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapseAll" />
                    </div>
                </template>

                <Column expander style="width: 5rem" frozen header="Paslon" />
                <Column field="" header="Kecamatan" frozen style="min-width: 12rem">
                    <template #body="slotProps">
                        <b>{{ slotProps.data.kec_name }}</b>
                    </template>
                </Column>
                <Column field="" header="Jumlah DPT" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.dpt)" :severity="'primary'" />
                    </template>
                </Column>
                <Column field="" header="Suara Masuk" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.total)" :severity="'info'" />
                    </template>
                </Column>
                <Column field="" header="Suara Sah" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.total - parseInt(slotProps.data.invalid))" :severity="'success'" />
                    </template>
                </Column>
                <Column field="" header="Suara Tidak Sah" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.invalid)" :severity="'danger'" />
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem" header="Info">
                    <template #body="slotProps">
                        <Button icon="pi pi-info-circle" outlined rounded class="mr-2" v-tooltip.bottom="`Lihat Detail ${slotProps.data.kec_name}`" @click="detailData(slotProps.data)" />
                    </template>
                </Column>
                <template #expansion="slotProps">
                    <div class="p-4 md:w-8/12">
                        <h6><u>Detail di Kecamatan {{ slotProps.data.kec_name }}</u></h6>
                        <DataTable :value="slotProps.data.paslons" class="">
                            <Column field="" header="Urut Paslon">
                                <template #body="slotProps">
                                    {{ slotProps.data.urut }} &middot; {{ slotProps.data.nama }}
                                </template>
                            </Column>
                            <Column field="voting" header="Jumlah Suara">
                                <template #body="slotProps">
                                    {{ formatNumber(slotProps.data.voting) }}
                                </template>
                            </Column>
                            <Column field="voting" header="Persentase">
                                <template #body="slotProp">
                                    {{ slotProp.data.voting > 0 ? ((slotProp.data.voting / slotProps.data.total) * 100).toFixed(1) : 0 }}%
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </template>
            </DataTable>
        </div>
       
        <Dialog v-model:visible="detailDialog" :style="{ width: '850px' }" :header="headerTitle" :modal="true" :closable="true" >
            <div>
                <h6 class="text-center">Jumlah DPT : {{ formatNumber(rawDesa.dpt) }} || Jumlah TPS : {{ formatNumber(rawDesa.tps_total) }}</h6>
                <DataTable v-model:expandedRows="detailTerpilih" :value="detailDesa" ref="dt" dataKey="id" scrollable scrollHeight="350px" tableStyle="max-width: 850px">
                    <template #header>
                        <div class="flex flex-wrap justify-start gap-2">
                            <Button text icon="pi pi-plus" label="Buka Semua" @click="expandDialog" />
                            <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapseDialog" />
                        </div>
                    </template>
                    <Column expander style="width: 5rem" header="Paslon" />
                    <Column field="desakel_name" header="Nama Desa/Kel.">
                        <template #body="slotProps">
                            <Button text :label="slotProps.data.desakel_name" @click="detail_tps($event, slotProps.data)" v-tooltip.right="'Klik untuk melihat data TPS di ' + slotProps.data.desakel_name" />
                        </template>
                    </Column>
                    <Column field="" header="Suara Sah">
                        <template #body="slotProps">
                            {{ formatNumber(slotProps.data.valid) }}
                        </template>
                    </Column>
                    <Column field="" header="Suara Tidak Sah">
                        <template #body="slotProps">
                            {{ formatNumber(parseInt(slotProps.data.invalid)) }}
                        </template>
                    </Column>
                    <Column field="" header="Jumlah Suara">
                        <template #body="slotProps">
                            <Tag 
                                :value="formatNumber(slotProps.data.total) + ' Suara (' + ((slotProps.data.total / slotProps.data.dpt) * 100).toFixed(0) + '%)'" 
                                :severity="((slotProps.data.total / slotProps.data.dpt) * 100) === 100 ? 'success' : 'warn'" 
                            />
                            <!-- {{ formatNumber(slotProps.data.total) }} -->
                        </template>
                    </Column>
                    <Column field="" header="Jumlah DPT">
                        <template #body="slotProps">
                            {{ formatNumber(slotProps.data.dpt) }}
                        </template>
                    </Column>
                    <Column field="" header="Jumlah TPS">
                        <template #body="slotProps">
                            {{ formatNumber(slotProps.data.tps_total) }}
                        </template>
                    </Column>
                    <template #expansion="slotProps">
                        <div class="p-4 md:w-8/12">
                            <h6><u>Detail Desa/Kel.  {{ notps(slotProps.data.desakel_name) }}</u></h6>
                            <DataTable :value="slotProps.data.paslons">
                                <Column field="nama" header="Urut Paslon"></Column>
                                <Column field="voting" header="Jumlah Suara">
                                    <template #body="slotProps">
                                        {{ formatNumber(slotProps.data.voting) }}
                                    </template>
                                </Column>
                            </DataTable>
                        </div>
                    </template>
                </DataTable>
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" text @click="detailDialog = false" :disabled="submitted" />
            </template>
        </Dialog>

        <Popover ref="op">
            <div v-if="desaSelected" class="rounded flex flex-col">
                <div>
                    <h6 class="text-center">Data TPS di Desa/Kel. {{ desaSelectedRaw.desakel_name }} | Jumlah DPT : {{ formatNumber(desaSelectedRaw.dpt) }}</h6>
                    <!-- <h6 class="text-center">Jumlah DPT : {{ formatNumber(desaSelectedRaw.dpt) }}</h6> -->
                     <Divider />
                    <DataTable 
                        v-model:expandedRows="expandedTPS" 
                        :value="desaSelected" 
                        ref="dt" 
                        dataKey="id" 
                        :rows="5" 
                        :paginator="true"
                    >
                        <!-- <template #header>
                            <div class="flex flex-wrap justify-start gap-2">
                                <Button text icon="pi pi-plus" label="Buka Semua" @click="expandPop" />
                                <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapsePop" />
                            </div>
                        </template>
                        <Column expander style="width: 5rem" header="Paslon" /> -->
                        <Column field="desakel_name" header="Nomor TPS">
                            <template #body="slotProps">
                                {{ notps(slotProps.data.no_tps) }}
                            </template>
                        </Column>
                        <Column field="" header="Suara Sah">
                            <template #body="slotProps">
                                {{ formatNumber(slotProps.data.valid) }}
                            </template>
                        </Column>
                        <Column field="" header="Suara Tidak Sah">
                            <template #body="slotProps">
                                {{ formatNumber(parseInt(slotProps.data.invalid)) }}
                            </template>
                        </Column>
                        <Column field="" header="Jumlah Suara">
                            <template #body="slotProps">
                                <Tag 
                                    :value="formatNumber(slotProps.data.total) + ' Suara (' + ((slotProps.data.total / slotProps.data.dpt) * 100).toFixed(0) + '%)'" 
                                    :severity="((slotProps.data.total / slotProps.data.dpt) * 100) === 100 ? 'success' : 'warn'" 
                                />
                                <!-- {{ formatNumber(slotProps.data.total) }} -->
                            </template>
                        </Column>
                        <Column field="" header="Jumlah DPT">
                            <template #body="slotProps">
                                {{ formatNumber(slotProps.data.dpt) }}
                            </template>
                        </Column>
                        <!-- <template #expansion="slotProps">
                            <div class="p-4 md:w-8/12">
                                <h6><u>Detail TPS  {{ notps(slotProps.data.no_tps) }}</u></h6>
                                <DataTable :value="slotProps.data.paslons">
                                    <Column field="nama" header="Urut Paslon"></Column>
                                    <Column field="voting" header="Jumlah Suara">
                                        <template #body="slotProps">
                                            {{ formatNumber(slotProps.data.voting) }}
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                        </template> -->
                    </DataTable>
                </div>
            </div>
        </Popover>
    </div>
</template>

<style lang="scss">

</style>