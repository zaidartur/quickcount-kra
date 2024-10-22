<script setup>
import { defineProps, ref, onBeforeMount } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { io } from 'socket.io-client';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Select from 'primevue/select';
import Message from 'primevue/message';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Password from 'primevue/password';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputNumber from 'primevue/inputnumber';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    users: Object,
    kec: Object,
    desa: Object,
    paslon: Object,
    mydata: Object,
})

const kecamatan = ref(new Array())
const desas = ref(new Array())
const paslons = ref(new Array())
const voteData = ref(new Array())
const defaultData = datas.mydata.map((my) => {
    return my
})
const votingPoint = ref(new Array())
const voteTotal = ref(0)
const expandedRows = ref([])
const dataPaslon = ref(new Array())

const socket = io('http://localhost:3000', {
    withCredentials: true,
})

const initData = () => {
    defaultData.value = []
    kecamatan.value = []
    voteData.value = []
    desas.value = []
    paslons.value = []

    if (datas.kec) {
        datas.kec.map((val, i) => {
            kecamatan.value.push({
                label: val.kec_name,
                value: val.kec_id
            })
        }) 
    }
    if (datas.desa) {
        datas.desa.map((ds) => {
            desas.value.push({
                label: ds.desakel_name,
                value: ds.full_id
            })
        })
    }
    if (datas.mydata) {
        datas.mydata.map((my) => {
            voteData.value.push(my)
        })
    }
    if (datas.paslon) {
        datas.paslon.map((dp) => {
            paslons.value.push(dp)
        })
    }
    // console.log(dataPaslon.value)
}

const initPaslon = () => {
    dataPaslon.value = []
    if (datas.paslon) {
        datas.paslon.map((dp) => {
            dataPaslon.value.push({
                uuid: dp.uuid_paslon,
                name: dp.nama_paslon,
                point: 0
            })
        })
    }
}

initData()
initPaslon
const dt = ref()
const isDesa = ref()
const dataEdit = ref(null)
const desaSelected = ref()
const confirmDialog = ref(false)
const addDialog = ref(false)
const submitted = ref(false)
const headerTitle = ref('Tambah Data')
const errorDesa = ref('')
const myPassword = ref(null)
const invalidVote = ref(
    datas.mydata.length > 0 ? datas.mydata[0].vote_tidaksah : 0
)
const editLabel = ref({
    label: 'Edit Data',
    icon: 'pi pi-pencil'
})
const inputStatus = ref(
    datas.mydata.length > 0 ? true : false
)
const roleLabel = ['Super Admin', 'Admin', 'Admin Kecamatan', 'Admin Desa']
const form = useForm({
    id: null,
    uuid: null,
    kec: null,
    desa: null,
    desaName: null,
    voteValid: null,
    voteInvalid: 0,
    totalVote: null,
    tahun: null,
    user: null,
    type: null
})

const sendingSocket = (datas) => {
    // console.log('socket', datas)
    const add = voteTotal.value + datas.vote
    voteTotal.value = add
    socket.emit('sending-paslon', datas)
}

const updateSocket = (datas) => {
    // console.log('update-socket', datas)
    const add = voteTotal.value + datas.vote
    voteTotal.value = add
    socket.emit('updating-paslon', datas)
}

function expandAll() {
    expandedRows.value = voteData.value.reduce((acc, p) => (acc[p.id] = true) && acc, {});
}

function collapseAll() {
    expandedRows.value = null;
}

const findKecamatan = (val) => {
    let res = null
    kecamatan.value.map((kc) => {
        if (kc.value === val) {
            res = kc.label
        }
    })
    return res
}

const findDesa = (val) => {
    let res = null
    datas.desa.map((ds) => {
        if (ds.full_id === val) {
            res = ds.desakel_name
        }
    })
    return res
}

// const myKecamatan = () => {
//     let isKec = null
//     if (auth.level === 2) {
//         isKec = auth.kode
//     } else if (auth.level === 3) {
//         isKec = auth.kode.substr(2,2)
//     }

//     let res = []
//     kecamatan.value.map((mk) => {
//         if (mk.value === isKec) {
//             kecamatanSelected.value = {
//                 label: mk.label,
//                 value: mk.value,
//             }
//         }
//     })
// }
const myDesa = () => {
    isDesa.value = {}
    if (auth.level === 3) {
        desas.value.map((ds) => {
            if (ds.value === auth.kode) {
                isDesa.value = {
                    label: ds.label,
                    value: ds.value
                }
            }
        })
    }
}
// myKecamatan()
myDesa()

const getTotal = () => {
    voteTotal.value = 0
    voteData.value.map((ps) => {
        const add = voteTotal.value + parseInt(ps.total)
        voteTotal.value = add
    })
}
getTotal()

const new_data = () => {
    form.reset()
    headerTitle.value = 'Tambah Data'
    form.type = 'new'
    desaSelected.value = []
    initPaslon()
    form.voteInvalid = 0
    desa_existing()
    addDialog.value = true
}

