<script setup>
import { defineProps, onMounted, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

const page  = usePage()
const message = page.props.flash.message
const auth = page.props.auth.user
const toast = useToast()

const datas = defineProps({
    detail: Object,
})

onMounted(() => {
    isMobile()
    window.addEventListener('resize', isMobile)
})

const detectMobile = ref(false)
const roleLabel = ref(null)
const submitted = ref(false)
const lform = useForm({})
const form = useForm({
    uuid: null,
    name: null,
})

const initData = () => {
    form.name = auth.name
}

initData()

const isMobile = () => {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    // console.log('isMobile', check)
    detectMobile.value = check;
    return check;
}

if (auth.level === 0) {
    roleLabel.value = 'Super Admin'
} else if (auth.level === 1) {
    roleLabel.value = 'Admin Kabupaten'
} else if (auth.level === 2) {
    roleLabel.value = 'Admin Kecamatan'
} else if (auth.level === 3) {
    roleLabel.value = 'Admin Desa'
} else {
    roleLabel.value = ''
}

const saveProfile = () => {
    form.uuid = auth.uuid
    form.name = form.name

    submitted.value = true
    form.post('/profile/update-data', {
        resetOnSuccess: true,
        onSuccess: (res) => {
            const messages = res.props.flash.message
            initData()
            alert_response(messages)
            submitted.value = false
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Peringatan', detail: 'Terjadi kesalahan pada sistem', life: 3000 });
            submitted.value = false
        }
    })
}

const signout = () => {
    submitted.value = true
    lform.post('/logout')
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
    <Head title="Profile" />

    <div>
        <h3 class="-mb-5">Update Profile</h3>
        <h5>{{ roleLabel }}</h5>

        <div v-if="!detectMobile" class="card w-full">
            <div class="w-full gap-6">
                <div class="mb-5">
                    <h5 class="md:w-6/12 -mb-0">Nama User</h5>
                    <IconField class="md:w-10/12">
                        <InputIcon class="pi pi-user" />
                        <InputText placeholder="Nama User" v-model="form.name" class="md:w-6/12" :autofocus="true" :disabled="submitted" />
                    </IconField>
                </div>
                <div class="mb-36 md:w-10/12">
                    <Button label="Update" icon="pi pi-save" severity="success" class="md:w-6/12" @click="saveProfile" :disabled="submitted" />
                </div>
                <div class="md:w-10/12">
                    <Button label="Logout" icon="pi pi-sign-out" severity="danger" class="md:w-6/12" @click="signout" :disabled="submitted" />
                </div>
            </div>
        </div>
        <div v-if="detectMobile" class="card w-full">
            <div class="w-full">
                <div class="mb-5">
                    <h5 class="md:w-6/12 -mb-0">Nama User</h5>
                    <IconField class="w-full">
                        <InputIcon class="pi pi-user" />
                        <InputText placeholder="Nama User" v-model="auth.name" fluid :autofocus="true" :disabled="submitted" />
                    </IconField>
                </div>
                <div class="mb-36 md:w-10/12">
                    <Button label="Update" icon="pi pi-save" severity="success" fluid @click="saveProfile" :disabled="submitted" />
                </div>
                <div class="md:w-10/12">
                    <Button label="Logout" icon="pi pi-sign-out" severity="danger" fluid @click="signout" :disabled="submitted" />
                </div>
            </div>
        </div>
    </div>
    <form @submit.prevent="signout"></form>
</template>