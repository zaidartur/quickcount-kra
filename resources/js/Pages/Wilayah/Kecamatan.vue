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
import RadioButton from 'primevue/radiobutton';
import Textarea from 'primevue/textarea';
import Rating from 'primevue/rating';
import Tag from 'primevue/tag';
import Message from 'primevue/message';
import { useForm, usePage, router } from '@inertiajs/vue3';

const page  = usePage()
const message = page.props.flash.message
const toast = useToast()
const datas = defineProps({
    kec: Object,
})
const kec = ref(Array())

onMounted(() => {
    ProductService.getProducts().then((data) => (products.value = data));
});


const initData = () => {
    if (datas.kec.length > 0) {
        kec.value = []
        datas.kec.map((val, i) => {
            kec.value.push(val);
        }) 
    } else {
        kec.value = []
    }
}

initData()
const errorCode = ref('')
const errorName = ref('')
const modalHeader = ref('Tambah Kecamatan')
const kecamatan = ref({})
const dt = ref();
const products = ref();
const dataDialog = ref(false);
const deleteKecamatan = ref(false);
const deleteKecamatanTerpilih = ref(false);
const isKecamatan = ref({});
const kecamatanTerpilih = ref();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const form = useForm({
    id: '',
    code: '',
    name: '',
    type: 'new'
})


const submitted = ref(false);

function openNew() {
    modalHeader.value = 'Tambah Kecamatan'
    kecamatan.value = []
    form.type = 'new'
    errorCode.value = ''
    errorName.value = ''
    dataDialog.value = true
    submitted.value = false
}

function hideDialog() {
    dataDialog.value = false;
}


function editProduct(prod) {
    modalHeader.value = 'Edit Kecamatan'
    kecamatan.value = { ...prod }
    form.type = 'edit'
    errorCode.value = ''
    errorName.value = ''
    dataDialog.value = true
    submitted.value = false
}

const checkCode = () => {
    if (kecamatan.value.kec_id < 1 || kecamatan.value.kec_id > 99) {
        errorCode.value = 'Kode Kecamatan tidak sesuai'
    } else {
        errorCode.value = ''
    }
}

const checkName = () => {
    if (kecamatan.value.kec_name) {
        errorName.value = ''
    } else {
        errorName.value = 'Nama Kecamatan wajib diisi'
    }
}

const submit = async() => {
    submitted.value = true
    if (kecamatan.value.kec_id && kecamatan.value.kec_name) {
        if (kecamatan.value.kec_id > 0 && kecamatan.value.kec_id < 100) {
            form.code = kecamatan.value.kec_id ?? ''
            form.name = kecamatan.value.kec_name ?? ''
            if (form.type === 'edit') {
                form.id = kecamatan.value.id
            } else {
                form.id = ''
            }
            // console.log(JSON.stringify(form))

            // save
            form.post('/data-wilayah/kecamatan', {
                resetOnSuccess: true,
                onSuccess: (res) => {
                    const messages = res.props.flash.message
                    initData()
                    alert_response(messages)
                    dataDialog.value = false
                    submitted.value = false
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
                    submitted.value = false
                }
            })
        } else {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Kode Kecamatan tidak sesuai', life: 3000 });
            submitted.value = false
        }
    } else {
        errorCode.value = 'Kode Kecamatan wajib diisi'
        errorName.value = 'Nama Kecamatan wajib diisi'
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon untuk mengisi semua inputan', life: 3000 });
        submitted.value = false
    }
}

const alert_response = (rsp) => {
    if (rsp.status === 'error') {
        toast.add({ severity: 'error', summary: 'Error', detail: rsp.msg, life: 3000 });
    } else if (rsp.status === 'success') {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: rsp.msg, life: 3000 });
    }
}

const delete_data = () => {
    submitted.value = true
    form.id     = isKecamatan.value.id
    form.code   = isKecamatan.value.kec_id
    form.name   = isKecamatan.value.kec_name
    form.type   = 'single'

    // sending
    form.post('/data-wilayah/hapus-kecamatan', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            deleteKecamatan.value = false
            submitted.value = false
            isKecamatan.value = null
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
            submitted.value = false
        }
    })
}

function confirmDeleteData(kc) {
    isKecamatan.value = kc
    deleteKecamatan.value = true;
}

