<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import HeaderComponent from '../../components/dashboard/headercomponent.vue'
  import SidebarComponent from '../../components/dashboard/SidebarComponent.vue'

  export default {
    components: {
      SidebarComponent,
      HeaderComponent,
    },
    setup() {
      const token = localStorage.getItem('token')
      const toast = useToast()
      const route = useRoute()
      const router = useRouter()
      const isLoading = ref(false)
      const item = reactive({
        email: route.params.email
      })

      onMounted(() => {
        init()
      })

      function init() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/adminapp/get-detail-mitra-nonvalidasi?email=${item.email}`)
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
            item.kapastitas = data.makskapasitas
            item.fasilitas = data.fasilitas
          })
          .catch((err) => {
            isLoading.value = false
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function simpanValidasi(event) {
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        let jsonSave = {
          statusmitra: 1,
          email: item.email,
          userrole: 3
        }
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post(`api/adminapp/simpan-validasi-mitra`, jsonSave)
          .then((res) => {
            if (res.data.success) {
              toast.success(res.data.message, {
                type: 'success',
                position: 'top-right',
                duration: '3000'
              })

              let body = 'Pendaftaran Mitra Berhasil! Anda dapat login kembali dan kelola aset Anda'
              axios.get(`api/adminapp/send-email-to-mitra?emailtujuan=${item.email}&body=${body}`)
              .then((resp) => {
                toast.success(resp.data.message, {
                  type: 'success',
                  position: 'top-right',
                  duration: '3000'
                })
                btn.innerHTML = `<span><i class="bi bi-check-lg"></i> Validasi</span>`
                router.push({
                  path: '/adminapp/dashboard/daftar-validasi-mitra'
                })
              })
              .catch((error) => {
                toast.error('Terjadi kesalahan', {
                  type: 'error',
                  position: 'top-right',
                  duration: '3000'
                })
                btn.innerHTML = `<span><i class="bi bi-check-lg"></i> Validasi</span>`
              })
            }
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `<span><i class="bi bi-check-lg"></i> Validasi</span>`
          })
      }

      function kirimGagalValidasi(event) {
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/adminapp/send-email-to-mitra?emailtujuan=${item.email}&body=${item.keteranganTolak}`)
          .then((res) => {
            toast.success(res.data.message, {
              type: 'success',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = 'Simpan'
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = 'Simpan'
          })
      }

      return {
        item,
        init,
        kirimGagalValidasi,
        simpanValidasi,
      }
    }
  }
</script>

<template>
  <div class="content">
    <SidebarComponent />
    <div class="home_content">
      <div class="wrapper">
        <HeaderComponent />
        <div class="container">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><router-link to="/adminapp/dashboard" class="text-warning text-decoration-none">Home</router-link></li>
              <li class="breadcrumb-item"><router-link to="/adminapp/dashboard/daftar-validasi-mitra" class="text-warning text-decoration-none">Validasi Mitra</router-link></li>
              <li class="breadcrumb-item active" aria-current="page">Detail Validasi</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <img v-bind:src="item.urlGambar" class="card-img-top" alt="Gambar Sample Mitra">
            <div class="card-body fs-5">
              <h5 class="card-title">{{ item.namamitra }}</h5>
              <h6 class="card-subtitle mb-2 text-muted">{{ item.namalengkap }}</h6>
              <div class="card-text">
                {{ item.alamat }}
              </div>
              <div class="card-text">
                Telp: {{ item.notelp }}
              </div>
              <div class="card-text">
                Email: {{ item.email }}
              </div>
              <div class="card-text">
                No. Rekening: {{ item.norekening }}
              </div>
              <div class="card-text">
                Kapasitas: {{ item.kapastitas }} Orang
              </div>
              <div class="card-text">
                Fasilitas: {{ item.fasilitas }}
              </div>
              <a v-bind:href="item.linkmaps" target="_blank" class="card-link">Link G-Maps</a>
              <div class="mt-3">
                <button type="button" class="btn btn-sm btn-success" @click="simpanValidasi($event)">
                  <span><i class="bi bi-check-lg"></i> Validasi</span>
                </button>
                <button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#modalTolakValidasiMitra">
                  <span><i class="bi bi-x"></i> Tolak</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Tolak Validasi -->
        <div class="modal fade" id="modalTolakValidasiMitra" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTolakValidasiMitraLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalTolakValidasiMitraLabel">Tolak Validasi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Keterangan</label>
                  <textarea v-model="item.keteranganTolak" cols="6" rows="6" class="form-control"></textarea>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-primary" @click="kirimGagalValidasi($event)">Simpan</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal Tolak Validasi -->
      </div>
    </div>
  </div>
</template>