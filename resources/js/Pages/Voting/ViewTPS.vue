<script setup>
import { defineProps, onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { io } from 'socket.io-client';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import Select from 'primevue/select';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Password from 'primevue/password';
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
    role: Object,
    dpt: Number,
})
onMounted(() => {
    isMobile()
    window.addEventListener('resize', isMobile)
})

const kecamatan = ref(new Array())
const desas = ref(new Array())
const paslons = ref(new Array())
const votingPoint = ref(new Array())

// const socket = io('http://localhost:3000', {
//     withCredentials: true,
// })
const socket = io('https://qcws.caturnus.com', {
    withCredentials: true,
})

const initData = () => {
    kecamatan.value = []
    paslons.value = []
    desas.value = []
    let calon = []

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
    if (datas.paslon) {
        datas.paslon.map((ps, p) => {
            paslons.value.push(ps)
            calon.push({
                uuid: ps.uuid_paslon,
                name: ps.nama_paslon,
                point: 0,
            })
        })
    }
    if (datas.mydata.length > 0) {
        calon = []
        if (auth.level === 4) {
            const vote = JSON.parse(datas.mydata[0].vote_sah)
            calon = vote
        }
    } else {
        calon = []
        datas.paslon.map((ps) => {
            calon.push({
                uuid: ps.uuid_paslon,
                name: ps.nama_paslon,
                point: 0,
            })
        })
    }
    votingPoint.value = calon
    // console.log(paslons.value, JSON.parse(datas.mydata[0].vote_sah))
}

initData()
const kecamatanSelected = ref(new Array())
const desaSelected = ref(new Array())
const confirmDialog = ref(false)
const submitted = ref(false)
const myPassword = ref(null)
const detectMobile = ref(false)
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
    voteInvalid: null,
    totalVote: null,
    tahun: null,
    user: null,
    type: null
})

const sendingSocket = (datas) => {
    // console.log('sending-socket', datas)
    socket.emit('sending-paslon', datas)
}

const updateSocket = (datas) => {
    // console.log('update-socket', datas)
    socket.emit('updating-paslon', datas)
}

