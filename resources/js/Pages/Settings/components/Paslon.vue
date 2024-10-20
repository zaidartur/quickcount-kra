<script setup>
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, defineProps } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Image from 'primevue/image';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Panel from 'primevue/panel';
import FileUpload from 'primevue/fileupload';
import Message from 'primevue/message';
import Skeleton from 'primevue/skeleton';

const paslon = defineProps({
    lists: Object,
})
const dataPaslon = ref(new Array())
const initData = () => {
    dataPaslon.value = []
    if (paslon.lists) {
        paslon.lists.map((ps) => {
            dataPaslon.value.push(ps)
        })
    }
}
initData()
const page = usePage().props
const toast = useToast()
const paslonDialog = ref(false)
const deleteDialog = ref(false)
const progressSpinner = ref(false)
const errorName = ref('')
const errorNomor = ref('')
const errorTahun = ref('')
const errorFile = ref('')
const src = ref(null)
const singlePaslon = ref(null)
const submitted = ref(false)
const form = useForm({
    id: '',
    urut: '',
    name: '',
    foto: null,
    tahun: '',
    type: '',
    foto_status: '',
})
// console.log('a', page)

const resetError = () => {
    errorName.value = ''
    errorNomor.value = ''
    errorTahun.value = ''
    errorFile.value = ''
}

const reset_form = () => {
    form.id     = ''
    form.urut   = ''
    form.name   = ''
    form.foto   = null
    form.tahun  = ''
    form.type   = ''
    form.foto_status = ''
}

const new_paslon = () => {
    reset_form()
    form.type = 'new'
    src.value = null
    resetError()
    paslonDialog.value = true
    console.log(form)
}

const edit_paslon = (props) => {
    form.reset()
    form.id     = props.uuid_paslon
    form.urut   = props.no_urut
    form.name   = props.nama_paslon
    form.foto   = props.foto_paslon
    form.tahun  = props.tahun
    form.type   = 'edit'
    form.foto_status = 'old'
    src.value   = props.foto_paslon

    paslonDialog.value = true
}

const checkName = () => {
    if (form.name) {
        errorName.value = ''
        return true
    } else {
        errorName.value  = 'Nama Pasangan Calon harus di isi'
        return false
    }
}

const checkNomor = () => {
    if (form.urut) {
        if (form.urut > 0 && form.urut < 10) {
            errorNomor.value = ''
            return true
        } else {
            errorNomor.value = 'Format Nomor Urut tidak sesuai'
            return false
        }
    } else {
        errorNomor.value = 'Nomor Ururt harus di isi'
        return false
    }
}

const checkTahun = () => {
    if (form.tahun) {
        if (form.tahun > 2000 && form.tahun < 3000) {
            errorTahun.value = ''
            return true
        } else {
            errorTahun.value = 'Format Tahun tidak sesuai'
            return false
        }
    } else {
        errorTahun.value = 'Tahun harus di isi'
        return false
    }
}

