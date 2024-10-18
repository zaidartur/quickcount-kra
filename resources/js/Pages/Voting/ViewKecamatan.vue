<script setup>
import { defineProps, ref } from 'vue';
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
const votingPoint = ref(new Array())

const socket = io('http://localhost:3000', {
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
        if (auth.level === 3) {
            const vote = JSON.parse(datas.mydata[0].vote_sah)
            calon = vote
        }
    } else {
        datas.paslon.map((ps) => {
            calon.push({
                uuid: ps.uuid_paslon,
                name: ps.nama_paslon,
                point: 0,
            })
        })
    }
    votingPoint.value = calon
}

initData()
const kecamatanSelected = ref(new Array())
const desaSelected = ref(new Array())
const confirmDialog = ref(false)
const submitted = ref(false)
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
    voteInvalid: null,
    totalVote: null,
    tahun: null,
    user: null,
    type: null
})

const sendingSocket = (datas) => {
    console.log('socket', datas)
    // uuid, kec, desa, vote(+-)
    // socket.on('connection', (sc) => {
    //     //
    // })
    socket.emit('sending-paslon', datas)
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

const myKecamatan = () => {
    let isKec = null
    if (auth.level === 2) {
        isKec = auth.kode
    } else if (auth.level === 3) {
        isKec = auth.kode.substr(2,2)
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
            if (ds.value === auth.kode) {
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

const submitVoteDialog = () => {
    form.type = 'new'
    confirmDialog.value = true
}

const submitVoteDesa = () => {
    const initVote  = votingPoint.value
    form.kec        = auth.kode.substr(2,2)
    form.desa       = auth.kode
    form.desaName   = desaSelected.value.label
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
                const data = {
                    uuid: iv.uuid,
                    kec: kecamatanSelected.value.value,
                    desa: desaSelected.value.value,
                    vote: iv.point,
                }
                sendingSocket(data)
            })
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
    return point
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
    form.type = 'edit'
}

const checkDiffVote = (newest) => {
    const def = JSON.parse(datas.mydata[0].vote_sah)
    def.map((d, i) => {
        if (d.point === newest[i].point) {
            console.log('same')
        } else {
            const diff = parseInt(newest[i].point) - parseInt(d.point)
            const data = {
                uuid: d.uuid,
                kec: kecamatanSelected.value.value,
                desa: desaSelected.value.value,
                vote: diff,
            }
            sendingSocket(data)
        }
    })
}

const updateVoteDialog = (type) => {
    form.type = type
    myPassword.value = null
    confirmDialog.value = true
}

const updateVoteDesa = () => {
    const newVote   = votingPoint.value
    form.id         = datas.mydata[0]?.id
    form.uuid       = datas.mydata[0]?.uuid_vote
    form.kec        = auth.kode.substr(2,2)
    form.desa       = auth.kode
    form.desaName   = desaSelected.value.label
    form.voteValid  = JSON.stringify(votingPoint.value)
    form.voteInvalid = invalidVote.value
    form.totalVote  = totalVote()
    form.user       = auth.uuid
    form.type       = 'update'
    checkDiffVote(newVote)

    console.log('update', form)
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

        <div v-if="auth.level === 3" class="w-full gap-6">
            <div class="mb-5" v-for="(psl, idx) in paslons">
                <h5 class="md:w-6/12 -mb-0">{{ psl.nama_paslon }}</h5>
                <IconField class="md:w-10/12">
                    <InputIcon class="pi pi-envelope" />
                    <InputText type="number" placeholder="Jumlah voting" v-model="votingPoint[idx].point" class="md:w-6/12" :autofocus="idx === 0 && !inputStatus" :disabled="inputStatus" />
                </IconField>
            </div>
            <div class="mb-5">
                <h5 class="md:w-6/12 -mb-0 text-red-500">Suara Tidak Sah</h5>
                <IconField class="md:w-10/12">
                    <InputIcon class="pi pi-envelope" />
                    <InputText type="number" placeholder="Jumlah voting" v-model="invalidVote" class="md:w-6/12" invalid :disabled="inputStatus" />
                </IconField>
            </div>
        </div>
        <div class="">
            <Button label="Simpan" icon="pi pi-save" @click="submitVoteDialog" :disabled="submitted" v-if="datas.mydata.length < 1" />
            <!-- <Button :label="editLabel.label" :icon="editLabel.icon" @click="updateVoteDialog" :disabled="submitted" v-if="datas.mydata.length > 0" /> -->
                <Button label="Edit Data" icon="pi pi-pencil" @click="updateVoteDialog('edit')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type !== 'update'" />
                    <Button label="Update Data" icon="pi pi-save" @click="updateVoteDialog('update')" :disabled="submitted" v-if="datas.mydata.length > 0 && form.type === 'update'" />
            <Button label="Batalkan" icon="pi pi-times" class="ml-5" severity="warn" outlined @click="cancelUpdateDesa" :disabled="submitted" v-if="(datas.mydata.length > 0 && !inputStatus)" />
        </div>


        <Dialog v-model:visible="confirmDialog" :style="{ width: '450px' }" header="Konfirmasi" :modal="true" :closable="false" >
            <div class="flex items-center gap-4" v-if="form.type === 'new' || (form.type === 'update' || !inputStatus)">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Anda sudah yakin dengan data yang Anda input?</span
                >
            </div>
            <div class="w-full justify-items-center items-center align-middle mt-5 mb-5" v-if="form.type === 'edit' && inputStatus">
                <label class="w-full ">Masukkan Password Anda untuk konfirmasi :</label>
                <InputGroup class="w-full justify-items-center">
                    <InputGroupAddon>
                        <i class="pi pi-lock"></i>
                    </InputGroupAddon>
                    <Password placeholder="Password Anda" v-model="myPassword" autofocus :toggleMask="true" :feedback="false" fluid class="md:w-10/12" :disabled="submitted" />
                </InputGroup>
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