const findKecamatan = (val) => {
    let res = null
    const kec = (val.length < 2 ? ('0'+val) : val)
    kecamatan.value.some((kc) => {
        if (kc.value === kec) {
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

const myKecamatan = () => {
    let isKec = null
    if (auth.level === 2) {
        isKec = (auth.kode.length < 2 ? ('0'+auth.kode.toString()) : auth.kode.toString())
    } else if (auth.level === 3) {
        const textKec = auth.kode.toString()
        isKec = textKec.substr(2,2)
    }

    let res = []
    kecamatan.value.map((mk) => {
        if (mk.value === isKec) {
            kecamatanSelected.value = {
                label: mk.label,
                value: mk.value,
            }
        }
    })
}
const myDesa = () => {
    desaSelected.value = {}
    if (auth.level === 3) {
        desas.value.map((ds) => {
            if (parseInt(ds.value) === auth.kode) {
                desaSelected.value = {
                    label: ds.label,
                    value: ds.value
                }
            }
        })
    }
}
myKecamatan()
myDesa()

const isMobile = () => {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    // console.log('isMobile', check)
    detectMobile.value = check;
    return check;
}

const submitVoteDialog = () => {
    form.type = 'new'
    confirmDialog.value = true
}

const submitVoteDesa = () => {
    const initVote  = votingPoint.value
    const invalid   = invalidVote.value
    const kode      = auth.kode.toString()
    const no_tps    = datas.role.text.split(', ')[0]
    form.dpt        = kode.split('-')[1]
    form.kec        = kode.substr(2,2)
    form.desa       = kode.split('-')[0]
    form.desaName   = datas.role.text.split(', ')[1]
    form.tps        = datas.role.text.split(', ')[0]
    form.voteValid  = JSON.stringify(votingPoint.value)
    form.voteInvalid = invalidVote.value
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'new'

    // console.log(form)
    submitted.value = true
    form.post('/suara-masuk/tambah-data', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            initVote.map((iv) => {
                const datavote = {
                    uuid: iv.uuid,
                    kec: kode.substr(2,2),
                    desa: kode.split('-')[0],
                    tps: no_tps,
                    vote: iv.point,
                }
                // console.log('data', datavote)
                sendingSocket(datavote)
            })
            if (invalid && invalid > 0) {
                const datas = {
                    uuid: 'invalid',
                    kec: kode.substr(2,2),
                    desa: kode.split('-')[0],
                    tps: no_tps,
                    vote: invalid,
                }
                sendingSocket(datas)
                // console.log('invalid', datas)
            }
            inputStatus.value = true
            confirmDialog.value = false
            submitted.value = false
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
            submitted.value = false
        }
    })
}

const totalVote = () => {
    let point = 0
    votingPoint.value.map((vp) => {
        point = point+parseInt(vp.point)
    })
    return point+parseInt(invalidVote.value)
}

const checkPwd = async() => {
    submitted.value = true
    if (myPassword.value) {
        await axios.get('/suara-masuk/cek-pwd/' + btoa(myPassword.value)).then((res) => {
            if (res.data.status === 200) {
                inputStatus.value = false
                submitted.value = false
                confirmDialog.value = false
                form.type = 'update'
                editLabel.value.label = 'Update'
                editLabel.value.icon = 'pi pi-save'
            } else if (res.data.status === 401) {
                inputStatus.value = true
                submitted.value = false
                editLabel.value.label = 'Edit Data'
                editLabel.value.icon = 'pi pi-pencil'
                myPassword.value = ''
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Password tidak sesuai', life: 3000 });
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon masukkan password Anda', life: 3000 });
        submitted.value = false
    }
}

const cancelUpdateDesa = () => {
    inputStatus.value = true
    editLabel.value.label = 'Edit Data'
    editLabel.value.icon = 'pi pi-pencil'
    votingPoint.value = JSON.parse(datas.mydata[0].vote_sah)
    invalidVote.value = datas.mydata[0].vote_tidaksah
    sum_suara_masuk()
    form.type = 'edit'
}

const checkDiffVote = (newest) => {
    const def = JSON.parse(datas.mydata[0].vote_sah)
    const kode = auth.kode.toString()
    const no_tps = datas.role.text.split(', ')[0]
    def.map((d, i) => {
        if (d.point === newest[i].point) {
            console.log('same')
        } else {
            const diff = parseInt(newest[i].point) - parseInt(d.point)
            const data = {
                uuid: d.uuid,
                kec: kode.substr(2,2),
                desa: kode.split('-')[0],
                tps: no_tps,
                vote: diff,
            }
            updateSocket(data)
        }
    })
}

const checkInvalidDiff = () => {
    const old  = parseInt(datas.mydata[0].vote_tidaksah)
    const diff = parseInt(invalidVote.value) - old
    const kode = auth.kode.toString()
    const no_tps = datas.role.text.split(', ')[0]
    if (diff !== 0) {
        const data = {
            uuid: 'invalid',
            kec: kode.substr(2,2),
            desa: kode.split('-')[0],
            tps: no_tps,
            vote: diff,
        }
        updateSocket(data)
    }
}

const updateVoteDialog = (type) => {
    form.type = type
    myPassword.value = null
    confirmDialog.value = true
}

const updateVoteDesa = () => {
    submitted.value = true
    const newVote   = votingPoint.value
    const kode      = auth.kode.toString()
    form.id         = datas.mydata[0]?.id
    form.uuid       = datas.mydata[0]?.uuid_vote
    form.dpt        = kode.split('-')[1]
    form.kec        = kode.substr(2,2)
    form.desa       = kode.split('-')[0]
    form.desaName   = datas.role.text.split(', ')[1]
    form.tps        = datas.role.text.split(', ')[0]
    form.voteValid  = JSON.stringify(votingPoint.value)
    form.voteInvalid = invalidVote.value
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'update'

    // console.log('update', form)
    checkDiffVote(newVote)
    checkInvalidDiff()
    form.post('/suara-masuk/tambah-data', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            inputStatus.value = true
            confirmDialog.value = false
            submitted.value = false
            form.type = 'edit'
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

const formatNumber = (num) => {
    const value = parseInt(num)
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
}

const sum_suara_masuk = async() => {
    rekapInputan.value = 0
    const datas = await votingPoint.value
    // console.log('vp', datas)
    datas.map((dp) => {
        if (dp.point >= 0) {
            const res = rekapInputan.value + parseInt(dp.point)
            rekapInputan.value = res
        } else {
            const res = rekapInputan.value
            rekapInputan.value = res
        }
    })
    const _total = rekapInputan.value + parseInt(invalidVote.value)
    rekapInputan.value = _total
}
</script>

<template>
    <Head title="Suara Masuk" />

    <div>
        <h3 class="mb-5" v-if="!detectMobile">Input Suara Masuk TPS {{ datas.role.text }}</h3>
        <h3 class="mb-5 text-center" v-if="detectMobile">Input Suara Masuk<br>TPS {{ datas.role.text }}</h3>
        <h5 v-if="!detectMobile">Jumlah DPT : {{ formatNumber(datas.dpt) }} || Jumlah Suara Masuk : {{ formatNumber(rekapInputan) }} <i class="pi pi-verified" style="color: green" v-if="datas.dpt.toString() === rekapInputan.toString()"></i></h5>
        <h5 v-if="detectMobile" class="text-center">Jumlah DPT : {{ formatNumber(datas.dpt) }} <br>Jumlah Suara Masuk : {{ formatNumber(rekapInputan) }} <i class="pi pi-verified" style="color: green" v-if="datas.dpt.toString() === rekapInputan.toString()"></i></h5>

        <!-- <div class="flex flex-col md:flex-row md:w-8/12 w-full mb-5" v-if="auth.level === 7">
            <label for="kecamatan" class="md:w-5/12">Kecamatan</label>
            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-map-marker"></i>
                </InputGroupAddon>
                <Select id="kecamatan" v-model="kecamatanSelected" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" required="true" fluid :disabled="true"></Select>
            </InputGroup>
        </div>
        <div class="flex flex-col md:flex-row md:w-8/12 w-full mb-5" v-if="auth.level === 7">
            <label for="desa" class="md:w-5/12">Desa/Kelurahan</label>
            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-map-marker"></i>
                </InputGroupAddon>
                <Select id="desa" v-model="desaSelected" :options="desas" optionLabel="label" placeholder="Pilih Desa" required="true" fluid :disabled="true"></Select>
            </InputGroup>
        </div> -->

        <div v-if="auth.level === 4 && !detectMobile">
            <div class="w-full gap-6">
                <div class="mb-5" v-for="(psl, idx) in paslons">
                    <h5 class="md:w-6/12 -mb-0">{{ psl.nama_paslon }}</h5>
                    <IconField class="md:w-10/12">
                        <InputIcon class="pi pi-envelope" />
                        <InputNumber placeholder="Jumlah voting" v-model="votingPoint[idx].point" class="md:w-6/12" :autofocus="idx === 0 && !inputStatus" @blur="sum_suara_masuk" :disabled="inputStatus" />
                    </IconField>
                </div>
                <div class="mb-5">
                    <h5 class="md:w-6/12 -mb-0 text-red-500">Suara Tidak Sah</h5>
                    <IconField class="md:w-10/12">
                        <InputIcon class="pi pi-envelope" />
                        <InputNumber placeholder="Jumlah voting" v-model="invalidVote" class="md:w-6/12" invalid @blur="sum_suara_masuk" :disabled="inputStatus" />
                    </IconField>
                </div>
            </div>
            <div class="">
                <Button label="Simpan" icon="pi pi-save" @click="submitVoteDialog" :disabled="submitted" v-if="datas.mydata.length < 1" />
                <!-- <Button :label="editLabel.label" :icon="editLabel.icon" @click="updateVoteDialog" :disabled="submitted" v-if="datas.mydata.length > 0" /> -->
                    <Button label="Edit Data" icon="pi pi-pencil" @click="updateVoteDialog('edit')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type !== 'update'" />
                        <Button label="Update Data" icon="pi pi-save" @click="updateVoteDialog('update')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type === 'update'" />
                <Button label="Batalkan" icon="pi pi-times" class="ml-5" severity="warn" @click="cancelUpdateDesa" :disabled="submitted" v-if="(datas.mydata.length > 0 && !inputStatus)" />
            </div>
        </div>
        <div v-if="auth.level === 4 && detectMobile">
            <div class="w-full gap-6">
                <div class="mb-5" v-for="(psl, idx) in paslons">
                    <h5 class="md:w-6/12 -mb-0">{{ psl.nama_paslon }}</h5>
                    <IconField class="md:w-10/12">
                        <InputIcon class="pi pi-envelope" />
                        <InputNumber placeholder="Jumlah voting" v-model="votingPoint[idx].point" fluid class="md:w-6/12" :autofocus="idx === 0 && !inputStatus" @blur="sum_suara_masuk" inputStyle="height:55px; font-size: 24px; font-weight: bold;" :disabled="inputStatus" />
                    </IconField>
                </div>
                <div class="mb-5">
                    <h5 class="md:w-6/12 -mb-0 text-red-500">Suara Tidak Sah</h5>
                    <IconField class="md:w-10/12">
                        <InputIcon class="pi pi-envelope" />
                        <InputNumber placeholder="Jumlah voting" v-model="invalidVote" fluid class="md:w-6/12" invalid @blur="sum_suara_masuk" inputStyle="height:55px; font-size: 24px; font-weight: bold;" :disabled="inputStatus" />
                    </IconField>
                </div>
            </div>
            <div class="mt-10 flex flex-row w-full justify-between py-3">
                <Button label="Simpan" icon="pi pi-save" @click="submitVoteDialog" :disabled="submitted" v-if="datas.mydata.length < 1" size="large" class="w-5/12" />
                <Button label="Edit Data" icon="pi pi-pencil" @click="updateVoteDialog('edit')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type !== 'update'" size="large" class="w-5/12" />
                <Button label="Update Data" icon="pi pi-save" @click="updateVoteDialog('update')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type === 'update'" size="large" class="w-5/12" />
                <Button label="Batalkan" icon="pi pi-times" class="w-5/12" severity="danger" @click="cancelUpdateDesa" :disabled="submitted" v-if="(datas.mydata.length > 0 && !inputStatus)" size="large" />
            </div>
        </div>


        <Dialog v-model:visible="confirmDialog" :style="{ width: '450px' }" header="Konfirmasi" :modal="true" :closable="false" >
            <div class="flex items-center gap-4" v-if="form.type === 'new' || (form.type === 'update' || !inputStatus)">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Anda sudah yakin dengan data yang Anda input?</span
                >
            </div>
            <div class="w-full justify-items-center items-center text-center align-center mt-5 mb-5" v-if="form.type === 'edit' && inputStatus">
                <i class="pi pi-lock !text-3xl w-full text-center mb-5" />
                <label class="w-full">Masukkan Password Anda untuk konfirmasi :</label>
                <Password placeholder="Password Anda" v-model="myPassword" :autofocus="form.type === 'edit' && inputStatus" :toggleMask="true" :feedback="false" fluid class="text-center mt-3 w-[22em]" v-on:keyup.enter="checkPwd" :disabled="submitted" />
                <!-- <InputGroup class="w-full justify-items-center mt-5">
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password placeholder="Password Anda" v-model="myPassword" :autofocus="form.type === 'edit' && inputStatus" :toggleMask="true" :feedback="false" fluid class="md:w-8/12" :disabled="submitted" />
                </InputGroup> -->
            </div>
            <template #footer>
                <Button label="Tutup" icon="pi pi-times" text @click="confirmDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="submitVoteDesa" :disabled="submitted" v-if="form.type === 'new'" />
                <Button label="Ya, Konfirmasi Update" icon="pi pi-check" @click="updateVoteDesa" :disabled="submitted" v-if="!inputStatus && form.type === 'update'" />
                <Button label="Konfirmasi" icon="pi pi-key" @click="checkPwd" :disabled="submitted" v-if="form.type === 'edit' && inputStatus" />
            </template>
        </Dialog>
    </div>
</template>