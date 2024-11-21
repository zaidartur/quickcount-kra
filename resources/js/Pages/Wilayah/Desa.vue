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
import Message from 'primevue/message';
import { useForm, usePage } from '@inertiajs/vue3';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    kec: Object,
    desa: Object,
})

const desa = ref(Array())
const kecamatan = ref(Array())

onMounted(() => {
    ProductService.getProducts().then((data) => (multidesa.value = data));
});

const initData = () => {
    if (datas.desa.length > 0) {
        desa.value = []
        datas.desa.map((val, i) => {
            desa.value.push(val);
        }) 
    } else {
        desa.value = []
    }
    // desa.value = []
    
    if (datas.kec.length > 0) {
        kecamatan.value = []
        datas.kec.map((kc) => {
            kecamatan.value.push({
                label: kc.kec_name,
                value: kc.kec_id
            })
        })
    } else {
        kecamatan.value = []
    }
} 

initData()
const dt = ref();
const errorCode = ref('')
const errorName = ref('')
const errorKec = ref('')
const modalHeader = ref('Tambah Desa')
const multidesa = ref();
const desaDialog = ref(false);
const deleteDesaDialog = ref(false);
const deleteMultiDesaDialog = ref(false);
const isDesa = ref({});
const desaTerpilih = ref();
const kecamatanTerpilih = ref()
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const submitted = ref(false);
const form = useForm({
    id: '',
    kec: '',
    code: 0,
    name: '',
    type: 'new'
})

function openNew() {
    isDesa.value = {};
    form.kec = null
    form.code = 0,
    form.name = '',
    form.type = 'new'
    kecamatanTerpilih.value = null

    errorCode.value = ''
    errorName.value = ''
    errorKec.value = ''

    submitted.value = false;
    desaDialog.value = true;
}

function hideDialog() {
    desaDialog.value = false;
    submitted.value = false;
}

const checkKecamatan = () => {
    if (kecamatanTerpilih.value) {
        errorKec.value = ''
        return true
    } else {
        errorKec.value = 'Kecamatan belum dipilih'
        return false
    }
}

const checkCode = () => {
    if ((parseInt(form.code) < 100 || parseInt(form.code) > 9999)) {
        errorCode.value = 'Kode Desa/Kelurahan tidak sesuai'
        return false
    } else {
        errorCode.value = ''
        return true
    }
}

const checkName = () => {
    if (form.name) {
        errorName.value = ''
        return true
    } else {
        errorName.value = 'Nama Desa/Kelurahan wajib diisi'
        return false
    }
}

const saveDesa = () => {
    submitted.value = true
    // if (kecamatanTerpilih.value && form.code && form.name) {
    if (checkKecamatan() && checkCode() && checkName()) {
        if (form.code > 100 && form.code < 9999) {
            form.kec = kecamatanTerpilih.value.value
            // console.log(form)
            form.post('/data-wilayah/desa', {
                resetOnSuccess: true,
                onSuccess: (res) => {
                    const messages = res.props.flash.message
                    initData()
                    alert_response(messages)
                    desaDialog.value = false
                    submitted.value = false
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
                    submitted.value = false
                }
            })
        } else {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Kode Desa/Kelurahan tidak sesuai', life: 3000 });
            submitted.value = false
        }
    } else {
        submitted.value = false
        checkKecamatan()
        checkCode()
        checkName()
        toast.add({ severity: 'error', summary: 'Perhatian', detail: 'Mohon untuk mengisi semua inputan', life: 3000 });
    }
}

function editProduct(prod) {
    // product.value = { ...prod };
    isDesa.value = { ...prod}
    form.id   = isDesa.value.id
    form.type = 'edit'
    form.code = isDesa.value.desakel_id
    form.name = isDesa.value.desakel_name
    kecamatanTerpilih.value = {
        label: isDesa.value.kec_name,
        value: isDesa.value.kec_id
    }

    errorCode.value = ''
    errorName.value = ''
    errorKec.value = ''

    desaDialog.value = true;
}

const alert_response = (rsp) => {
    if (rsp.status === 'error') {
        toast.add({ severity: 'error', summary: 'Error', detail: rsp.msg, life: 3000 });
    } else if (rsp.status === 'success') {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: rsp.msg, life: 3000 });
    }
}

