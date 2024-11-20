<script setup>
import { defineProps, ref } from 'vue';
import { Socket, io } from 'socket.io-client';
import logo from '@/Assets/Lambang_Kabupaten_Karanganyar-grayscale-84px.png';
import Fluid from 'primevue/fluid';

const datas = defineProps({
    paslon: Object,
    kec: Object,
    desa: Object,
    suara: Object,
})
const socket = io('http://localhost:3000', {
    withCredentials: true,
})
// const socket = io('https://qcws.caturnus.com', {
//     withCredentials: true,
// })

const dataPaslon = ref(datas.suara.valid)
const voteInvalid = ref(0)
const voteValid = ref(0)
const voteTotal = ref(0)

const initData = () => {
    voteInvalid.value = parseInt(datas.suara.invalid)
    voteValid.value = 0
    datas.suara.valid.map((dv) => {
        const add = voteValid.value + parseInt(dv.total)
        voteValid.value = add
    })
}
initData()

function formatNumber(value) {
    if (value) return value.toLocaleString({ style: 'number' })
    return 0
}

socket.on('get-paslon', (gp) => {
    // console.log('get', gp)
    if (gp.uuid === 'invalid') {
        const add = voteInvalid.value + gp.vote
        voteInvalid.value = add
        return true
    } else {
        dataPaslon.value.some((dp,d) => {
            if (dp.uuid === gp.uuid) {
                const add = dp.total + gp.vote
                dataPaslon.value[d].total = add
                voteValid.value = voteValid.value + gp.vote
                return true
            }
        })
    }
})

socket.on('update-paslon', (up) => {
    // console.log('update', up)
    if (up.uuid === 'invalid') {
        const add = voteInvalid.value + up.vote
        voteInvalid.value = add
        return true
    } else {
        dataPaslon.value.some((dp,d) => {
            if (dp.uuid === up.uuid) {
                const add = dp.total + up.vote
                dataPaslon.value[d].total = add
                voteValid.value = voteValid.value + up.vote
                return true
            }
        })
    }
})
// console.log(datas.suara)
</script>

<template>
    <div class="mb-10" v-if="dataPaslon.length > 0">
        <h2 class="w-full text-center py-5">
            Data Suara Masuk <br>
            {{ formatNumber(voteValid + voteInvalid) }} <small>suara</small>
        </h2>

        <div :class="(dataPaslon.length < 3 || dataPaslon.length === 4 ) ? 'grid grid-cols-2' : 'grid grid-cols-3'" class="gap-12 w-full justify-evenly mb-5">
            <div class="box" v-for="dp in dataPaslon">
                <div class="box-icon">
                    <!-- <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"></path></svg> -->
                    <img :src="logo" style="max-width: 40px">
                    </div>
                <div class="box-label">{{ formatNumber(dp.total) }} Suara ({{ (dp.total / (voteValid+voteInvalid) * 100).toFixed(1) ?? 0 }}%)</div>
                <div class="box-title">{{ dp.urut }} - {{ dp.name }}</div>
                <div class="box-image">
                    <img :src="dp.foto" alt="profile">
                </div>
                <!-- <div class="studio-button">
                    <div class="studio-button-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg>
                    </div>
                    <div class="studio-button-label">
                    Open in Studio
                    </div>
                </div> -->
            </div>
        </div>

        <div class="w-full text-center">
            <label><i>* Suara Tidak Sah : {{ formatNumber(voteInvalid) }} Suara ({{ (voteInvalid / (voteValid+voteInvalid) * 100).toFixed(1) ?? 0 }}%)</i></label>
        </div>
    </div>

    <div class="w-full text-center" v-if="dataPaslon.length < 1">
        <h1>Belum ada data Pasangan Calon</h1>
    </div>
</template>

<style lang="scss">
.box {
    // background: white;
    background: var(--surface-overlay);
    border-radius: 20px;
    display: grid;
    grid-template-columns: 64px 1fr;
    position: relative;
    //   width: 33.2em;
}

.box-icon {
    display: grid;
    place-items: center;
}

.box-label {
    height: 64px;
    display: flex;
    align-items: center;
    padding-left: 16px;
    font-size: 16px;
    letter-spacing: 0.125em;
}

.box-title {
    white-space: nowrap;
    display: flex;
    align-items: center;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    font-size: 20px;
    padding-top: 16px;
}

.box-image {
    // width: 400px;
    // height: 400px;
    border-radius: 18px 0 18px 0;
    overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
  }
}

.box-three {
    // width: 266px;
    // height: 266px;
    width: 406px;
    height: 406px;
}

.box-five {
    width: 376px;
    height: 376px;
}

.studio-button {
    position: absolute;
    bottom: 16px;
    right: 16px;
    display: flex;
    align-items: center;
    background: #68009d;
    color: white;
    padding: 8px 10px;
    border-radius: 50px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    transition: 0.35s ease all;
    overflow: hidden;
    max-width: 22px; /* icon size */
}

.studio-button-icon {
    position: relative;
    top: 1px;
}

.studio-button-label {
    text-transform: uppercase;
    white-space: nowrap;
    padding: 0 8px;
    opacity: 0;
    transform: translateX(10px);
    transition: 0.25s ease all;
}

.box:hover {
    .studio-button {
        max-width: 100%;
    }
    .studio-button-label {
        opacity: 1;
        transform: translateX(0);
        transition: 0.25s 0.1s ease-in opacity, 0.15s 0.1s cubic-bezier(.175, .885, .32, 1.275) transform;
    }
}

</style>