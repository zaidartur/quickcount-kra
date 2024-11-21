<script setup>
import { defineProps, ref, onMounted } from 'vue';
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
import Card from 'primevue/card';
import MeterGroup from 'primevue/metergroup';
import Divider from 'primevue/divider';

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
    sum_dpt: Number,
})

onMounted(() => {
    isMobile()
    window.addEventListener('resize', isMobile)
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
    // console.log('data', datas.mydata)
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
const datatps = ref([])
const tpsSelected = ref()
const confirmDialog = ref(false)
const addDialog = ref(false)
const submitted = ref(false)
const headerTitle = ref('Tambah Data')
const errorDesa = ref('')
const errorTPS = ref('')
const myPassword = ref(null)
const detailDialog = ref(false)
const detectMobile = ref(false)
const detailDesa = ref([])
const detailTerpilih = ref([])
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
const clr = ['#eab308', '#8b5cf6', '#3b82f6', '#f97316', '#f59e0b', '#10b981', '#14b8a6', '#84cc16']
const roleLabel = ['Super Admin', 'Admin', 'Admin Kecamatan', 'Admin Desa']
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

function expandDialog() {
    detailTerpilih.value = detailDesa.value.reduce((acc, p) => (acc[p.id] = true) && acc, {});
}

function collapseDialog() {
    detailTerpilih.value = null;
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
            if (ds.value === (auth.kode.length < 2 ? ('0'+auth.kode) : auth.kode)) {
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
    errorDesa.value = ''
    tpsSelected.value = {}
    errorTPS.value = ''
    initPaslon()
    form.voteInvalid = 0
    rekapInputan.value = 0
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
    if (desaSelected.value && desaSelected.value.value !== undefined) {
        errorDesa.value = ''
        jumlahDPT.value = ''
        get_data_tps()
        return true
    } else {
        errorDesa.value = 'Mohon untuk memilih desa/kelurahan terlebih dahulu'
        return false
    }
}

const check_tps = () => {
    if (tpsSelected.value && tpsSelected.value.value !== undefined) {
        errorTPS.value = ''
        jumlahDPT.value = tpsSelected.value.dpt
        return true
    } else {
        errorTPS.value = 'Mohon untuk memilih nomor TPS terlebih dahulu'
        return false
    }
}

const get_data_tps = async() => {
    if (desaSelected.value && desaSelected.value.value !== undefined) {
        axios.get('/data-user/get-tps/' + desaSelected.value.value).then((res) => {
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
                datatps.value = default_tps
            }
        })
    }
}

const submit = () => {
    const initVote  = dataPaslon.value
    const isInvalid = form.voteInvalid
    form.dpt        = tpsSelected.value.value
    form.kec        = (auth.kode.length < 2 ? ('0'+auth.kode) : auth.kode)
    form.desa       = desaSelected.value.value
    form.desaName   = desaSelected.value.label
    form.voteValid  = JSON.stringify(dataPaslon.value)
    // form.voteInvalid = invalidVote.value
    form.tps        = tpsSelected.value.label
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'new'

    // console.log(tpsSelected.value, form)
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
                        kec: (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString()),
                        desa: desaSelected.value.value,
                        tps: tpsSelected.value.value,
                        vote: iv.point,
                    }
                    sendingSocket(data)
                })
                if (isInvalid > 0) {
                    const data = {
                        uuid: 'invalid',
                        kec: (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString()),
                        desa: desaSelected.value.value,
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
    if (checkDesa() || check_tps() || totalVote() > 0) {
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
    const _total = totalVote()
    form.id         = dataEdit.value.id
    form.uuid       = dataEdit.value.uuid_vote
    form.kec        = dataEdit.value.kec_id
    form.desa       = dataEdit.value.desakel_id
    form.desaName   = dataEdit.value.desakel_name
    form.voteValid  = JSON.stringify(dataEdit.value.valid)
    // form.voteValid  = dataEdit.value.valid
    form.voteInvalid = dataEdit.value.invalid
    form.totalVote  = _total
    form.user       = auth.uuid
    form.type       = 'update'

    invalidVote.value = dataEdit.value.invalid
    votingPoint.value = JSON.stringify(dataEdit.value.valid)
    rekapInputan.value = _total

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
                kec: (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString()),
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
            kec: (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString()),
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

const detailVoteDialog = (props) => {
    headerTitle.value = 'Detail Suara Masuk di Desa ' + props.desakel_name
    // console.log('detail', props)
    detailDesa.value = props.tps
    detailDialog.value = true
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

const notps = (data) => {
    if (data < 10) {
        return `00${data}`
    } else if (data > 9 && data < 100) {
        return `0${data}`
    } else {
        return data
    }
}

const convert_meter = (data, invalid) => {
    if (data && data.length > 0) {
        let res = []
        data.map((d, i) => {
            res.push({
                label: d.name,
                color: clr[i],
                value: parseInt(d.point)
            })
        })
        res.push({
            label: 'Suara Tidak Sah',
            color: '#ef4444',
            value: parseInt(invalid)
        })
        return res
    } else {
        return []
    }
}

const max_meter = (data, invalid) => {
    if (data && data.length > 0) {
        let res = 0
        data.map((d, i) => {
            const sub = res + parseInt(d.point)
            res = sub
        })
        res = res + parseInt(invalid)
        return res
    } else {
        return 0
    }
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

const isMobile = () => {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    // console.log('isMobile', check)
    detectMobile.value = check;
    return check;
}
</script>

<template>
    <Head title="Suara Masuk" />

    <div>
        <h3 class="mb-5" v-if="!detectMobile">Input suara masuk {{ auth.level === 2 ? 'Kecamatan' : 'Desa/Kel.' }} {{ auth.level === 2 ? findKecamatan((auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString())) : (auth.level === 3 ? (findDesa(auth.kode.toString())+', '+findKecamatan(auth.kode.toString().substr(2,2))) : '') }}</h3>
        <h3 class="mb-5 text-center" v-if="detectMobile">Input suara masuk <br> {{ auth.level === 2 ? 'Kecamatan' : 'Desa/Kel.' }} {{ auth.level === 2 ? findKecamatan((auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString())) : (auth.level === 3 ? (findDesa(auth.kode.toString())+', '+findKecamatan(auth.kode.toString().substr(2,2))) : '') }}</h3>

        <!-- <div class="mb-5">
            <Button label="Tambah Data" icon="pi pi-plus-circle" @click="submitVoteDialog" />
        </div> -->
        <div class="card" v-if="!detectMobile">
            <div class="font-semibold text-xl mb-4">Jumlah Suara Masuk {{ formatNumber(voteTotal) }} dari {{ formatNumber(datas.sum_dpt) }} ({{ ((voteTotal / datas.sum_dpt) * 100).toFixed(1) }}%)</div>
            <DataTable v-model:expandedRows="expandedRows" :value="voteData" ref="dt" dataKey="full_id" tableStyle="min-width: 60rem">
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
                <Column field="desakel_name" header="Nama Desa/Kel"></Column>
                <Column field="" header="Suara Sah">
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.total - parseInt(slotProps.data.invalid)) }}
                    </template>
                </Column>
                <Column field="invalid" header="Suara Tidak Sah"></Column>
                <Column field="total" header="Total Suara">
                    <template #body="slotProps">
                        <b>
                            <Tag 
                                :value="formatNumber(slotProps.data.total) + ' Suara (' + ((slotProps.data.total / slotProps.data.dpt) * 100).toFixed(0) + '%)'" 
                                :severity="((slotProps.data.total / slotProps.data.dpt) * 100) === 100 ? 'success' : 'warn'" 
                            />
                        </b>
                    </template>
                </Column>
                <Column field="dpt" header="Jumlah DPT">
                    <template #body="slotProps">
                        <b>{{ formatNumber(slotProps.data.dpt) }}</b>
                    </template>
                </Column>
                <Column field="" header="TPS">
                    <template #body="slotProps">
                        <b>{{ (slotProps.data.tps.length) }} / {{ slotProps.data.tps_total }}</b>
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 3rem" header="Opsi">
                    <template #body="slotProps">
                        <Button icon="pi pi-info-circle" outlined rounded v-tooltip.bottom="'Detail Desa ' + slotProps.data.desakel_name" class="mr-2" severity="info" @click="detailVoteDialog(slotProps.data)" />
                        <!-- <Button icon="pi pi-pencil" outlined rounded v-tooltip.bottom="'Edit Data ' + slotProps.data.desakel_name" class="mr-2" severity="warn" @click="editVoteDialog(slotProps.data, 'edit')" /> -->
                    </template>
                </Column>
                <template #expansion="slotProps">
                    <div class="p-4 md:w-8/12">
                        <h5>Detail Desa/Kelurahan {{ slotProps.data.desakel_name }}</h5>
                        <DataTable :value="slotProps.data.valid">
                            <Column field="name" header="Urut Paslon"></Column>
                            <Column field="point" header="Jumlah Suara"></Column>
                            <Column field="" header="Persentase">
                                <template #body="slotProp">
                                    {{ ((slotProp.data.point / slotProps.data.total) * 100).toFixed(0) }}%
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </template>
            </DataTable>
        </div>

        <div v-if="detectMobile" class="w-full">
            <div class="font-semibold text-xl mb-4 text-center">Jumlah Suara Masuk <br> {{ formatNumber(voteTotal) }} dari {{ formatNumber(datas.sum_dpt) }} ({{ ((voteTotal / datas.sum_dpt) * 100).toFixed(1) }}%)</div>
            <div class="grid grid-rows-1 justify-items-center" v-for="vData in voteData">
                <Card style="width: 25rem; overflow: hidden" class="mb-5">
                    <template #title>Desa/Kelurahan {{ vData.desakel_name }}</template>
                    <template #subtitle>
                        Suara Masuk {{ formatNumber(vData.total) }} dari {{ formatNumber(vData.dpt) }} ({{ ((vData.total / vData.dpt) * 100).toFixed(0) }}%)
                        <br>
                        TPS Masuk {{ vData.tps.length }} dari {{ vData.tps_total }}
                    </template>
                    <template #content>
                        <!-- <div class="w-full" v-for="paslon in vData.valid">
                        </div> -->
                        <MeterGroup :value="convert_meter(vData.valid, vData.invalid)" :max="max_meter(vData.valid, vData.invalid)" />
                    </template>
                    <template #footer>
                        <div class="flex gap-4 mt-1">
                            <Button label="Detail" icon="pi pi-info-circle" class="w-full" @click="detailVoteDialog(vData)" />
                        </div>
                    </template>
                </Card>
            </div>
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
                <div>
                    <label for="tps" class="block font-bold">Nomor TPS</label>
                    <Select id="tps" v-model="tpsSelected" :options="datatps" optionLabel="label" placeholder="Pilih Nomor TPS" required="true" @blur="check_tps" @change="check_tps" :invalid="errorTPS.length > 0" fluid :disabled="submitted || form.type === 'update'"></Select>
                </div>
                <div class="-mt-10" v-if="errorTPS.length">
                    <label for="" class="font-semibold w-24">&nbsp;</label>
                    <Message severity="error" class="">{{ errorTPS }}</Message>
                </div>
                <div v-if="jumlahDPT">
                    <label for="jml_dpt">
                        Jumlah DPT : {{ jumlahDPT }} || Jumlah Input Data : {{ formatNumber(rekapInputan) }} <i class="pi pi-verified" style="color: green" v-if="jumlahDPT.toString() === rekapInputan.toString()"></i>
                    </label>
                </div>
                <div class="grid grid-cols-1">
                    <div class="mb-5" v-for="(psl, p) in paslons">
                        <label for="name" class="block font-bold">{{ psl.nama_paslon }}</label>
                        <InputNumber :id="`name_${psl.uuid_paslon}`" v-model="dataPaslon[p].point" required="true" :min="1" :max="100000" @blur="sum_suara_masuk" placeholder="0" fluid :disabled="submitted" />
                    </div>
                    <div class="mb-10">
                        <label for="name" class="block font-bold">Suara Tidak Sah</label>
                        <InputNumber id="invalid" v-model="form.voteInvalid" required="true" :min="1" :max="100000" placeholder="0" @blur="sum_suara_masuk" fluid :disabled="submitted" />
                    </div>
                </div>
            </div>
            <template #footer>
                <Button label="Batal" icon="pi pi-times" text @click="addDialog = false" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-check" @click="submitVoteDialog" :disabled="submitted" v-if="form.type === 'new'" />
                <Button label="Update" icon="pi pi-save" @click="updateVote" :disabled="submitted" v-if="form.type === 'update'" />
            </template>
        </Dialog>

        <Dialog v-model:visible="detailDialog" :style="{ width: detectMobile ? '90%' : '750px' }" :header="headerTitle" :modal="true" :closable="true" >
            <div v-if="!detectMobile">
                <DataTable v-model:expandedRows="detailTerpilih" :value="detailDesa" ref="dt" dataKey="id" scrollable scrollHeight="flex" tableStyle="max-width: 750px">
                    <template #header>
                        <div class="flex flex-wrap justify-start gap-2">
                            <Button text icon="pi pi-plus" label="Buka Semua" @click="expandDialog" />
                            <Button text icon="pi pi-minus" label="Tutup Semua" @click="collapseDialog" />
                        </div>
                    </template>
                    <Column expander style="width: 5rem" header="Detail" />
                    <Column field="no_tps" header="Nomor TPS">
                        <template #body="slotProps">
                            {{ notps(slotProps.data.no_tps) }}
                        </template>
                    </Column>
                    <Column field="" header="Suara Sah">
                        <template #body="slotProps">
                            {{ formatNumber(slotProps.data.total - parseInt(slotProps.data.invalid)) }}
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
                    <template #expansion="slotProps">
                        <div class="p-4 md:w-8/12">
                            <h6><u>Detail TPS  {{ notps(slotProps.data.no_tps) }}</u></h6>
                            <DataTable :value="slotProps.data.valid">
                                <Column field="name" header="Urut Paslon"></Column>
                                <Column field="point" header="Jumlah Suara"></Column>
                            </DataTable>
                        </div>
                    </template>
                </DataTable>
            </div>
            <div v-if="detectMobile">
                <div class="grid grid-rows-1 justify-items-center" v-for="isTps in detailDesa">
                    <Card style="width: 25rem; overflow: hidden" class="mb-2">
                        <template #title>Nomor TPS {{ notps(isTps.no_tps) }}</template>
                        <template #subtitle>
                            Suara Masuk {{ formatNumber(isTps.total) }} dari {{ formatNumber(isTps.dpt) }} ({{ ((isTps.total / isTps.dpt) * 100).toFixed(0) }}%)
                        </template>
                        <template #content>
                            <!-- <div class="w-full" v-for="paslon in isTps.valid">
                            </div> -->
                            <MeterGroup :value="convert_meter(isTps.valid, isTps.invalid)" :max="max_meter(isTps.valid, isTps.invalid)" />
                        </template>
                    </Card>
                    <Divider class="mb-2" />
                </div>
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" text @click="detailDialog = false" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>