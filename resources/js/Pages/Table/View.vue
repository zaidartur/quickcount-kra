<script setup>
import { defineProps, ref, onMounted  } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import Tag from 'primevue/tag';

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
console.log(dataProgress.value, 'OK')
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

function formatNumber(value) {
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
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

const detailData = async(prop) => {
    console.log('detail', prop)
}

</script>

<template>
    <Head title="Tabel Statistik" />

    <div>
        <div class="card">
            <Toolbar class="mb-6" v-if="auth.level < 2">
                <template #end>
                    <!-- <Button label="Import" icon="pi pi-download" severity="success" class="mr-2" @click="openNew" outlined /> -->
                    <Button label="Export" icon="pi pi-upload" severity="primary" @click="exportCSV($event)" :disabled="desa.length > 0 ? false : true" outlined />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:expandedRows="expandedRows"
                :value="dataProgress"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Kecamatan"
            >
                <template #header>
                    <div class="flex flex-wrap justify-end gap-2">
                        <Button text icon="pi pi-plus" label="Buka Semua" @click="expandAll" />
                        <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapseAll" />
                    </div>
                </template>

                <Column expander style="width: 5rem" />
                <Column field="" header="Kecamatan" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        <b>{{ slotProps.data.kec_name }}</b>
                    </template>
                </Column>
                <Column field="" header="Jumlah Suara Masuk" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.total)" :severity="'info'" />
                    </template>
                </Column>
                <Column field="" header="Suara Sah" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.total - parseInt(slotProps.data.invalid))" :severity="'success'" />
                    </template>
                </Column>
                <Column field="" header="Suara Tidak Sah" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        <Tag :value="formatNumber(slotProps.data.invalid)" :severity="'danger'" />
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
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
                            <Column field="voting" header="Jumlah Suara"></Column>
                        </DataTable>
                    </div>
                </template>
            </DataTable>
        </div>
       
    </div>
</template>

<style lang="scss">

</style>