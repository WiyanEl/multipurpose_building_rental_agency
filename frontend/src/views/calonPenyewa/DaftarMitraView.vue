<script>
  import FooterComponentVue from '../../components/calonPenyewa/FooterComponent.vue'
  import HeaderComponent from '../../components/calonPenyewa/HeaderComponent.vue'
  import VueDatePicker from '@vuepic/vue-datepicker'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import moment from 'moment'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { RouterLink, useRouter, useRoute } from 'vue-router'

  export default {
    components: {
      HeaderComponent,
      FooterComponentVue,
      VueDatePicker
    },
    setup() {
      const token = localStorage.getItem('token')
      const emailUser = localStorage.getItem('emailuser')
      const idUser = localStorage.getItem('iduser')
      const roleUser = localStorage.getItem('roleuser')
      const toast = useToast()
      const route = useRoute()
      const router = useRouter()
      const dateNow = new Date()
      const item = reactive({
        lokasi: route.params.lokasi,
        kapasitas: route.params.kapasitas,
        namaMitra: route.params.nMitra
      })

      onMounted(() => {
        init()
      })

      function init() {
        let lokasi = ''
        if (item.lokasi != undefined && item.lokasi != '-') {
          lokasi = item.lokasi
        }
        let kapasitas = ''
        if (item.kapasitas != undefined && item.kapasitas != '-') {
          kapasitas = item.kapasitas
        }
        let nMitra = ''
        if (item.namaMitra != undefined && item.namaMitra != '-') {
          nMitra = item.namaMitra
        }
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/penyewa/get-list-mitra?lokasi=${lokasi}&kapasitas=${kapasitas}&namamitra=${nMitra}`)
          .then((res) => {
            item.listMitra = res.data.data
            let i = 0;
            for (const key of item.listMitra) {
              item.listMitra[i].urlImage = `${axios.defaults.baseURL}${key.path}`
              i++
            }
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function cari(event) {
        let tglAwal = ''
        if (item.tglAwal != undefined) {
          tglAwal = moment(item.tglAwal).format('YYYY-MM-DD HH:ss')
        }
        let tglAkhir = ''
        if (item.tglAkhir != undefined) {
          tglAkhir = moment(item.tglAkhir).format('YYYY-MM-DD HH:ss')
        }
        let nMitra = ''
        if (item.namaMitra != undefined && item.namaMitra != '-') {
          nMitra = item.namaMitra
        }
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        item.listMitra = []
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/penyewa/get-list-mitra?tglawal=${tglAwal}&tglakhir=${tglAkhir}&namamitra=${nMitra}`)
          .then((res) => {
            item.listMitra = res.data.data
            let i = 0;
            for (const key of item.listMitra) {
              item.listMitra[i].urlImage = `${axios.defaults.baseURL}${key.path}`
              i++
            }
            btn.innerHTML = `CARI`
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `CARI`
          })
      }

      return {
        item,
        cari
      }
    }
  }
</script>

<template>
  <div>
    <HeaderComponent />
    <div class="container mt-5 mb-5 listData">
      <div class="row">
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <div class="container">
                <div class="mb-3">
                  <label class="form-label">Lokasi</label>
                  <input v-model="item.lokasi" type="text" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">Kapasitas</label>
                  <input v-model="item.kapasitas" type="text" class="form-control">
                </div>
                <div class="mb-3">
                  <label class="form-label">Nama Mitra</label>
                  <input v-model="item.namaMitra" type="text" class="form-control">
                </div>
                <div class="mb-3 d-grid gap-2">
                  <button type="button" class="btn btn-info text-light rounded btn-full" @click="cari($event)">CARI</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="card">
            <div class="card-body">
              <div class="container">
                <div class="row">
                  <div v-for="key in item.listMitra" class="col-lg-4">
                    <div class="card shadow-sm mt-3 mb-3 card-mitra">
                      <img v-bind:src="key.urlImage" class="card-img-top" alt="Gambar Mitra">
                      <div class="card-body fs-5">
                        <h5 class="card-title">{{ key.namamitra }}</h5>
                        <h6 class="card-subtitle mb-1 text-muted">{{ key.alamat }}</h6>
                        <div class="card-text">
                          Telp: {{ key.notelp }}
                        </div>
                        <div class="card-text">
                          Kapasitas: {{ key.makskapasitas }}
                        </div>
                        <a v-bind:href="key.linkmapsalamat" target="_blank" class="card-link">Link G-Maps</a>
                        <div class="mt-1 d-grid gap-2">
                          <router-link :to="{ name: 'dataDetailMitra', params: { id: key.id } }" class="btn btn-info btn-sm text-light rounded btn-full">Pilih</router-link>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <FooterComponentVue />
  </div>
</template>

<style scoped>
  .listData {
    min-height: 100vh;
  }

  .card-mitra {
    min-height: 300px;
  }
</style>