const checkFile = () => {
    // src.value = null
    if (form.foto) {
        if (form.foto_status === 'old') {
            errorFile.value = ''
            return true
        } else {
            const filename = form.foto.name
            const filesize = form.foto.size
            const ext = filename.substring(filename.lastIndexOf('.')+1, filename.length) || filename
            
            if ((ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif') && filesize < 2097152) {
                errorFile.value = ''
                return true
            } else {
                errorFile.value = 'File harus di isi dengan format jpg/jpeg/png dan maksimal 2 Mb'
                form.logo = null
                return false
            }
        }
    } else {
        errorFile.value = 'File harus di isi dengan format "jpg/jpeg/png" dan maksimal 2 Mb'
        form.logo = null
        return false
    }
}

const onFileSelect = (event) => {
    progressSpinner.value = true
    if (checkFile()) {
        if (form.type === 'edit') {
            form.foto_status = 'new'
        } else {
            form.foto_status = ''
        }

        const file = event.files[0]
        const reader = new FileReader()

        reader.onload = async (e) => {
            src.value = e.target.result
        };

        progressSpinner.value = false
        reader.readAsDataURL(file)
    } else {
        progressSpinner.value = false
    }
}
console.log(window.location.pathname)

const save_paslon = () => {
    submitted.value = true
    if (checkName() && checkNomor() && checkTahun() && checkFile()) {
        console.log(form)
        form.post('/setting/tambah-paslon', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                paslonDialog.value = false
                submitted.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        checkName()
        checkNomor()
        checkTahun()
        checkFile()
        submitted.value = false
    }
}

const drop_alert = (prop) => {
    singlePaslon.value = prop
    deleteDialog.value = true
}

const delete_paslon = () => {
    submitted.value = true
    reset_form()
    if (singlePaslon.value) {
        form.id = singlePaslon.value.uuid_paslon
        form.post('/setting/hapus-paslon', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                deleteDialog.value = false
                singlePaslon.value = null
                submitted.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Data tidak dikenali', life: 3000 });
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
    <div>
        <!-- <Card class="mb-5 text-center">
            <template #content>
                <h3>Setting Pasngan Calon</h3>
            </template>
        </Card> -->
        <h4>Setting Pasangan Calon</h4>
        <div class="">
            <div class="col-span-12 text-center">
                <Button type="button" icon="pi pi-plus" label="Tambah Data" class="mb-6" @click="new_paslon"></Button>
            </div>

            <div class="grid grid-cols-12 gap-4 justify-items-center">
                <div :class="dataPaslon.length > 2 ? 'col-span-4' : 'col-span-6'" v-for="dp in dataPaslon">
                    <Card style="width: 25rem; overflow: hidden" v-animateonscroll="{ enterClass: 'animate-fadein', leaveClass: 'animate-fadeout' }">
                        <template #header>
                            <img alt="user header" :src="dp?.foto_paslon" />
                        </template>
                        <template #title>
                            <div class="w-full text-center">{{ dp?.no_urut }}</div>
                        </template>
                        <template #content>
                            <div class="w-full text-center mb-20">
                                <h2>{{ dp?.nama_paslon }}</h2>
                            </div>
                        </template>
                        <template #footer>
                            <div class="flex gap-4 mt-1">
                                <Button label="Hapus" severity="danger" icon="pi pi-trash" outlined class="w-full" @click="drop_alert(dp)" />
                                <Button label="Edit" severity="warn" icon="pi pi-pencil" class="w-full" @click="edit_paslon(dp)" />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>

        </div>

        <Dialog v-model:visible="paslonDialog" :style="{ width: '450px' }" header="Tambah Data" :modal="true" :closable="false">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-3">Nama Pasangan Calon</label>
                    <InputText id="name" v-model="form.name" required="true" autofocus :invalid="errorName.length > 0" @change="checkName" fluid />
                    <small v-if="errorName" class="text-red-500">{{ errorName }}</small>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label for="urut" class="block font-bold mb-3">Nomor Urut</label>
                        <InputText type="number" id="urut" v-model="form.urut" required="true" :invalid="errorNomor.length > 0" @change="checkNomor" fluid />
                        <small v-if="errorNomor" class="text-red-500">{{ errorNomor }}</small>
                    </div>
                    <div class="col-span-6">
                        <label for="tahun" class="block font-bold mb-3">Tahun</label>
                        <InputText type="number" id="tahun" v-model="form.tahun" required="true" :invalid="errorTahun.length > 0" @change="checkTahun" fluid />
                        <small v-if="errorTahun" class="text-red-500">{{ errorTahun }}</small>
                    </div>
                </div>
                <div class="items-center justify-items-center gap-4">
                    <label for="file_foto" class="block font-bold mb-3">Foto Pasangan Calon</label>
                    <FileUpload 
                        choose-label="Pilih file foto"
                        name="file_foto[]" 
                        mode="basic"
                        v-model="form.foto" 
                        @input="form.foto = $event.target.files[0]"
                        @change="checkFile"
                        @select="onFileSelect"
                        accept=".jpg,.jpeg,.png,.gif" 
                        :maxFileSize="2097152" 
                        :disabled="submitted"
                        customUpload
                    />
                </div>
                <div class="flex items-center gap-4 mb-4 -mt-4" v-if="errorFile.length > 0">
                    <Message severity="error" class="text-center">{{ errorFile }}</Message>
                </div>
                <div class="flex flex-col items-center justify-items-center gap-4" v-if="progressSpinner">
                    <Skeleton width="10rem" height="12rem"></Skeleton>
                </div>
                <div class="flex flex-col items-center justify-items-center gap-4" v-if="!progressSpinner">
                    <img v-if="src" :src="src" alt="Paslon" class="shadow-md rounded-xl w-full sm:w-64 " />
                </div>
                <div class="mb-5">&nbsp;</div>
            </div>

            <template #footer>
                <Button label="Batal" severity="danger" icon="pi pi-times" text @click="paslonDialog = false" :disabled="submitted" />
                <Button label="Simpan" icon="pi pi-save" @click="save_paslon" :disabled="submitted" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteDialog" :style="{ width: '450px' }" header="Confirm" :modal="true" :closable="false">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="singlePaslon">Anda ingin menghapus data <b>{{ singlePaslon.nama_paslon }} ({{ singlePaslon.no_urut }})</b> ?</span>
            </div>
            <template #footer>
                <Button label="Tidak" icon="pi pi-times" severity="danger" text @click="deleteDialog = false" :disabled="submitted" />
                <Button label="Ya, Konfirmasi" icon="pi pi-check" @click="delete_paslon" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped lang="scss">
    .paslon_card {
        height: 379px;
        width: 300px;
        background: grey;
        border-radius: 10px;
        transition: background 0.8s;
        overflow: hidden;
        background: black;
        box-shadow: 0 70px 63px -60px #000000;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .card_image {
        // background: url('https://i.pinimg.com/736x/8f/a0/51/8fa051251f5ac2d0b756027089fbffde--terry-o-neill-al-pacino.jpg') center center no-repeat;
        background-size: 300px;

        &:hover {
            background: url('https://i.pinimg.com/736x/8f/a0/51/8fa051251f5ac2d0b756027089fbffde--terry-o-neill-al-pacino.jpg') left center no-repeat;
            // background: left center no-repeat;
            background-size: 600px;
        }

        h2 {
            opacity: 1;
        }

        .fa {
            opacity: 1;
        }

    }
</style>