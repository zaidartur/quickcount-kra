<script setup>
import { defineProps, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { io } from 'socket.io-client';
import { useToast } from 'primevue/usetoast';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import Select from 'primevue/select';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Button from 'primevue/button';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()
const datas = defineProps({
    users: Object,
    kec: Object,
    desa: Object,
    paslon: Object
})

const kecamatan = ref(new Array())
const desas = ref(new Array())
const paslons = ref(new Array())

const socket = io('http://localhost:3000', {
    withCredentials: true,
})

const initData = () => {
    kecamatan.value = []
    paslons.value = []
    desas.value = []

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
        datas.paslon.map((ps) => {
            paslons.value.push(ps)
        })
    }
}

initData()
const kecamatanSelected = ref(new Array())
const desaSelected = ref(new Array())
const roleLabel = ['Super Admin', 'Admin', 'Admin Kecamatan', 'Admin Desa']
const form = useForm({
    id: null,
    uuid: null,
    kec: null,
    desa: null,
    desaName: null,
    voteValid: null,
    voteInvalid: null,
    tahun: null,
    user: null,
})

socket.on('connection', (socket) => {
    //
})

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
console.log(datas.paslon)


</script>

<template>
    <Head title="Suara Masuk" />

    <div>
        <h3>Input suara masuk {{ auth.level === 2 ? 'Kecamatan' : 'Desa/Kel.' }} {{ auth.level === 2 ? findKecamatan(auth.kode) : (auth.level === 3 ? (findDesa(auth.kode)+', '+findKecamatan(auth.kode.substr(2,2))) : '') }}</h3>

        <div class="flex flex-col md:flex-row md:w-8/12 w-full mb-5">
            <label for="kecamatan" class="md:w-5/12">Kecamatan</label>
            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-map-marker"></i>
                </InputGroupAddon>
                <Select id="kecamatan" v-model="kecamatanSelected" :options="kecamatan" optionLabel="label" placeholder="Pilih Kecamatan" required="true" fluid :disabled="true"></Select>
            </InputGroup>
        </div>
        <div class="flex flex-col md:flex-row md:w-8/12 w-full mb-5" v-if="auth.level === 3">
            <label for="desa" class="md:w-5/12">Desa/Kelurahan</label>
            <InputGroup>
                <InputGroupAddon>
                    <i class="pi pi-map-marker"></i>
                </InputGroupAddon>
                <Select id="desa" v-model="desaSelected" :options="desas" optionLabel="label" placeholder="Pilih Desa" required="true" fluid :disabled="true"></Select>
            </InputGroup>
        </div>
        <div v-if="auth.level === 3">
            <div class="mb-5" v-for="psl in paslons">
                <h5 class="md:w-6/12 -mb-0">{{ psl.nama_paslon }}</h5>
                <IconField class="md:w-10/12">
                    <InputIcon class="pi pi-envelope" />
                    <InputText type="number" placeholder="Jumlah voting" class="md:w-5/12" />
                </IconField>
            </div>
        </div>
        <div class="">
            <Button label="Simpan" icon="pi pi-save" @click="" :disabled="false" />
            <Button label="Clear" icon="pi pi-save" @click="" :disabled="false" />
        </div>
    </div>
</template>