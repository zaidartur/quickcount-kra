<script setup>
import { ProductService } from '@/Services/ProductService';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, defineProps } from 'vue';
import Toolbar from 'primevue/toolbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputNumber from 'primevue/inputnumber';
import FileUpload from 'primevue/fileupload';
import Popover from 'primevue/popover';
import ProgressBar from 'primevue/progressbar';
import { useForm, usePage } from '@inertiajs/vue3';
import ProgressSpinner from 'primevue/progressspinner';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    dpt: Object,
    kec: Object,
    desa: Object
})

const pemilih = ref(new Array())
const kecamatan = ref(new Array())
const desa = ref(new Array())
const selectDesa = ref(new Array())

const initData = () => {
    if (datas.dpt.length > 0) {
        pemilih.value = []
        datas.dpt.map((val, i) => {
            pemilih.value.push(val);
        }) 
    } else {
        pemilih.value = []
    }
}

const initWilayah = () => {
    if (datas.kec.length > 0) {
        kecamatan.value = []
        datas.kec.map((val, i) => {
            kecamatan.value.push({
                label: val.kec_name,
                value: val.kec_id
            })
        }) 
    } else {
        kecamatan.value = []
    }

    if (datas.desa.length > 0) {
        desa.value = []
        datas.desa.map((v) => {
            desa.value.push(v)
        }) 
    } else {
        desa.value = []
    }
}

initData()
initWilayah()
const dt = ref();
const headerTitle = ref('Tambah DPT')
const kecamatanSelected = ref()
const desaSelected = ref()
const errorKec = ref('')
const errorDesa = ref('')
const errorYear = ref('')
const errorTotal = ref('')
const errorFile = ref('')
const editData = ref()
const tahun = ref(0)
const totaldpt = ref(0)
const addDialog = ref(false)
const deleteProductDialog = ref(false)
const deleteProductsDialog = ref(false)
const importDialog = ref(false)
const duplicateDialog = ref(false)
const templateDialog = ref(false)
const duplicateDataImport = ref(new Array())
const selectedDuplicate = ref(new Array())
const dptTerpilih = ref(new Array())
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false)
const form = useForm({
    id: '', kec: '', desa: '', tahun: '', total: 0, type: 'new'
})
const formImport = useForm({
    import_file: null
})
const opn = ref(null)
    
function openNew() {
    headerTitle.value = 'Tambah DPT'
    form.type = 'new'
    clearError()
    selectDesa.value = []

    kecamatanSelected.value = []
    desaSelected.value = []
    tahun.value = ''
    totaldpt.value = 0

    addDialog.value = true
}

function hideDialog() {
    addDialog.value = false
}

const selectKec = () => {
    desaSelected.value = {}
    selectDesa.value = []
    
    desa.value.map((tmp) => {
        if (tmp.kec_id === kecamatanSelected.value.value) {            
            selectDesa.value.push({
                label: tmp.desakel_name,
                value: tmp.desakel_id
            })
        }
    })
}

const checkKecamatan = () => {
    if (kecamatanSelected.value) {
        errorKec.value = ''
        return true
    } else {
        errorKec.value = 'Kecamatan belum dipilih'
        return false
    }
}

const checkDesa = () => {
    if(desaSelected.value) {
        errorDesa.value = ''
        return true
    } else {
        errorDesa.value = 'Desa/Kelurahan belum dipilih'
        return false
    }
}

const checkTahun = () => {
    if(tahun.value) {
        if(tahun.value > 2000 && tahun.value < 3000) {
            errorYear.value = ''
            return true
        } else {
            errorYear.value = 'Format tahun tidak sesuai'
            return false
        }
    } else {
        errorYear.value = 'Tahun belum di input'
        return false
    }
}

const checkTotal = () => {
    if(totaldpt.value && totaldpt.value > 0) {
        errorTotal.value = ''
        return true
    } else {
        errorTotal.value = 'Total DPT belum di input'
        return false
    }
}

