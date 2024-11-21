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
import Tag from 'primevue/tag';

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
    role: Object,
    sumDPT: Number,
})

const kecamatan = ref(new Array())
const desas = ref(new Array())
const datatps = ref(new Array())
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
// const socket = io('https://qcws.caturnus.com/', {
//     withCredentials: true,
// })

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
}

const initPaslon = async() => {
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
initPaslon()

const dt = ref()
const isDesa = ref()
const dataEdit = ref(null)
const desaSelected = ref()
const tpsSelected = ref()
const confirmDialog = ref(false)
const addDialog = ref(false)
const submitted = ref(false)
const headerTitle = ref('Tambah Data')
const errorDesa = ref('')
const myPassword = ref(null)
const jumlahDPT = ref(null)
const rekapInputan = ref(0)
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
const roleLabel = ['Super Admin', 'Admin', 'Admin Kecamatan', 'Admin Desa', 'Admin TPS']
const form = useForm({
    id: null,
    uuid: null,
    dpt: null,
    kec: null,
    desa: null,
    desaName: null,
    tps: null,
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
    kecamatan.value.some((kc) => {
        if (kc.value === val) {
            res = kc.label
            return true
        }
    })
    return res
}

const findDesa = (val) => {
    let res = null
    datas.desa.some((ds) => {
        if (ds.full_id === val) {
            res = ds.desakel_name
            return true
        }
    })
    return res
}

const myDesa = () => {
    isDesa.value = {}
    const kode = auth.kode.toString()
    if (auth.level === 3) {
        desas.value.some((ds) => {
            if (ds.value === auth.kode) {
                isDesa.value = {
                    label: ds.label,
                    value: ds.value
                }
                return true
            }
        })
    }
}
// myKecamatan()
myDesa()
// console.log('votedata', voteData.value)

const getTotal = () => {
    voteTotal.value = 0
    voteData.value.map((ps) => {
        const add = voteTotal.value + parseInt(ps.total_vote)
        voteTotal.value = add
    })
}
getTotal()

const notps = (data) => {
    if (data < 10) {
        return `00${data}`
    } else if (data > 9 && data < 100) {
        return `0${data}`
    } else {
        return data
    }
}

const new_data = async() => {
    await axios.get('/data-user/get-tps/' + auth.kode.split('-')[0]).then((res) => {
        if (res.data) {
            datatps.value = []
            let default_tps = []
            res.data.map((tps) => {
                default_tps.push({
                    label: notps(tps.no_tps),
                    value: tps.id,
                    dpt: tps.total
                })
            })

            if (voteData.value.length > 0) {
                const _uid = []
                voteData.value.map((vd) => {
                    _uid.push(vd.dpt_id)
                })
                
                const filter = default_tps.filter((ds) => {
                    if (!_uid.includes(ds.value)) {
                        return ds
                    }
                })
                datatps.value = filter
            } else {
                datatps.value = default_tps
            }
        }
    })
    form.reset()
    headerTitle.value = 'Tambah Data'
    form.type = 'new'
    desaSelected.value = []
    initPaslon()
    form.voteInvalid = 0
    jumlahDPT.value = null
    rekapInputan.value = 0
    tpsSelected.value = {}
    // desa_existing()
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
    if (tpsSelected.value && tpsSelected.value.value !== undefined) {
        errorDesa.value = ''
        jumlahDPT.value = tpsSelected.value.dpt
        return true
    } else {
        errorDesa.value = 'Mohon untuk memilih TPS dahulu'
        // console.log('error')
        return false
    }
}

const submit = () => {
    const initVote  = dataPaslon.value
    const isInvalid = form.voteInvalid
    const kode      = auth.kode.toString()
    form.dpt        = tpsSelected.value.value
    form.kec        = kode.substr(2,2)
    form.desa       = kode
    form.desaName   = isDesa.value.label
    form.voteValid  = JSON.stringify(dataPaslon.value)
    // form.voteInvalid = invalidVote.value
    form.tps        = tpsSelected.value.label
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'new'

    // console.log(form)
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
                        kec: kode.substr(2,2),
                        desa: kode,
                        tps: tpsSelected.value.value,
                        vote: iv.point,
                    }
                    sendingSocket(data)
                })
                if (isInvalid > 0) {
                    const data = {
                        uuid: 'invalid',
                        kec: kode.substr(2,2),
                        desa: kode,
                        tps: tpsSelected.value.value,
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
    if (dataPaslon.value) {
        dataPaslon.value.map((vp) => {
            point = point+parseInt(vp.point)
        })
        
    }
    return (point + parseInt(form.voteInvalid))
}

const checkPwd = async() => {
    submitted.value = true
    if (myPassword.value) {
        await axios.get('/suara-masuk/cek-pwd/' + btoa(myPassword.value)).then((res) => {
            if (res.data.status === 200) {
                headerTitle.value = 'Update TPS ' + notps(dataEdit.value.no_tps)
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
    dataPaslon.value = JSON.parse(data.vote_sah)
    confirmDialog.value = true
}

const editData = async() => {
    await axios.get('/data-user/get-tps/' + auth.kode.split('-')[0]).then((res) => {
        if (res.data) {
            datatps.value = []
            res.data.map((tps) => {
                datatps.value.push({
                    label: notps(tps.no_tps),
                    value: tps.id,
                    dpt: tps.total
                })
            })
        }
    })

    form.id         = dataEdit.value.id
    form.uuid       = dataEdit.value.uuid_vote
    form.dpt        = dataEdit.value.dpt_id
    form.kec        = dataEdit.value.kec_id
    form.desa       = dataEdit.value.full_id
    form.desaName   = dataEdit.value.desakel_name
    form.tps        = dataEdit.value.no_tps
    // form.voteValid  = JSON.stringify(dataEdit.value.vote_sah)
    // form.voteValid  = dataEdit.value.valid
    form.voteInvalid = dataEdit.value.vote_tidaksah
    form.user       = auth.uuid
    form.type       = 'update'

    invalidVote.value = dataEdit.value.vote_tidaksah
    votingPoint.value = dataEdit.value.vote_sah
    tpsSelected.value = {
        label: notps(dataEdit.value.no_tps),
        value: dataEdit.value.dpt_id,
        dpt: dataEdit.value.total
    }
    jumlahDPT.value = dataEdit.value.total
    rekapInputan.value = parseInt(dataEdit.value.total_vote)
}

const checkDiffVote = () => {
    const old = JSON.parse(votingPoint.value)
    const newest = dataPaslon.value
    const kode = auth.kode.toString()
    old.map((d, i) => {
        if (d.point === newest[i].point) {
            console.log('same')
        } else {
            const diff = parseInt(newest[i].point) - parseInt(d.point)
            const data = {
                uuid: d.uuid,
                kec: kode.substr(2,2),
                desa: kode,
                vote: diff,
            }
            updateSocket(data)
        }
    })
}

const checkInvalidDiff = () => {
    const old  = parseInt(dataEdit.value.vote_tidaksah)
    const diff = parseInt(form.voteInvalid) - old
    const kode = auth.kode.toString()
    // console.log(old, diff)
    if (diff !== 0) {
        const data = {
            uuid: 'invalid',
            kec: kode.substr(2,2),
            desa: kode,
            vote: diff,
        }
        updateSocket(data)
    }
}

const updateVote = () => {    
    confirmDialog.value = true
}

const updateVoteDesa = () => {
    checkDiffVote()
    checkInvalidDiff()

    confirmDialog.value = false
    submitted.value = true
    form.voteValid  = JSON.stringify(dataPaslon.value)
    form.totalVote  = totalVote()
    // console.log(form)
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

const sum_suara_masuk = () => {
    rekapInputan.value = 0
    dataPaslon.value.map((dp) => {
        if (dp.point) {
            const res = rekapInputan.value + parseInt(dp.point)
            rekapInputan.value = res
        } else {
            const res = rekapInputan.value
            rekapInputan.value = res
        }
    })
    const _total = rekapInputan.value + parseInt(form.voteInvalid)
    rekapInputan.value = _total
}

const alert_response = (rsp) => {
    if (rsp.status === 'error') {
        toast.add({ severity: 'error', summary: 'Error', detail: rsp.msg, life: 3000 });
    } else if (rsp.status === 'success') {
        toast.add({ severity: 'success', summary: 'Berhasil', detail: rsp.msg, life: 3000 });
    }
}

const formatNumber = (num) => {
    const value = parseInt(num)
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
}
</script>

<template>
    <Head title="Suara Masuk" />

    <div>
        <h3 class="mb-5">Input suara masuk Desa/Kel. {{ datas.role.text }}</h3>

        <!-- <div class="mb-5">
            <Button label="Tambah Data" icon="pi pi-plus-circle" @click="submitVoteDialog" />
        </div> -->
        <div class="card">
            <div class="font-semibold text-xl mb-4">Jumlah suara masuk {{ formatNumber(voteTotal) }} dari {{ formatNumber(datas.sumDPT) }} ({{ ((voteTotal/datas.sumDPT) * 100).toFixed(2) }}%)</div>
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
                <Column expander style="width: 5rem" header="Detail" />
                <Column field="no_tps" header="Nomor TPS">
                    <template #body="slotProps">
                        TPS {{ notps(slotProps.data.no_tps) }}
                    </template>
                </Column>
                <Column field="" header="Suara Sah">
                    <template #body="slotProps">
                        {{ (slotProps.data.total_vote - parseInt(slotProps.data.vote_tidaksah)) }}
                    </template>
                </Column>
                <Column field="vote_tidaksah" header="Suara Tidak Sah"></Column>
                <Column field="total_vote" header="Total Suara">
                    <template #body="slotProps">
                        <b>
                            <Tag 
                                :value="formatNumber(slotProps.data.total_vote) + ' Suara (' + ((slotProps.data.total_vote / slotProps.data.total) * 100).toFixed(0) + '%)'" 
                                :severity="((slotProps.data.total_vote / slotProps.data.total) * 100) === 100 ? 'success' : 'warn'" 
                            />
                            <!-- {{ (slotProps.data.total_vote) }} suara ({{ ((slotProps.data.total_vote / slotProps.data.total) * 100).toFixed(0) }}%) -->
                        </b>
                    </template>
                </Column>
                <Column field="total" header="Jumlah DPT">
                    <template #body="slotProps">
                        <b>{{ (slotProps.data.total) }}</b>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 3rem" header="Edit">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded v-tooltip.bottom="'Edit TPS ' + notps(slotProps.data.no_tps)" class="mr-2" @click="editVoteDialog(slotProps.data, 'edit')" />
                    </template>
                </Column>
                <template #expansion="slotProps">
                    <div class="p-4">
                        <h5>Detail TPS {{ notps(slotProps.data.no_tps) }}</h5>
                        <DataTable :value="JSON.parse(slotProps.data.vote_sah)">
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
                <Button label="Ya, Konfirmasi Update" icon="pi pi-check" @click="updateVoteDesa" :disabled="submitted" v-if="form.type === 'update'" />
                <Button label="Konfirmasi" icon="pi pi-key" @click="checkPwd" :disabled="submitted" v-if="form.type === 'edit'" />
            </template>
        </Dialog>

        <Dialog v-model:visible="addDialog" :style="{ width: '450px' }" :header="headerTitle" :modal="true" :closable="false" >
            <div class="flex flex-col gap-6">
                <div>
                    <label for="desakel" class="block font-bold">Nomor TPS</label>
                    <Select id="desakel" v-model="tpsSelected" :options="datatps" optionLabel="label" placeholder="Pilih Nomor TPS" required="true" @blur="checkDesa" @change="checkDesa" :invalid="errorDesa.length > 0" fluid :disabled="submitted || form.type === 'update'"></Select>
                </div>
                <div class="-mt-10" v-if="errorDesa.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorDesa }}</Message>
                </div>
                <div v-if="jumlahDPT">
                    <label for="jml_dpt">
                        Jumlah DPT : {{ jumlahDPT }} || Jumlah Input Data : {{ formatNumber(rekapInputan) }} <i class="pi pi-verified" style="color: green" v-if="jumlahDPT.toString() === rekapInputan.toString()"></i>
                    </label>
                </div>
                <div class="grid grid-cols-1">
                    <div class="mb-5" v-for="(psl, p) in paslons">
                        <label for="name" class="block font-bold">{{ psl.nama_paslon }}</label>
                        <InputNumber :id="`name_${psl.uuid_paslon}`" v-model="dataPaslon[p].point" required="true" :min="0" :max="100000" placeholder="0" fluid @blur="sum_suara_masuk" :disabled="submitted" />
                    </div>
                    <div class="mb-10">
                        <label for="name" class="block font-bold">Suara Tidak Sah</label>
                        <InputNumber id="invalid" v-model="form.voteInvalid" required="true" :min="0" :max="100000" placeholder="0" fluid @blur="sum_suara_masuk" :disabled="submitted" />
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