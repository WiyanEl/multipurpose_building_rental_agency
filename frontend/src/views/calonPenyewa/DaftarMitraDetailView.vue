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
      VueDatePicker,
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
      const baseUrl = axios.defaults.baseURL
      const item = reactive({
        idMitra: route.params.id,
        listAset: []
      })

      onMounted(() => {
        init()
      })

      function init() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/penyewa/get-data-mitra?idmitra=${item.idMitra}`)
          .then((res) => {
            var data = res.data.data
            item.alamat = data.alamat
            item.urlGambar = `${axios.defaults.baseURL}${data.path}`
            item.gambar = data.gambar
            item.userid = data.userid
            item.linkmaps = data.linkmapsalamat
            item.fullname = data.namalengkap
            item.namamitra = data.namamitra
            item.norekening = data.norekening
            item.notelp = data.notelp
            item.fasilitas = data.fasilitas
            item.kapasitas = data.makskapasitas
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })

        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/penyewa/get-data-aset-mitra?idmitra=${item.idMitra}`)
          .then((res) => {
            item.listAset = res.data.data
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })

        
      }

      return {
        item,
        baseUrl
      }
    }
  }
</script>

<template>
  <div>
    <HeaderComponent />
    <div class="container mt-5 mb-5 listData">
      <div class="row mb-3">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="container">
                <div class="row">
                  <div class="col-lg-4">
                    <img v-bind:src="item.urlGambar" class="img-thumbnail" alt="Gambar Mitra">
                  </div>
                  <div class="col-lg-8">
                    <div class="mb-3">
                      <h3>{{ item.namamitra }} ({{ item.notelp }})</h3>
                    </div>
                    <div class="card-text">
                      <h5>{{ item.alamat }}</h5>
                    </div>
                    <div class="card-text">
                      <h5>Fasilitas : {{ item.fasilitas }}</h5>
                    </div>
                    <div class="card-text">
                      <h5>Kapasitas Sampai Dengan : {{ item.kapasitas }}</h5>
                    </div>
                    <div class="mb-3">
                      <a v-bind:href="item.linkmaps" target="_blank" class="card-link">Link G-Maps</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="alert alert-warning text-center" role="alert">
            DAFTAR ASET
          </div>
        </div>
      </div>
      <div class="row">
        <div v-for="list in item.listAset" class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-4">
                  <img v-bind:src="baseUrl + list.asetgambar[0].path" class="img-thumbnail" alt="Gambar Mitra">
                </div>
                <div class="col-lg-8">
                  <router-link :to="{ name: 'dataDetailAsetMitra', params: { id: list.idaset } }" class="card-title fs-3 text-decoration-none">
                    {{ list.namaaset }}
                  </router-link>
                  <div v-if="list.hargadiskon != null" class="card-subtitle text-muted">
                    <del>Rp. {{ list.hargasewa }}</del>
                  </div>
                  <div v-if="list.hargadiskon != null" class="card-text text-warning fs-3">
                    Rp. {{ list.hargasewa - list.hargadiskon }} / {{ list.maxjamsewa }} Jam
                  </div>
                  <div v-if="list.hargadiskon == null" class="card-text text-warning fs-3">
                    Rp. {{ list.hargasewa }} / {{ list.maxjamsewa }} Jam
                  </div>
                  <div class="card-subtitle text-muted">
                    {{ list.kapasitas }} Orang
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

</style>