const clearError = () => {
    errorKec.value = ''
    errorDesa.value = ''
    errorYear.value = ''
    errorTotal.value = ''
}

const submit = () => {
    submitted.value = true
    if (checkKecamatan() && checkDesa() && checkTahun() && checkTotal()) {
        form.kec = kecamatanSelected.value.value
        form.desa = desaSelected.value.value
        form.tahun = tahun.value
        form.total = totaldpt.value

        form.post('/daftar-pemilih-tetap/simpan-dpt', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                addDialog.value = false
                submitted.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        checkKecamatan()
        checkDesa()
        checkTahun()
        checkTotal()
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon untuk mengisi semua inputan', life: 3000 });
    }
}

function editProduct(prod) {
    headerTitle.value = 'Edit DPT'
    form.type = 'edit'
    clearError()
    editData.value = { ...prod }

    getDesa(editData.value.kec_id)
    kecamatanSelected.value = {
        label: editData.value.kec_name,
        value: editData.value.kec_id
    }
    desaSelected.value = {
        label: editData.value.desakel_name,
        value: editData.value.desakel_id
    }
    form.id = editData.value.id
    tahun.value = editData.value.tahun_dpt
    totaldpt.value = editData.value.total

    addDialog.value = true
}

const getDesa = (kc) => {
    selectDesa.value = []
    desa.value.map((d) => {
        if (d.kec_id === kc)
        selectDesa.value.push({
            label: d.desakel_name,
            value: d.desakel_id,
        })
    })
}

function confirmDeleteProduct(prod) {
    editData.value = prod
    deleteProductDialog.value = true
    console.log(editData.value)
}

function deleteProduct() {
    submitted.value = true
    form.id = editData.value.id
    form.type = 'single'

    form.post('/daftar-pemilih-tetap/hapus-dpt', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            editData.value = {}
            deleteProductDialog.value = false
            submitted.value = false
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
            submitted.value = false
        }
    })
}

function confirmDeleteSelected() {
    deleteProductsDialog.value = true
    // console.log(dptTerpilih.value)
}

function deleteSelectedProducts() {
    submitted.value = true
    if (dptTerpilih.value.length > 0) {
        let uid = []
        dptTerpilih.value.map((del) => {
            uid.push(del.id)
        })

        form.id     = uid.toString()
        form.type   = 'multi'
        form.post('/daftar-pemilih-tetap/hapus-dpt', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                dptTerpilih.value = []
                deleteProductsDialog.value = false
                submitted.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        submitted.value = false
    }
}

const importDlg = () => {
    formImport.import_file = null
    importDialog.value = true
}

const saveImport = () => {
    submitted.value = true
    duplicateDataImport.value = []
    
    if (formImport.import_file) {
        errorFile.value = ''
        formImport.post('daftar-pemilih-tetap/import-dpt', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                formImport.import_file = null
                importDialog.value = false
                submitted.value = false
                
                if (messages.status === 'success' && messages.data.length > 0) {
                    messages.data.map((m) => {
                        duplicateDataImport.value.push(m)
                    })
                    duplicateDialog.value = true
                } else if (messages.status === 'template') {
                    templateDialog.value = true
                }
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'File tidak tersedia atau tidak sesuai dengan format dan ukuran', life: 3000 });
        submitted.value = false
    }
}

const downloadTemplate = () => {
    const dl = useForm({
        type: 'dpt'
    })
    // dl.post('/download-template')
    window.open('/download-template/dpt', '_blank')
}

const checkFile = () => {
    const filename = formImport.import_file?.name
    const filesize = formImport.import_file?.size
    const ext = filename.substring(filename.lastIndexOf('.')+1, filename.length) || filename
    
    if (ext === 'xlsx' && filesize < 3145728) {
        errorFile.value = ''
    } else {
        formImport.import_file = null
        errorFile.value = 'Format file yang diperbolekan xlsx dan maksmimal 3 Mb'
    }
}

function exportCSV() {
    dt.value.exportCSV();
}

function formatNumber(value) {
    if (value) return value.toLocaleString({ style: 'number' })
    return
}

