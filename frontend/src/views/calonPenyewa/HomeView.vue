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
        tglAwal: moment(dateNow).format('YYYY-MM-DD 00:00'),
        tglAkhir: moment(dateNow).format('YYYY-MM-DD 23:59')
      })

      function cariMitra() {
        let nMitra = '-'
        if (item.namaMitra != undefined) {
          nMitra = item.namaMitra
        }
        let lokasi = '-'
        if (item.lokasi != undefined) {
          lokasi = item.lokasi
        }
        let kapasitas = '-'
        if (item.kapasitas != undefined) {
          kapasitas = item.kapasitas
        }
        let tglAwal = ''
        if (item.tglAwal != undefined) {
          tglAwal = moment(item.tglAwal).format('YYYY-MM-DD HH:ss');
        }
        let tglAkhir = ''
        if (item.tglAwal != undefined) {
          tglAkhir = moment(item.tglAkhir).format('YYYY-MM-DD HH:ss');
        }
        router.push({
          path: `/daftar-gedung/${lokasi}/${kapasitas}/${nMitra}`
        })
      }

      return {
        item,
        cariMitra
      }
    }
  }
</script>

<template>
  <div>
    <HeaderComponent />
    <div class="jumbotron">
      <div class="container">
        <div class="row">
          <div class="col-12 title-jumbotron text-light text-center">
            <div class="row justify-content-evenly m-5 inner-jumbotron">
              <div class="mb-5">
                <h2 class="text-light">
                  Temukan Gedung, Aula, Ruang Pertemuan Yang Anda Perlukan
                </h2>
              </div>
              <div class="row justify-content-center">
                <div class="col-12 col-lg-8">
                  <div class="card">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-lg-4">
                          <label class="form-label">Lokasi</label>
                          <input v-model="item.lokasi" type="text" class="form-control">
                        </div>
                        <div class="col-lg-4">
                          <label class="form-label">Kapasitas</label>
                          <input v-model="item.kapasitas" type="text" class="form-control">
                        </div>
                        <div class="col-lg-4">
                          <label class="form-label">Nama Mitra</label>
                          <input v-model="item.namaMitra" type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row justify-content-center mt-3">
                <div class="col-12 col-lg-5">
                  <hr>
                </div>
              </div>
              <div class="row justify-content-center mt-3">
                <div class="col-12 col-lg-3">
                  <button type="button" class="btn btn-info text-light ps-5 pe-5 pt-2 pb-2" @click="cariMitra()">CARI</button>
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
  @import url('https://fonts.googleapis.com/css2?family=Merriweather&display=swap');

  .jumbotron {
    width: 100vw;
    height: 100vh;
    background-image: url('https://source.unsplash.com/1200x400?aula');
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
    display: flex;
  }

  .jumbotron::before {
    content: '';
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(0,0,0,0.5),rgba(0,0,0,0.5));
    position: absolute;
    top: 0;
    z-index: 0;
  }

  .jumbotron .container {
    margin: auto;
  } 

  .inner-jumbotron {
    position: relative;
    z-index: 1;
  }

  .inner-jumbotron h2 {
    font-family: 'Merriweather';
    font-size: 3em;
  }
</style>