const desa_existing = () => {
    if (voteData.value.length > 0) {
        const _uid = []
        voteData.value.map((vd) => {
            _uid.push(vd.desakel_id)
        })
        
        const filter = desas.value.filter((ds) => {
            if (!_uid.includes(ds.value)) {
                return ds
            }
        })
        desas.value = filter
    }
}

const checkDesa = () => {
    if (desaSelected.value) {
        errorDesa.value = ''
        return true
    } else {
        errorDesa.value = 'Mohon untuk memilih desa/kelurahan terlebih dahulu'
        return false
    }
}

const submit = () => {
    const initVote  = dataPaslon.value
    const isInvalid = form.voteInvalid
    form.kec        = auth.kode
    form.desa       = desaSelected.value.value
    form.desaName   = desaSelected.value.label
    form.voteValid  = JSON.stringify(dataPaslon.value)
    // form.voteInvalid = invalidVote.value
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'new'

    console.log(form)
    confirmDialog.value = false
    submitted.value = true
    form.post('/suara-masuk/tambah-data', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            if (messages.status === 'success') {
                initVote.map((iv) => {
                    const data = {
                        uuid: iv.uuid,
                        kec: auth.kode,
                        desa: desaSelected.value.value,
                        vote: iv.point,
                    }
                    sendingSocket(data)
                })
                if (isInvalid > 0) {
                    const data = {
                        uuid: 'invalid',
                        kec: auth.kode,
                        desa: desaSelected.value.value,
                        vote: isInvalid,
                    }
                    sendingSocket(data)
                }
                getTotal()
                addDialog.value = false
            }

            submitted.value = false
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
            submitted.value = false
        }
    })
}

const submitVoteDialog = () => {
    form.type = 'new'
    if (checkDesa() && totalVote() > 0) {
        confirmDialog.value = true
    } else {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Mohon untuk mengisi semua field', life: 3000 });
    }
}

const totalVote = () => {
    let point = 0
    dataPaslon.value.map((vp) => {
        point = point+parseInt(vp.point)
    })
    return (point + parseInt(form.voteInvalid))
}

