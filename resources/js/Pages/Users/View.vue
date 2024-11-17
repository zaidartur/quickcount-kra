<script setup>
import { defineProps, readonly, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import Toolbar from 'primevue/toolbar';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import Popover from 'primevue/popover';
import Divider from 'primevue/divider';
import Password from 'primevue/password';
import Slider from 'primevue/slider';
import InputGroup from 'primevue/inputgroup';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
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
const selectTPS = ref(new Array())

const initData = () => {
    accountLists.value = []
    if (datas.users) {
        datas.users.map((du) => {
            accountLists.value.push(du)
        })
    }
}

const initWilayah = () => {
    if (auth.level < 2) {
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
    } else {
        let kode = null
        if (auth.level === 2) {
            kode = (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString())
        } else if (auth.level === 3) {
            kode = (auth.kode.toString().substr(2,2))
        }
        if (datas.kec.length > 0) {
            kecamatan.value = []
            datas.kec.some((val, i) => {
                if (val.kec_id === kode) {
                    kecamatan.value.push({
                        label: val.kec_name,
                        value: val.kec_id
                    })
                    return true
                }
            }) 
        } else {
            kecamatan.value = []
        }
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
// console.log(auth.level, kecamatan.value)
const dt = ref();
const accountSelected = ref([])
const kecamatanSelected = ref()
const desaSelected = ref()
const tpsSelected = ref([])
const levelSelected = ref([])
const headerTitle = ref('Tambah User')
const addDialog = ref(false)
const deleteDialog = ref(false)
const deleteSelectedDialog = ref(false)
const changePwdDialog = ref(false)
const passwordDialog = ref(false)
const closableModal = ref(false)

const errorKec = ref('')
const errorDesa = ref('')
const errorTPS = ref('')
const errorName = ref('')
const errorEmail = ref('')
const errorPwd = ref('')
const errorLevel = ref('')
const errorKode = ref('')

const roles = ref({})
const editData = ref()
const pwdGenerator = ref('')
const pwdStrength = ref(24)
const pwdStrengthLabel = ref('Lemah')
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
if (auth.level < 2) {
    roles.value = [
        {label: 'Super Admin', value: 0},
        {label: 'Admin', value: 1},
        {label: 'Admin Kecamatan', value: 2},
        {label: 'Admin Desa/Kelurahan', value: 3},
        {label: 'Admin TPS', value: 4},
    ]
} else if (auth.level === 2) {
    roles.value = [
        {label: 'Admin Kecamatan', value: 2},
        {label: 'Admin Desa/Kelurahan', value: 3},
        {label: 'Admin TPS', value: 4},
    ]
} else if (auth.level === 3) {
    roles.value = [
        {label: 'Admin Desa/Kelurahan', value: 3},
        {label: 'Admin TPS', value: 4},
    ]
} else if (auth.level === 4) {
    roles.value = [
        {label: 'Admin TPS', value: 4},
    ]
}

const roleLabel = [
    {label: 'Super Admin', value: 0},
    {label: 'Admin', value: 1},
    {label: 'Admin Kecamatan', value: 2},
    {label: 'Admin Desa', value: 3},
    {label: 'Admin TPS', value: 4},
]

const isRole = (val) => {
    let isLabel = null
    roleLabel.map((rl) => {
        if (rl.value === val) {
            isLabel = rl.label
        }
    })
    return isLabel
}

const generatePwd = () => {
    let charactersArray = 'a-z'.split(',')
    let CharacterSet = ''
    let password = ''
    let size = 8
    
    switch (pwdStrength.value) {
      case 12:
        size = 10
        charactersArray = 'a-z,A-Z'.split(',')
        pwdStrengthLabel.value = 'Lemah'
        break
      case 24:
        size = 12
        charactersArray = 'a-z,A-Z,0-9'.split(',')
        pwdStrengthLabel.value = 'Cukup'
        break
     case 36:
        size = 14
        charactersArray = 'a-z,A-Z,0-9,#'.split(',')
        pwdStrengthLabel.value = 'Bagus'
        break
     case 48:
        size = 16
        charactersArray = 'a-z,A-Z,0-9,#'.split(',')
        pwdStrengthLabel.value = 'Bagus Sekali'
        break
    }
    if (charactersArray.indexOf('a-z') >= 0) {
      CharacterSet += 'abcdefghijklmnopqrstuvwxyz'
    }
    if (charactersArray.indexOf('A-Z') >= 0) {
      CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    }
    
    if (charactersArray.indexOf('0-9') >= 0) {
      CharacterSet += '0123456789'
    }
    if (charactersArray.indexOf('#') >= 0) {
      CharacterSet += '![]{}()%&*$#^<>~@|'
    }
    for (let i = 0; i < size; i++) {
      password += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length))
    }
    
    pwdGenerator.value = password
}

const checkName = () => {
    if (form.name) {
        if (form.name.length < 4 || form.name.length > 30) {
            errorName.value = 'Nama User 4-30 karakter'
            return false
        } else {
            errorName.value = ''
            return true
        }
    } else {
        errorName.value = 'Nama User harus di isi'
        return false
    }
}

const validateEmail = () => {
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
    if (levelSelected.value.value === 2 || levelSelected.value.value === 3 || levelSelected.value.value === 4) {
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
    if (levelSelected.value.value === 3 || levelSelected.value.value === 4) {
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

const checkTPS = () => {
    if (levelSelected.value.value === 4) {
        if(tpsSelected.value) {
            errorTPS.value = ''
            return true
        } else {
            errorTPS.value = 'Nomor TPS belum dipilih'
            return false
        }
    } else {
        errorTPS.value = ''
        return true
    }
}

const checkPwd = () => {
    if (form.type === 'new') {
        if (form.pass) {
            if (form.pass.length >= 8) {
                errorPwd.value = ''
                return true
            } else {
                errorPwd.value = 'Password minimal 8 karakter dan berisi huruf besar dan kecil, simbol, dan angka'
                return false
            }
        } else {
            errorPwd.value = 'Password harus di isi'
            return false
        }
    } else {
        errorPwd.value = ''
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
    
    if (auth.level < 3) {
        desa.value.map((tmp) => {
            if (tmp.kec_id === kecamatanSelected.value.value) {            
                selectDesa.value.push({
                    label: tmp.desakel_name,
                    value: tmp.full_id
                })
            }
        })
    } else if (auth.level === 3) {
        desa.value.some((tmp) => {
            if (tmp.full_id === auth.kode) {
                selectDesa.value.push({
                    label: tmp.desakel_name,
                    value: tmp.full_id
                })
                return true
            }
        })
    } else {
        desa.value.some((tmp) => {
            if (tmp.full_id === auth.kode.split('-')[0]) {            
                selectDesa.value.push({
                    label: tmp.desakel_name,
                    value: tmp.full_id
                })
                return true
            }
        })
    }
}

const openNew = () => {
    form.reset()
    headerTitle.value = 'Tambah User'
    form.type = 'new'
    clearError()
    pwdStrength.value = 24
    generatePwd()
    selectDesa.value = ''
    kecamatanSelected.value = ''
    desaSelected.value = ''
    levelSelected.value = ''

    submitted.value = false
    closableModal.value = false
    addDialog.value = true
}

const copyPwd = () => {
    navigator.clipboard.writeText(pwdGenerator.value)
    form.pass = pwdGenerator.value
    checkPwd()

    toast.add({ severity: 'success', summary: 'Sukses', detail: 'Password berhasil di salin', life: 3000 });
}

const findByValue = (val, objects) => {
    let data = []
    // const uid = (val.length < 2 ? ('0'+val) : val)

    if (objects.length > 0) {
        objects.some((arr) => {
            if (arr.value === val) {
                data = arr
                return true
            }
        })
    }
    return data
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

const tps_kecamatan = () => {
    if (levelSelected.value.value === 4) {
        selectTPS.value = []
        tpsSelected.value = {}
    }
}

const isTPS = async() => {
    selectTPS.value = []
    tpsSelected.value = {}
    if (desaSelected.value) {
        await axios.get('/data-user/get-tps/' + desaSelected.value.value).then((res) => {
            if (res.data) {
                res.data.map((tps) => {
                    selectTPS.value.push({
                        label: notps(tps.no_tps),
                        value: tps.id
                    })
                })
            }
        })
    }
}

const saveUser = async() => {
    submitted.value = true
    if (checkName() && validateEmail() && checkLevel() && checkKecamatan() && checkDesa() && checkTPS() && checkPwd()) {
        // init
        const cek  = form.type === 'new' ? await axios.get('/data-user/cek-email/' + form.email).then((res) => { return res.data }) : 'true'
        form.kode  = (levelSelected.value.value === 2 ? kecamatanSelected.value.value : (levelSelected.value.value === 3 ? desaSelected.value.value : (levelSelected.value.value === 4 ? (desaSelected.value.value+'-'+tpsSelected.value.value) : 0)))
        form.level = levelSelected.value.value
        
        // submitting if validated
        if (cek === 'true') {
            errorEmail.value = ''
            form.post('data-user/tambah-user', {
                resetOnSuccess: true,
                onSuccess: (res) => {
                    const messages = res.props.flash.message
                    initData()
                    alert_response(messages)
                    kecamatanSelected.value = []
                    desaSelected.value = []
                    submitted.value = false
                    addDialog.value = false
                },
                onError: () => {
                    toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                    submitted.value = false
                }
            })
        } else {
            errorEmail.value = 'Alamat email sudah digunakan'
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Alamat email sudah digunakan', life: 3000 });
            submitted.value = false
        }
    } else {
        checkName()
        validateEmail()
        checkLevel()
        checkKecamatan()
        checkDesa()
        checkPwd()
        submitted.value = false
    }
}

const detailUser = (prop) => {
    editUser(prop)
    headerTitle.value = 'Detail User ' + form.name
    form.type = 'detail'
    submitted.value = true
    closableModal.value = true
    console.log(prop)
}

const editUser = (prop) => {
    // console.log('pr', prop)
    selectDesa.value = []
    selectKec.value = []
    desaSelected.value = []
    kecamatanSelected.value = []
    headerTitle.value = 'Edit User'
    form.reset()
    form.type = 'edit'
    form.name   = prop.name
    form.uuid   = prop.uuid
    form.email  = prop.email
    form.level  = prop.level
    form.kode   = prop.kode

    levelSelected.value = findByValue(prop.level, roles.value)

    // kecamatan
    if (prop.level === 2) {
        const kode = (prop.kode.length < 2 ? ('0'+prop.kode.toString()) : prop.kode.toString())
        kecamatanSelected.value = findByValue(kode, kecamatan.value)
        desa.value.map((ds) => {
            if (ds.kec_id === kode) {
                selectDesa.value.push({
                    label: ds.desakel_name,
                    value: ds.full_id
                })
            }
        })
    }
    // desakel
    if (prop.level === 3) {
        const isKode = prop.kode.toString()
        const kec = isKode.substr(2, 2)
        kecamatanSelected.value = findByValue(kec, kecamatan.value)
        desa.value.map((ds) => {
            if (ds.kec_id === kec) {            
                selectDesa.value.push({
                    label: ds.desakel_name,
                    value: ds.full_id
                })
            }
        })
        desaSelected.value = findByValue(isKode, selectDesa.value)
    }
    if (prop.level === 4) {
        const isKode = prop.kode.toString()
        const kec = isKode.substr(2, 2)
        kecamatanSelected.value = findByValue(kec, kecamatan.value)
        desa.value.map((ds) => {
            if (ds.kec_id === kec) {            
                selectDesa.value.push({
                    label: ds.desakel_name,
                    value: ds.full_id
                })
            }
        })
        desaSelected.value = findByValue(isKode.split('-')[0], selectDesa.value)
        isTPS()
        tpsSelected.value = {
            label: notps(prop.no_tps),
            value: parseInt(isKode.split('-')[1])
        }
        console.log(tpsSelected.value, selectTPS.value)
    }
    
    submitted.value = false
    closableModal.value = false
    addDialog.value = true
}

const change_pwd = (prop) => {
    pwdStrength.value = 24
    generatePwd()
    form.reset()
    form.name   = prop.name
    form.uuid   = prop.uuid
    submitted.value = false
    changePwdDialog.value = true
}

const confirm_password = () => {
    submitted.value = true
    if (form.uuid && form.pass) {
        // console.log(form)
        form.post('data-user/password-user', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                submitted.value = false
                changePwdDialog.value = false

                if (form.uuid === auth.uuid) {
                    passwordDialog.value = true
                }
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Password baru belum di isi', life: 3000 });
        submitted.value = false
    }
}

const _logout = () => {
    form.post(route('logout'))
}

const delete_user = (prop) => {
    form.reset()
    form.id     = prop.id
    form.uuid   = prop.uuid
    form.name   = prop.name
    form.email  = prop.email
    form.level  = prop.level
    form.kode   = prop.kode
    form.pass   = null

    submitted.value = false
    deleteDialog.value = true
}

const delete_user_confirm = () => {
    submitted.value = true
    if (form.pass) {
        form.post('data-user/hapus-user', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                submitted.value = false
                deleteDialog.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon untuk mengisi password Anda untuk konfirmasi', life: 3000 });
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
</script>

<template>
    <Head title="Data User" />
    <div>
        <div class="card">
            <Toolbar class="mb-6" v-if="auth.level < 4">
                <template #start>
                    <Button label="Buat Akun Baru" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <!-- <Button label="Hapus" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="dptTerpilih.length < 1" /> -->
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
                            <InputText v-model="filters['global'].value" placeholder="Cari Nama..." />
                        </IconField>
                    </div>
                </template>

                <!-- <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column> -->
                <Column field="name" header="Nama User" sortable style="min-width: 12rem"></Column>
                <Column field="email" header="Alamat Email" sortable style="min-width: 16rem"></Column>
                <Column field="level" header="Role" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        {{ isRole(slotProps.data.level) }}
                        {{ (slotProps.data.level === 2 ? slotProps.data.kec_name : (slotProps.data.level === 3 ? (slotProps.data.desakel_name) : '')) }}
                    </template>
                </Column>
                <Column field="created_at" header="Tanggal Buat" sortable style="min-width: 16rem">
                    <template #body="slotProps">
                        {{ slotProps.data.created_at }}
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-info" outlined rounded severity="info" class="mr-2" @click="detailUser(slotProps.data)" v-tooltip.bottom="'Detail User'" />
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editUser(slotProps.data)" v-tooltip.bottom="'Edit User'" v-if="slotProps.data.uuid === auth.uuid || auth.level < 4" />
                        <Button icon="pi pi-key" outlined rounded severity="warn" class="mr-2" @click="change_pwd(slotProps.data)" v-tooltip.bottom="'Ubah Password'" v-if="slotProps.data.uuid === auth.uuid || auth.level < 4" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger" @click="delete_user(slotProps.data)" v-tooltip.bottom="'Hapus User'" v-if="slotProps.data.uuid !== auth.uuid && auth.level < 4" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="addDialog" :style="{ width: (form.type === 'detail' ? '900px' : (levelSelected.value === 4 ? '700px' : '500px')) }" :header="headerTitle" :modal="true" :closable="closableModal" >
            <div class="flex flex-col md:flex-row md:w-12/12">
                <div class="flex flex-col gap-6" :class="(form.type === 'detail' && auth.level < 0) ? 'md:w-5/12 ml-5' : 'md:w-full'">
                    <div>
                        <label for="name" class="block font-bold">Nama Lengkap</label>
                        <InputText id="name" v-model="form.name" placeholder="Nama User" required="true" fluid @change="checkName" :invalid="errorName.length > 0" :disabled="submitted" autofocus />
                    </div>
                    <div class="-mt-4" v-if="errorName.length">
                        <Message severity="error" class="">{{ errorName }}</Message>
                    </div>
                    <div>
                        <label for="email" class="block font-bold">Alamat Email</label>
                        <InputText id="email" type="email" v-model="form.email" placeholder="Alamat Email" :class="form.type === 'edit' ? 'cursor-not-allowed' : ''" required="true" fluid @change="validateEmail" @blur="validateEmail" :invalid="errorEmail.length > 0" :readonly="form.type === 'edit' ? true : false" :disabled="submitted" />
                    </div>
                    <div class="-mt-4" v-if="errorEmail.length">
                        <Message severity="error" class="">{{ errorEmail }}</Message>
                    </div>
                    <div v-if="form.type === 'edit' && form.uuid === auth.uuid ? false : true">
                        <label for="role" class="block font-bold">Role</label>
                        <Select id="role" v-model="levelSelected" :options="roles" optionLabel="label" placeholder="Pilih Role" required="true" @change="checkLevel" :invalid="errorLevel.length > 0" fluid :disabled="submitted"></Select>
                    </div>
                    <div class="-mt-4" v-if="errorLevel.length">
                        <Message severity="error" class="">{{ errorLevel }}</Message>
                    </div>
                    <div class="grid grid-cols-12 gap-4" v-if="(levelSelected.value === 2 || levelSelected.value === 3 || levelSelected.value === 4) && (form.type === 'edit' && form.uuid === auth.uuid ? false : true)">
                        <div 
                            :class="levelSelected.value === 2 ? 'col-span-12' : (levelSelected.value === 3 ? 'col-span-6' : 'col-span-4')"
                             v-if="levelSelected.value === 2 || levelSelected.value === 3 || levelSelected.value === 4"
                        >
                            <div v-if="levelSelected.value === 2 || levelSelected.value === 3 || levelSelected.value === 4">
                                <label for="kecamatan" class="block font-bold">Kecamatan</label>
                                <Select id="kecamatan" v-model="kecamatanSelected" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" required="true" @change="selectKec" @blur="tps_kecamatan" :invalid="errorKec.length > 0" fluid :disabled="submitted"></Select>
                            </div>
                            <div class="-mt-4" v-if="errorKec.length">
                                <label for="" class="font-semibold w-24">&nbsp;</label>
                                <Message severity="error" class="">{{ errorKec }}</Message>
                            </div>
                        </div>
                        <div 
                            :class="levelSelected.value === 3 ? 'col-span-6' : (levelSelected.value === 4 ? 'col-span-4' : '')" 
                            v-if="levelSelected.value === 3 || levelSelected.value === 4"
                        >
                            <div v-if="levelSelected.value === 3 || levelSelected.value === 4">
                                <label for="desakel" class="block font-bold">Desa/Kelurahan</label>
                                <Select id="desakel" v-model="desaSelected" :options="selectDesa" optionLabel="label" placeholder="Pilih Desa/Kelurahan" required="true" @change="checkDesa" @blur="isTPS" :invalid="errorDesa.length > 0" fluid :disabled="submitted"></Select>
                            </div>
                            <div class="-mt-4" v-if="errorDesa.length">
                                <label for="" class="font-semibold w-24">&nbsp;</label>
                                <Message severity="error" class="">{{ errorDesa }}</Message>
                            </div>
                        </div>
                        <div class="col-span-4" v-if="levelSelected.value === 4">
                            <div v-if="levelSelected.value === 4">
                                <label for="tps" class="block font-bold">Nomor TPS</label>
                                <Select id="tps" v-model="tpsSelected" :options="selectTPS" optionLabel="label" placeholder="Pilih TPS" required="true" @change="checkTPS" :invalid="errorTPS.length > 0" fluid :disabled="submitted"></Select>
                            </div>
                            <div class="-mt-4" v-if="errorTPS.length">
                                <label for="" class="font-semibold w-24">&nbsp;</label>
                                <Message severity="error" class="">{{ errorTPS }}</Message>
                            </div>
                        </div>
                    </div>
                    <div v-if="form.type === 'new'">
                        <label for="password" class="block font-bold">Password</label>
                        <Password id="password" v-model="form.pass" placeholder="Password" required="true" fluid :toggleMask="true" :feedback="true" @change="checkPwd" :invalid="errorPwd.length > 0" :disabled="submitted" />
                    </div> 
                    <div class="-mt-4" v-if="errorPwd.length">
                        <Message severity="error" class="">{{ errorPwd }}</Message>
                    </div>
                    <Divider />
                    <div v-if="form.type === 'new'">
                        <label for="generate_password" class="block font-bold">Generated Password<br> <small>(Klik tombol <u>salin</u> apabila Anda ingin menggunakan password ini)</small></label>
                        <InputGroup class="mb-5">
                            <InputText id="generate_password" v-model="pwdGenerator" placeholder="Generated Password" required="false" fluid :disabled="submitted" />
                            <Button label="Salin" icon="pi pi-copy" v-tooltip.bottom="'Salin Password'" @click="copyPwd" :disabled="submitted" />
                        </InputGroup>
                        <Slider v-model="pwdStrength" :step="12" :min="12" :max="48" class="w-full" @change="generatePwd"  v-tooltip.bottom="pwdStrengthLabel" :disabled="submitted" />
                    </div> 
                    <div class="mb-2">&nbsp;</div>
                </div>

                <div class="w-full md:w-2/12" v-if="form.type === 'detail' && auth.level < 0">
                    <Divider layout="vertical" class="!hidden md:!flex"><b>DETAIL</b></Divider>
                </div>

                <div class="w-full md:w-5/12 flex items-center justify-center py-5" v-if="form.type === 'detail' && auth.level < 0">
                    <Button label="Sign Up" icon="pi pi-user-plus" severity="success" class="w-full max-w-[17.35rem] mx-auto"></Button>
                </div>
            </div>
            <template #footer>
                <Button :label="form.type === 'detail' ? 'Tutup' : 'Batal'" icon="pi pi-times" text @click="addDialog = false" :disabled="submitted" v-if="form.type !== 'detail'" />
                <Button label="Simpan" icon="pi pi-check" @click="saveUser" :disabled="submitted" v-if="form.type !== 'detail'" />
            </template>
        </Dialog>

        <Dialog v-model:visible="changePwdDialog" :style="{ width: '450px' }" :header="'Ubah Password ' + form.name" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="password" class="block font-bold">Password Baru (Manual Password)</label>
                    <Password id="password" v-model="form.pass" placeholder="Password" required="true" fluid :toggleMask="true" :feedback="true" @change="checkPwd" :invalid="errorPwd.length > 0" :disabled="submitted" />
                </div> 
                <div class="-mt-4" v-if="errorPwd.length">
                    <Message severity="error" class="">{{ errorPwd }}</Message>
                </div>
                <Divider class="font-bold">ATAU</Divider>
                <div>
                    <label for="generate_password" class="block font-bold">
                        Generated Password 
                        <Button icon="pi pi-question" size="small" text v-tooltip.right="'Gunakan password generator ini apabila Anda menginginkannya'" />
                    </label>
                    <InputGroup class="mb-5">
                        <InputText id="generate_password" v-model="pwdGenerator" placeholder="Generated Password" required="false" fluid :disabled="submitted" />
                        <Button label="Salin" icon="pi pi-copy" v-tooltip.bottom="'Salin Password'" @click="copyPwd" :disabled="submitted" />
                    </InputGroup>
                    <Slider v-model="pwdStrength" :step="12" :min="12" :max="48" class="w-full" @change="generatePwd"  v-tooltip.bottom="pwdStrengthLabel" :disabled="submitted" />
                </div> 
                <div class="mb-2">&nbsp;</div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" severity="danger" text @click="changePwdDialog = false" :disabled="submitted" />
                <Button label="Update Password" icon="pi pi-check" @click="confirm_password" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="passwordDialog" :style="{ width: '450px' }" header="Notifikasi" :modal="true" :closable="false">
            <div class="flex items-center gap-4">
                <i class="pi pi-check-circle !text-3xl" />
                <span>Password berhasil dirubah</span>
            </div>
            <template #footer>
                <Button label="Konfirmasi" icon="pi pi-check" @click="_logout" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Konfirmasi Hapus" :modal="true">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="form">Anda ingin menghapus user <b>{{ form.name }}</b> ({{ form.email }}) ?</span>
            </div>
            <div class="mt-6 mb-6">
                <label for="password" class="block font-bold">Masukkan Password Anda untuk konfirmasi</label>
                <Password id="password" v-model="form.pass" placeholder="Password" required="true" fluid :toggleMask="false" :feedback="false" @change="checkPwd" :invalid="errorPwd.length > 0" :disabled="submitted" />
            </div> 
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="delete_user_confirm" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>