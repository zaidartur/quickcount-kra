<script setup>
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Textarea from 'primevue/textarea';
import Dialog from 'primevue/dialog';
import FileUpload from 'primevue/fileupload';
import Message from 'primevue/message';
import Image from 'primevue/image';

const cfg = defineProps({
    app: Object,
})
const dataConfig = ref(new Array())

const initData = () => {
    dataConfig.value = [cfg.app]
}

initData()
const toast = useToast();
const dialogConfig = ref(false)
const errorFile = ref('')
const src = ref(null)
const submitted = ref(false)
const form = useForm({
    id: '',
    name: '',
    pemilik: '',
    tahun: '',
    website: '',
    alamat: '',
    logo: null,
})

const editConfig = (props) => {
    form.id         = props.id
    form.name       = props.app_name
    form.pemilik    = props.app_pemilik
    form.tahun      = props.app_tahun
    form.website    = props.app_website
    form.alamat     = props.app_address
    form.logo       = null

    dialogConfig.value = true
}

const checkFile = () => {
    src.value = null
    if (form.logo) {
        const filename = form.logo.name
        const filesize = form.logo.size
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

const upadateConfig = () => {
    submitted.value = true
    if (form.id && form.name && form.pemilik && form.tahun) {
        // console.log(form)
        form.post('/setting/update-config', {
            resetOnSuccess: true,
            onSuccess: (res) => {
                const messages = res.props.flash.message
                initData()
                alert_response(messages)
                dialogConfig.value = false
                submitted.value = false
            },
            onError: () => {
                toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Input data tidak sesuai', life: 3000 });
                submitted.value = false
            }
        })
    } else {
        toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Mohon untuk mengisi semua inputan', life: 3000 });
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
    <div class="mb-10">
        <!-- <Card class="mb-5 text-center">
            <template #content>
                <h3>Setting Aplikasi</h3>
            </template>
        </Card> -->

        <h4>Setting Aplikasi</h4>
        <div class="card">
            <DataTable
                ref="dt"
                :value="dataConfig"
                dataKey="id"
                :paginator="false"
                :order="false"
            >
                <Column header="Nama Aplikasi" style="min-width: 12rem">
                    <template #body="slotProps">
                        <div class="flex items-center">
                            <img v-if="slotProps.data.app_logo" :src="`${slotProps.data?.app_logo}`" :alt="slotProps.data.app_logo" class="rounded" style="width: 64px" />
                            <span><b>{{ slotProps.data.app_name }}</b></span>
                        </div>
                    </template>
                </Column>
                <Column field="app_pemilik" header="Pemilik" style="min-width: 16rem"></Column>
                <Column field="app_tahun" header="Tahun" style="min-width: 16rem"></Column>
                <Column field="app_website" header="Website" style="min-width: 16rem"></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editConfig(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="dialogConfig" :style="{ width: '450px' }" header="Setting Aplikasi" :modal="true">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-3">Nama Aplikasi</label>
                    <InputText id="name" v-model="form.name" required="true" autofocus :invalid="submitted && !form.name" fluid />
                    <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="pemilik" class="block font-bold mb-3">Pemilik</label>
                    <InputText id="pemilik" v-model="form.pemilik" required="true" :invalid="submitted && !form.pemilik" fluid />
                    <small v-if="submitted && !form.pemilik" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="tahun" class="block font-bold mb-3">Tahun</label>
                    <InputText type="number" id="tahun" v-model="form.tahun" required="true" :invalid="submitted && !from.tahun" fluid />
                    <small v-if="submitted && !form.tahun" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="website" class="block font-bold mb-3">Alamat Website</label>
                    <InputText type="url" id="website" v-model="form.website" :invalid="submitted && !form.website" fluid />
                    <small v-if="submitted && !form.website" class="text-red-500">Name is required.</small>
                </div>
                <div>
                    <label for="alamat" class="block font-bold mb-3">Alamat</label>
                    <Textarea id="alamat" v-model="form.alamat" rows="3" cols="20" fluid />
                </div>
                <div class="items-center justify-items-center gap-4">
                    <label for="file_logo" class="block font-bold mb-3">Logo Aplikasi</label>
                    <FileUpload 
                        choose-label="Pilih file logo"
                        name="file_logo[]" 
                        mode="basic"
                        v-model="form.logo" 
                        @input="form.logo = $event.target.files[0]"
                        @change="checkFile"
                        @select="onFileSelect"
                        accept=".jpg,.jpeg,.png,.gif" 
                        :maxFileSize="2097152" 
                        :disabled="submitted"
                        customUpload
                    />
                </div>
                <div class="flex flex-col items-center justify-items-center gap-4">
                    <img v-if="src" :src="src" alt="Logo" class="shadow-md rounded-xl w-full sm:w-64 " />
                </div>
                <div class="flex items-center gap-4 mb-4 -mt-4" v-if="errorFile">
                    <Message severity="error" class="">{{ errorFile }}</Message>
                </div>
                <div class="mb-5">&nbsp;</div>
            </div>

            <template #footer>
                <Button label="Batal" severity="danger" icon="pi pi-times" text @click="dialogConfig = false" :disabled="submitted" />
                <Button label="Update" icon="pi pi-save" @click="upadateConfig" :disabled="submitted" />
            </template>
        </Dialog>
    </div>
</template>