const checkPwd = async() => {
    submitted.value = true
    if (myPassword.value) {
        await axios.get('/suara-masuk/cek-pwd/' + btoa(myPassword.value)).then((res) => {
            if (res.data.status === 200) {
                headerTitle.value = 'Update Data Desa/Kel. ' + dataEdit.value.desakel_name
                submitted.value = false
                confirmDialog.value = false
                editData()
                addDialog.value = true
                form.type = 'update'
            } else if (res.data.status === 401) {
                submitted.value = false
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Password tidak sesuai', life: 3000 });
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon masukkan password Anda', life: 3000 });
        submitted.value = false
    }
}

const editVoteDialog = (data,type) => {
    form.type = type
    dataEdit.value = data
    myPassword.value = null
    confirmDialog.value = true
}

const editData = () => {
    dataPaslon.value = dataEdit.value.valid
    form.id         = dataEdit.value.id
    form.uuid       = dataEdit.value.uuid_vote
    form.kec        = dataEdit.value.kec_id
    form.desa       = dataEdit.value.desakel_id
    form.desaName   = dataEdit.value.desakel_name
    form.voteValid  = JSON.stringify(dataEdit.value.valid)
    // form.voteValid  = dataEdit.value.valid
    form.voteInvalid = dataEdit.value.invalid
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'update'

    invalidVote.value = dataEdit.value.invalid
    votingPoint.value = JSON.stringify(dataEdit.value.valid)

    datas.desa.map((ds) => {
        if (ds.full_id === dataEdit.value.desakel_id) {
            desaSelected.value = {
                label: ds.desakel_name,
                value: ds.full_id
            }
        }
    })
}

const checkDiffVote = () => {
    const old = JSON.parse(votingPoint.value)
    const newest = dataPaslon.value
    old.map((d, i) => {
        if (d.point === newest[i].point) {
            console.log('same')
        } else {
            const diff = parseInt(newest[i].point) - parseInt(d.point)
            const data = {
                uuid: d.uuid,
                kec: auth.kode,
                desa: desaSelected.value.value,
                vote: diff,
            }
            updateSocket(data)
        }
    })
}

const checkInvalidDiff = () => {
    const old  = parseInt(dataEdit.value.invalid)
    const diff = parseInt(form.voteInvalid) - old
    // console.log(old, diff)
    if (diff !== 0) {
        const data = {
            uuid: 'invalid',
            kec: auth.kode,
            desa: desaSelected.value.value,
            vote: diff,
        }
        updateSocket(data)
    }
}

const updateVote = () => {
    checkDiffVote()
    checkInvalidDiff()

    submitted.value = true
    console.log(form)
    form.post('/suara-masuk/tambah-data', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            if (messages.status === 'success') {
                // form.type = null
                addDialog.value = false
            }
            submitted.value = false
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
            submitted.value = false
        }
    })
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
    <Head title="Suara Masuk" />

    <div>
        <h3 class="mb-5">Input suara masuk {{ auth.level === 2 ? 'Kecamatan' : 'Desa/Kel.' }} {{ auth.level === 2 ? findKecamatan(auth.kode) : (auth.level === 3 ? (findDesa(auth.kode)+', '+findKecamatan(auth.kode.substr(2,2))) : '') }}</h3>

        <!-- <div class="mb-5">
            <Button label="Tambah Data" icon="pi pi-plus-circle" @click="submitVoteDialog" />
        </div> -->
        <div class="card">
            <div class="font-semibold text-xl mb-4">Data yang sudah ter-input {{ voteTotal }}</div>
            <DataTable v-model:expandedRows="expandedRows" :value="voteData" ref="dt" dataKey="id" tableStyle="min-width: 60rem">
                <template #header>
                    <div class="flex flex-wrap justify-start gap-2">
                        <Button label="Tambah Data" icon="pi pi-plus-circle" @click="new_data" />
                    </div>
                    <div class="flex flex-wrap justify-end gap-2">
                        <Button text icon="pi pi-plus" label="Buka Semua" @click="expandAll" />
                        <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapseAll" />
                    </div>
                </template>
                <Column expander style="width: 5rem" />
                <Column field="desakel_name" header="Nama Desa/Kel"></Column>
                <Column field="" header="Suara Sah">
                    <template #body="slotProps">
                        {{ (slotProps.data.total - parseInt(slotProps.data.invalid)) }}
                    </template>
                </Column>
                <Column field="invalid" header="Suara Tidak Sah"></Column>
                <Column field="total" header="Total">
                    <template #body="slotProps">
                        <b>{{ (slotProps.data.total) }}</b>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 3rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded v-tooltip.bottom="'Edit Data ' + slotProps.data.desakel_name" class="mr-2" @click="editVoteDialog(slotProps.data, 'edit')" />
                    </template>
                </Column>
                <template #expansion="slotProps">
                    <div class="p-4">
                        <h5>Detail Desa/Kelurahan {{ slotProps.data.desakel_name }}</h5>
                        <DataTable :value="slotProps.data.valid">
                            <Column field="name" header="Paslon"></Column>
                            <Column field="point" header="Jumlah Suara"></Column>
                        </DataTable>
                    </div>
                </template>
            </DataTable>
        </div>

        <Dialog v-model:visible="confirmDialog" :style="{ width: '450px' }" header="Konfirmasi" :modal="true" :closable="false" >
            <div class="flex items-center gap-4" v-if="form.type === 'new' || (form.type === 'update')">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Anda sudah yakin dengan data yang Anda input?</span
                >
            </div>
            <div class="w-full justify-items-center items-center align-middle mt-5 mb-5" v-if="form.type === 'edit'">
                <i class="pi pi-lock !text-3xl w-full text-center mb-5" />
                <label class="w-full">Masukkan Password Anda untuk konfirmasi :</label>
                <Password placeholder="Password Anda" v-model="myPassword" :autofocus="form.type === 'edit'" :toggleMask="true" :feedback="false" fluid class="text-center mt-3 w-[22em]" v-on:keyup.enter="checkPwd" :disabled="submitted" />
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" text @click="confirmDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="submit" :disabled="submitted" v-if="form.type === 'new'" />
                <Button label="Ya, Konfirmasi Update" icon="pi pi-check" @click="updateVoteDesa" :disabled="submitted" v-if="!inputStatus && form.type === 'update'" />
                <Button label="Konfirmasi" icon="pi pi-key" @click="checkPwd" :disabled="submitted" v-if="form.type === 'edit' && inputStatus" />
            </template>
        </Dialog>

        <Dialog v-model:visible="addDialog" :style="{ width: '450px' }" :header="headerTitle" :modal="true" :closable="false" >
            <div class="flex flex-col gap-6">
                <div>
                    <label for="desakel" class="block font-bold">Nama Desa/Kelurahan</label>
                    <Select id="desakel" v-model="desaSelected" :options="desas" optionLabel="label" placeholder="Pilih Desa/Kelurahan" required="true" @blur="checkDesa" @change="checkDesa" :invalid="errorDesa.length > 0" fluid :disabled="submitted || form.type === 'update'"></Select>
                </div>
                <div class="-mt-10" v-if="errorDesa.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorDesa }}</Message>
                </div>
                <div class="grid grid-cols-1">
                    <div class="mb-5" v-for="(psl, p) in paslons">
                        <label for="name" class="block font-bold">{{ psl.nama_paslon }}</label>
                        <InputNumber :id="`name_${psl.uuid_paslon}`" v-model="dataPaslon[p].point" required="true" :min="1" :max="100000" placeholder="0" fluid :disabled="submitted" />
                    </div>
                    <div class="mb-10">
                        <label for="name" class="block font-bold">Suara Tidak Sah</label>
                        <InputNumber id="invalid" v-model="form.voteInvalid" required="true" :min="1" :max="100000" placeholder="0" fluid :disabled="submitted" />
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="addDialog = false" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-check" @click="submitVoteDialog" :disabled="submitted" v-if="form.type === 'new'" />
                <Button label="Update" icon="pi pi-save" @click="updateVote" :disabled="submitted" v-if="form.type === 'update'" />
            </template>
        </Dialog>
    </div>
</template>