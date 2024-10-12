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

const paslon = defineProps({
    lists: Object,
})
const dataPaslon = ref(new Array())
const initData = () => {
    if (paslon.lists) {
        paslon.lists.map((ps) => {
            dataPaslon.value.push(ps)
        })
    }
}
initData()
console.log('paslon', dataPaslon.value)
const page = usePage().props
const paslonDialog = ref(false)
const errorFile = ref('')
const src = ref(null)
const form = useForm({
    urut: '',
    name: '',
    foto: null,
    tahun: '',
})
console.log(page)
const checkFile = () => {
    src.value = null
    if (form.foto) {
        const filename = form.foto.name
        const filesize = form.foto.size
        const ext = filename.substring(filename.lastIndexOf('.')+1, filename.length) || filename
        
        if ((ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif') && filesize < 2097152) {
            errorFile.value = ''
            return true
        } else {
            form.logo = null
            // errorFile.value = 'Format file yang diperbolekan xlsx dan maksmimal 3 Mb'
            return false
        }
    }
}

const onFileSelect = (event) => {
    if (checkFile()) {
        const file = event.files[0]
        const reader = new FileReader()

        reader.onload = async (e) => {
            src.value = e.target.result
        };

        reader.readAsDataURL(file)
    }
}

const savePaslon = () => {
    //
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
                <Button type="button" icon="pi pi-plus" label="Tambah Data" class="mb-6" @click="paslonDialog = true"></Button>
            </div>

            <div class="grid grid-cols-12 gap-4 justify-items-center" v-for="dp in dataPaslon">
                <Card style="width: 25rem; overflow: hidden" v-animateonscroll="{ enterClass: 'animate-fadein', leaveClass: 'animate-fadeout' }" class="col-span-6">
                    <template #header>
                        <img alt="user header" :src="dp?.foto_paslon" />
                    </template>
                    <template #title>{{ dp?.no_urut }}</template>
                    <template #subtitle>{{ dp?.nama_paslon }}</template>
                    <template #content>
                        <p class="m-0">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Inventore sed consequuntur error repudiandae numquam deserunt quisquam repellat libero asperiores earum nam nobis, culpa ratione quam perferendis esse, cupiditate neque
                            quas!
                        </p>
                    </template>
                    <template #footer>
                        <div class="flex gap-4 mt-1">
                            <Button label="Hapus" severity="danger" icon="pi pi-trash" outlined class="w-full" />
                            <Button label="Edit" severity="warn" icon="pi pi-pencil" class="w-full" />
                        </div>
                    </template>
                </Card>
            </div>
        </div>

        <Dialog v-model:visible="paslonDialog" :style="{ width: '450px' }" header="Tambah Data" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-3">Nama Pasangan Calon</label>
                    <InputText id="name" v-model="form.name" required="true" autofocus :invalid="submitted && !form.name" fluid />
                    <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
                </div>
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-6">
                        <label for="urut" class="block font-bold mb-3">Nomor Urut</label>
                        <InputText type="number" id="urut" v-model="form.urut" required="true" :invalid="submitted && !form.urut" fluid />
                        <small v-if="submitted && !form.urut" class="text-red-500">Name is required.</small>
                    </div>
                    <div class="col-span-6">
                        <label for="tahun" class="block font-bold mb-3">Tahun</label>
                        <InputText type="number" id="tahun" v-model="form.tahun" required="true" :invalid="submitted && !from.tahun" fluid />
                        <small v-if="submitted && !form.tahun" class="text-red-500">Name is required.</small>
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
                <div class="flex flex-col items-center justify-items-center gap-4">
                    <img v-if="src" :src="src" alt="Paslon" class="shadow-md rounded-xl w-full sm:w-64 " />
                </div>
                <div class="flex items-center gap-4 mb-4 -mt-4" v-if="errorFile">
                    <Message severity="error" class="">{{ errorFile }}</Message>
                </div>
                <div class="mb-5">&nbsp;</div>
            </div>

            <template #footer>
                <Button label="Batal" severity="danger" icon="pi pi-times" text @click="paslonDialog = false" />
                <Button label="Simpan" icon="pi pi-save" @click="savePaslon" />
            </template>
        </Dialog>
    </div>
</template>