function exportCSV() {
    dt.value.exportCSV();
}

function confirmDeleteSelected() {
    deleteKecamatanTerpilih.value = true;
}

function deleteSelectedData() {
    submitted.value = true

    // initiate
    let uid = []
    let code = []
    let name = []
    if (kecamatanTerpilih.value.length > 0 ) {
        kecamatanTerpilih.value.map((kt) => {
            uid.push(kt.id)
            code.push(kt.kec_id)
            name.push(kt.kec_name)
        })
    }

    form.id     = uid.toString()
    form.code   = code.toString()
    form.name   = name.toString()
    form.type   = 'multi'

    // sending
    form.post('/data-wilayah/hapus-kecamatan', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            deleteKecamatanTerpilih.value = false
            submitted.value = false
            kecamatanTerpilih.value = null
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
            submitted.value = false
        }
    })
}

</script>

<template>
    <Head title="Data Wilayah" />
    <div class="mb-6">
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!kecamatanTerpilih || !kecamatanTerpilih.length" />
                </template>

                <template #end>
                    <Button label="Import" icon="pi pi-download" severity="success" class="mr-2" outlined @click="exportCSV($event)" />
                    <Button label="Export" icon="pi pi-upload" severity="primary" outlined @click="exportCSV($event)" :disabled="kec.length > 0 ? false : true" />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:selection="kecamatanTerpilih"
                :value="kec"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Kecamatan "
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Data Kecamatan</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="kec_id" header="Code" sortable style="min-width: 12rem"></Column>
                <Column field="kec_name" header="Name" sortable style="min-width: 16rem"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteData(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="dataDialog" modal :header="modalHeader" :style="{ width: '25rem' }" :closable="false">
            <span class="text-surface-500 dark:text-surface-400 block mb-8">Tulislah kode Kecamatan saja<br>(tanpa kode Kabupaten)</span>
            <div class="flex items-center gap-4 mb-4">
                <label for="code" class="font-semibold w-24">Kode</label>
                <!-- <InputNumber id="code" class="flex-auto" min="0" max="99" v-model="kecamatan.kec_id" v-tooltip="'2 digit kode kecamatan'" :invalid="form.code < 1" @blur="checkCode" @change="checkCode" /> -->
                 <InputText id="code" type="number" class="flex-auto" autocomplete="off" v-model="kecamatan.kec_id" autofocus="true" :required="true" :invalid="errorCode.length > 0" @keyup="checkCode" />
            </div>
            <div class="flex items-center gap-4 mb-6 -mt-2" v-if="errorCode.length">
                <label for="code" class="font-semibold w-24">&nbsp;</label>
                <Message severity="error" class="">{{ errorCode }}</Message>
            </div>
            <div class="flex items-center gap-4 mb-8">
                <label for="name" class="font-semibold w-24">Nama Kecamatan</label>
                <InputText id="name" class="flex-auto" autocomplete="off" v-model="kecamatan.kec_name" :required="true" :invalid="errorName.length > 0" @keyup="checkName" />
            </div>
            <div class="flex items-center gap-4 mb-8 -mt-6" v-if="errorName.length">
                <label for="code" class="font-semibold w-24">&nbsp;</label>
                <Message severity="error" class="">{{ errorName }}</Message>
            </div>
            <div class="flex justify-end gap-2">
                <Button type="button" icon="pi pi-times" label="Batal" severity="secondary" @click="hideDialog" :disabled="submitted" ></Button>
                <Button type="button" icon="pi pi-save" label="Simpan" @click="submit" :disabled="submitted"></Button>
            </div>
        </Dialog>

        <Dialog v-model:visible="deleteKecamatan" :style="{ width: '450px' }" header="Confirm" :modal="true" :closable="false" >
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="isKecamatan">Anda yakin ingin menghapus <b>{{ isKecamatan.kec_name }}</b>?</span
                >
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" text @click="deleteKecamatan = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="delete_data" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteKecamatanTerpilih" :style="{ width: '450px' }" header="Confirm" :modal="true" :closable="false" >
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="isKecamatan">Anda yakin ingin menghapus {{ kecamatanTerpilih.length }} data yang dicentang?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" text @click="deleteKecamatanTerpilih = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" text @click="deleteSelectedData" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>