const alert_response = (rsp) => {
    if (rsp.status === 'error') {
        toast.add({ severity: 'error', summary: 'Error', detail: rsp.msg, life: 3000 });
    } else if (rsp.status === 'success') {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: rsp.msg, life: 3000 });
    }
}

const displayDuplicate = (event, data) => {
    selectedDuplicate.value = data
    opn.value.toggle(event)
}
</script>

<template>
    <Head title="Data Pemilih Tetap" />
    <div>
        <div class="card">
            <Toolbar class="mb-6" v-if="auth.level < 2">
                <template #start>
                    <!-- <Button label="Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" /> -->
                    <Button label="Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="dptTerpilih.length < 1" />
                </template>

                <template #end>
                    <Button label="Import" icon="pi pi-download" severity="success" class="mr-2" @click="importDlg" outlined />
                    <Button label="Export" icon="pi pi-upload" severity="primary" @click="exportCSV($event)" :disabled="pemilih.length < 1" outlined />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:selection="dptTerpilih"
                :value="pemilih"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Data DPT"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Daftar Pemilih Tetap</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false" v-if="auth.level < 2"></Column>
                <Column field="kec_name" header="Kecamatan" sortable style="min-width: 12rem"></Column>
                <Column field="desakel_name" header="Desa/Kelurahan" sortable style="min-width: 16rem"></Column>
                <Column field="tahun_dpt" header="Tahun" sortable style="min-width: 16rem"></Column>
                <Column field="total" header="Jumlah DPT" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.total) }}
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem" v-if="auth.level < 2">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="addDialog" :style="{ width: '450px' }" :header="headerTitle" :modal="true" :closable="false" >
            <div class="flex flex-col gap-6">
                <div>
                    <label for="kecamatan" class="block font-bold">Nama Kecamatan</label>
                    <Select id="kecamatan" v-model="kecamatanSelected" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" required="true" @change="selectKec" :invalid="errorKec.length > 0"fluid></Select>
                </div>
                <div class="-mt-10" v-if="errorKec.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorKec }}</Message>
                </div>
                <div>
                    <label for="desakel" class="block font-bold">Nama Desa/Kelurahan</label>
                    <Select id="desakel" v-model="desaSelected" :options="selectDesa" optionLabel="label" placeholder="Pilih Desa/Kelurahan" required="true" :invalid="errorDesa.length > 0" fluid></Select>
                </div>
                <div class="-mt-10" v-if="errorDesa.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorDesa }}</Message>
                </div>
                <div>
                    <label for="tahun" class="block font-bold">Tahun</label>
                    <InputText id="tahun" type="number" v-model="tahun" placeholder="Tahun" required="true" fluid @change="checkTahun" :invalid="errorYear.length > 0" />
                </div>
                <div class="-mt-10" v-if="errorYear.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorYear }}</Message>
                </div>
                <div>
                    <label for="name" class="block font-bold">Jumlah DPT</label>
                    <InputNumber id="name" v-model="totaldpt" required="true" :min="1" :max="100000" placeholder="0-100.000" fluid @change="checkTotal" :invalid="errorTotal.length > 0" />
                </div>
                <div class="mb-6 -mt-10" v-if="errorTotal.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorTotal }}</Message>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="hideDialog" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-check" @click="submit" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="importDialog" modal header="Import Data" :style="{ width: '30rem' }" :closable="false">
            <span class="text-surface-500 dark:text-surface-400 block mb-8 text-center">
                Pastikan file Excel sudah sesuai dengan template yang diberikan atau
                <Button label="Unduh Template" icon="pi pi-download" everity="success" text @click="downloadTemplate" />
            </span>
            <div class="flex items-center gap-4 mb-8 w-full" v-if="!submitted">
                <!-- <label for="name" class="font-semibold w-24">Nama Kecamatan</label> -->
                <FileUpload 
                    choose-label="Pilih File Excel (*.xlsx)"
                    name="file_excel[]" 
                    mode="basic" 
                    v-model="formImport.import_file" 
                    :required="true" 
                    @input="formImport.import_file = $event.target.files[0]"
                    @change="checkFile"
                    accept=".xlsx" 
                    :maxFileSize="5000000" 
                    customUpload
                    :disabled="submitted"
                />
            </div>
            <div class="flex items-center gap-4 mb-4 -mt-4" v-if="errorFile">
                <Message severity="error" class="">{{ errorFile }}</Message>
            </div>
            <div class="flex items-center justify-center gap-4 mb-8 -mt-6" v-if="submitted">
                <!-- <span class="pi pi-spin pi-cog text-primary" style="font-size: 3rem;"></span>
                <span>Memproses ...</span> -->
                <ProgressSpinner style="width: 55px; height: 55px" strokeWidth="5" fill="transparent" animationDuration="1.5s" aria-label="Custom ProgressSpinner" />
            </div>
            <div class="flex items-center justify-center gap-4 mb-8 -mt-6" v-if="submitted">
                <span>Memproses ...</span>
            </div>
            <!-- <div class="flex items-center justify-center gap-4 mb-4 -mt-6">
                <ProgressBar v-if="formImport.progress" :value="formImport.progress.percentage" :showValue="true" class="w-full"></ProgressBar>
            </div> -->
            <div class="mb-8">&nbsp;</div>
            <div class="flex justify-end gap-2">
                <Button type="button" icon="pi pi-times" label="Batal" severity="secondary" @click="importDialog = false" :disabled="submitted" ></Button>
                <Button type="button" icon="pi pi-save" label="Import File" @click="saveImport" :disabled="submitted"></Button>
            </div>
        </Dialog>

        <Dialog v-model:visible="duplicateDialog" modal header="Import Data" :style="{ width: '35rem' }" :closable="true">
            <span class="text-surface-500 dark:text-surface-400 block mb-8 text-center">
                File berhasil di import dengan <b> {{ duplicateDataImport.length }} duplikat </b> data
            </span>
            <div class="text-surface-500 dark:text-surface-400 block mb-8 text-center">
                <div v-if="duplicateDataImport.length > 0" class="w-full">
                    <span v-for="duplicate in duplicateDataImport">
                        <Button type="button" :label="duplicate.nama_desa" class="mr-1" outlined @click="displayDuplicate($event, duplicate)" /> 
                    </span>
                </div>

                <Popover ref="opn" id="overlay_panel" style="width: 450px">
                    <table class="w-full">
                        <tbody>
                            <tr>
                                <td width="40%">Desa/Kelurahan</td>
                                <td width="60%">:  {{ selectedDuplicate.nama_desa }}</td>
                            </tr>
                            <tr>
                                <td>Kecamatan</td>
                                <td>:  {{ selectedDuplicate.nama_kecamatan }}</td>
                            </tr>
                            <tr>
                                <td>Tahun</td>
                                <td>:  {{ selectedDuplicate.tahun }}</td>
                            </tr>
                            <tr>
                                <td>Jumlah DPT</td>
                                <td>:  {{ formatNumber(selectedDuplicate.jumlah) }}</td>
                            </tr>
                        </tbody>
                    </table>                    
                </Popover>
            </div>
            <div class="mb-8">&nbsp;</div>
            <div class="flex justify-end gap-2">
                <Button type="button" icon="pi pi-times" label="Tutup" severity="secondary" @click="duplicateDialog = false"></Button>
            </div>
        </Dialog>

        <Dialog v-model:visible="templateDialog" :style="{ width: '450px' }" header="Peringatan" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="templateDialog">
                    Template file yang dipilih tidak sesuai.
                </span>
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" severity="danger" text @click="templateDialog = false" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="editData">Anda ingin menghapus data <b>{{ editData.desakel_name }}, {{ editData.kec_name }} ({{ formatNumber(editData.total) }})</b> ?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteProductDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="deleteProduct" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="dptTerpilih">Anda ingin menghapus {{ dptTerpilih.length }} data yang dicentang?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteProductsDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" text @click="deleteSelectedProducts" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>

