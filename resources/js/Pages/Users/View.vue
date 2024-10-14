<script setup>
import { defineProps, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import Toolbar from 'primevue/toolbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputNumber from 'primevue/inputnumber';
import FileUpload from 'primevue/fileupload';
import Popover from 'primevue/popover';
import ProgressSpinner from 'primevue/progressspinner';
import Password from 'primevue/password';

const page  = usePage()
const message = page.props.flash.message
const toast = useToast()
const datas = defineProps({
    users: Object,
    kec: Object,
    desa: Object,
})

const accountLists = ref(new Array())
const kecamatan = ref(new Array())
const desa = ref(new Array())
const selectDesa = ref(new Array())

const initData = () => {
    accountLists.value = []
    if (datas.users) {
        datas.users.map((du) => {
            accountLists.value.push(du)
        })
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
// console.log(accountLists.value)
const dt = ref();
const accountSelected = ref([])
const kecamatanSelected = ref()
const desaSelected = ref()
const levelSelected = ref([])
const headerTitle = ref('Tambah User')
const addDialog = ref(false)
const deleteDialog = ref(false)
const deleteSelectedDialog = ref(false)
const errorKec = ref('')
const errorDesa = ref('')
const errorName = ref('')
const errorEmail = ref('')
const errorPwd = ref('')
const errorLevel = ref('')
const errorKode = ref('')
const editData = ref()
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false)
const form = useForm({
    id: null,
    name: null,
    uuid: null,
    email: null,
    level: null,
    kode: null,
    pass: null,
    type: null,
})
const roles = [
    {label: 'Super Admin', value: 0},
    {label: 'Admin', value: 1},
    {label: 'Admin Kecamatan', value: 2},
    {label: 'Admin Desa/Kelurahan', value: 3},
]

const validateEmail = () => {
    console.log(form.email)
    if (form.email && form.email.length > 0) {
        if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(form.email)) {
            errorEmail.value = ''
            return true
        } else {
            errorEmail.value = 'Format alamat email tidak sesuai'
            return false
        }
    } else {
        errorEmail.value = 'Alamat email harus di isi'
        return false
    }
}

const checkLevel = () => {
    if (levelSelected.value) {
        errorLevel.value = ''
        return true
    } else {
        errorLevel.value = 'Role belum di pilih'
        return false
    }
}

const checkKecamatan = () => {
    if (levelSelected.value.value === 2 || levelSelected.value.value === 3) {
        if (kecamatanSelected.value) {
            errorKec.value = ''
            return true
        } else {
            errorKec.value = 'Kecamatan belum dipilih'
            return false
        }
    } else {
        errorKec.value = ''
        return true
    }
}

const checkDesa = () => {
    if (levelSelected.value.value === 3) {
        if(desaSelected.value) {
            errorDesa.value = ''
            return true
        } else {
            errorDesa.value = 'Desa/Kelurahan belum dipilih'
            return false
        }
    } else {
        errorDesa.value = ''
        return true
    }
}

const clearError = () => {
    errorKec.value = ''
    errorDesa.value = ''
    errorName.value = ''
    errorEmail.value = ''
    errorPwd.value = ''
    errorLevel.value = ''
    errorKode.value = ''
}

const selectKec = () => {
    checkKecamatan()
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

const openNew = () => {
    form.reset()
    headerTitle.value = 'Tambah User'
    form.type = 'new'
    clearError()
    selectDesa.value = ''
    kecamatanSelected.value = ''
    desaSelected.value = ''
    levelSelected.value = ''

    addDialog.value = true
}

const saveUser = () => {
    if (validateEmail() && checkLevel() && checkKecamatan() && checkDesa()) {
        console.log('submit', form)
    } else {
        validateEmail()
        checkLevel()
        checkKecamatan()
        checkDesa()
    }
}

const delete_user = (prop) => {
    form.reset()
    form.id     = prop.id
    form.uuid   = prop.uuid
    form.name   = prop.name
    form.email  = prop.email
    form.level  = prop.level
    form.kode   = prop.kode

    deleteDialog.value = true
}

const delete_user_selected = () => {
    //
}
</script>

<template>
    <Head title="Data User" />
    <div>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <!-- <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="dptTerpilih.length < 1" /> -->
                </template>

                <template #end>
                    <Button label="Hapus" icon="pi pi-trash" severity="danger" class="mr-2" @click="deleteSelectedDialog = true" outlined :disabled="accountSelected.length < 1" />
                    <!-- <Button label="Export" icon="pi pi-upload" severity="primary" @click="exportCSV($event)" :disabled="pemilih.length < 1" outlined /> -->
                </template>
            </Toolbar>

            <DataTable
                ref="dt"
                v-model:selection="accountSelected"
                :value="accountLists"
                dataKey="id"
                :paginator="true"
                :rows="10"
                :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} Data User"
            >
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Daftar User</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="name" header="Nama User" sortable style="min-width: 12rem"></Column>
                <Column field="email" header="Alamat Email" sortable style="min-width: 16rem"></Column>
                <Column field="level" header="Role" sortable style="min-width: 16rem"></Column>
                <Column field="created_at" header="Tanggal Buat" sortable style="min-width: 16rem"></Column>
                <!-- <Column field="created_at" header="Tanggal Buat" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.total) }}
                    </template>
                </Column> -->
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-info" outlined rounded severity="info" class="mr-2" @click="editProduct(slotProps.data)" v-tooltip.bottom="'Detail User'" />
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" v-tooltip.bottom="'Edit User'" />
                        <Button icon="pi pi-key" outlined rounded severity="warn" class="mr-2" @click="editProduct(slotProps.data)" v-tooltip.bottom="'Ubah Password'" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="delete_user(slotProps.data)" v-tooltip.bottom="'Hapus User'" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="addDialog" :style="{ width: '450px' }" :header="headerTitle" :modal="true" :closable="false" >
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold">Nama Lengkap</label>
                    <InputText id="name" v-model="form.name" placeholder="Nama User" required="true" fluid @change="" :invalid="errorName.length > 0" autofocus />
                </div>
                <div class="-mt-10" v-if="errorName.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorName }}</Message>
                </div>
                <div>
                    <label for="email" class="block font-bold">Alamat Email</label>
                    <InputText id="email" type="email" v-model="form.email" placeholder="Alamat Email" required="true" fluid @change="validateEmail" @blur="validateEmail" :invalid="errorEmail.length > 0" />
                </div>
                <div class="-mt-10" v-if="errorEmail.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorEmail }}</Message>
                </div>
                <div>
                    <label for="role" class="block font-bold">Role</label>
                    <Select id="role" v-model="levelSelected" :options="roles" optionLabel="label" placeholder="Pilih Role" required="true" @change="checkLevel" :invalid="errorLevel.length > 0" fluid></Select>
                </div>
                <div class="-mt-10" v-if="errorLevel.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorLevel }}</Message>
                </div>
                <div class="grid grid-cols-12 gap-4" v-if="levelSelected.value === 2 || levelSelected.value === 3">
                    <div :class="levelSelected.value === 2 ? 'col-span-12' : 'col-span-6'">
                        <div v-if="levelSelected.value === 2 || levelSelected.value === 3">
                            <label for="kecamatan" class="block font-bold">Nama Kecamatan</label>
                            <Select id="kecamatan" v-model="kecamatanSelected" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" required="true" @change="selectKec" :invalid="errorKec.length > 0" fluid></Select>
                        </div>
                        <div class="-mt-4" v-if="errorKec.length">
                            <label for="" class="font-semibold w-24">&nbsp;</label>
                            <Message severity="error" class="">{{ errorKec }}</Message>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <div v-if="levelSelected.value === 3">
                            <label for="desakel" class="block font-bold">Nama Desa/Kelurahan</label>
                            <Select id="desakel" v-model="desaSelected" :options="selectDesa" optionLabel="label" placeholder="Pilih Desa/Kelurahan" required="true" @change="checkDesa" :invalid="errorDesa.length > 0" fluid></Select>
                        </div>
                        <div class="-mt-4" v-if="errorDesa.length">
                            <label for="" class="font-semibold w-24">&nbsp;</label>
                            <Message severity="error" class="">{{ errorDesa }}</Message>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="password" class="block font-bold">Password</label>
                    <Password id="password" v-model="form.pass" placeholder="Password" required="true" fluid :toggleMask="true" :feedback="false" @change="" :invalid="errorPwd.length > 0" />
                </div>
                <div class="-mt-10" v-if="errorPwd.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorPwd }}</Message>
                </div>
                <div class="mb-2">&nbsp;</div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="addDialog = false" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-check" @click="saveUser" :disabled="submitted" />
            </template>
        </Dialog>

        <!-- <Dialog v-model:visible="duplicateDialog" modal header="Import Data" :style="{ width: '35rem' }" :closable="true">
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
        </Dialog> -->

        <!-- <DeleteDialog
            :label="form.name"
            :show-dialog="deleteDialog"
            :submitted="submitted"
        /> -->
        <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="form">Anda ingin menghapus data <b>{{ form.name }}</b> ?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="deleteProduct" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteSelectedDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="accountSelected">Anda ingin menghapus {{ accountSelected.length }} data yang dicentang?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteSelectedDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" text @click="deleteSelectedProducts" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>