function confirmDeleteProduct(prod) {
    isDesa.value = prod;
    deleteDesaDialog.value = true;
}

function deleteProduct() {
    submitted.value = true
    // init form
    form.id     = isDesa.value.id
    form.kec    = isDesa.value.kec_id
    form.code   = isDesa.value.desakel_id
    form.name   = isDesa.value.desakel_name
    form.type   = 'single'

    form.post('data-wilayah/hapus-desa', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            deleteDesaDialog.value = false
            submitted.value = false
            isDesa.value = {}
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
            submitted.value = false
        }
    })
}

function exportCSV() {
    dt.value.exportCSV();
}

function confirmDeleteSelected() {
    deleteMultiDesaDialog.value = true
}

function deleteMultiDesaTerpilih() {
    submitted.value = true
    // initiate
    let uid = []
    let code = []
    let name = []
    if (desaTerpilih.value.length > 0 ) {
        desaTerpilih.value.map((ds) => {
            uid.push(ds.id)
            code.push(ds.kec_id)
            name.push(ds.kec_name)
        })
    }

    form.id     = uid.toString()
    form.code   = code.toString()
    form.name   = name.toString()
    form.type   = 'multi'

    form.post('/data-wilayah/hapus-desa', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            deleteMultiDesaDialog.value = false
            desaTerpilih.value = null
            submitted.value = false        
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
    <div>
        <div class="card">
            <Toolbar class="mb-6" v-if="auth.level < 2">
                <template #start>
                    <Button label="Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!desaTerpilih || !desaTerpilih.length" />
                </template>

                <template #end>
                    <Button label="Import" icon="pi pi-download" severity="success" class="mr-2" @click="openNew" outlined />
                    <Button label="Export" icon="pi pi-upload" severity="primary" @click="exportCSV($event)" :disabled="desa.length > 0 ? false : true" outlined />
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:selection="desaTerpilih"
                :value="desa"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Desa/Kelurahan"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Data Desa/Kelurahan</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false" v-if="auth.level < 2"></Column>
                <Column field="desakel_id" header="Code" sortable style="min-width: 12rem"></Column>
                <Column field="desakel_name" header="Name" sortable style="min-width: 16rem"></Column>
                <Column field="kec_name" header="Kecamatan" sortable style="min-width: 16rem"></Column>
                <Column :exportable="false" style="min-width: 12rem" v-if="auth.level < 2">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="desaDialog" :style="{ width: '450px' }" :header="modalHeader" :modal="true" :closable="false">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="kecamatan" class="block font-bold mb-3">Nama Kecamatan</label>
                    <Select id="kecamatan" v-model="kecamatanTerpilih" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" autofocus @change="checkKecamatan"  :invalid="errorKec.length > 0" fluid></Select>
                </div>
                <div class="mb-1 -mt-10" v-if="errorKec.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorKec }}</Message>
                </div>
                <div>
                    <label for="code" class="block font-bold mb-3">Kode Desa/Kelurahan</label>
                    <InputText id="code" type="number" v-model="form.code" required="true" :invalid="errorCode.length > 0" @keyup="checkCode" fluid />
                </div>
                <div class="mb-1 -mt-10" v-if="errorCode.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorCode }}</Message>
                </div>
                <div>
                    <label for="name" class="block font-bold mb-3">Nama Desa/Kelurahan</label>
                    <InputText id="name" v-model="form.name" required="true" :invalid="errorName.length > 0" @keyup="checkName" fluid />
                </div>
                <div class="mb-5 -mt-10" v-if="errorName.length">
                    <label for="code" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorName }}</Message>
                </div>
            </div>

            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="hideDialog" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-check" @click="saveDesa" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteDesaDialog" :style="{ width: '450px' }" header="Confirm" :modal="true" :closable="false" >
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="isDesa">Anda ingin menghapus <b>{{ isDesa.desakel_name + ' (' + isDesa.kec_name + ')' }}</b> ?</span
                >
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" text @click="deleteDesaDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="deleteProduct" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteMultiDesaDialog" :style="{ width: '450px' }" header="Confirm" :modal="true" :closable="false">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="isDesa">Anda yakin ingin menghapus {{ desaTerpilih.length }} data yang dicentang?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" text @click="deleteMultiDesaDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" text @click="deleteMultiDesaTerpilih" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>
