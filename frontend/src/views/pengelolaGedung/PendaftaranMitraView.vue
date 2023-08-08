<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'

  export default {
    setup() {
      const token = localStorage.getItem('token')
      const toast = useToast()
      const router = useRouter()
      const item = reactive({
        iduser: localStorage.getItem('iduser'),
        email: localStorage.getItem('emailuser'),
        namamitra: '',
        notelp: '',
        norekening: '',
        alamat: '',
        linkmapsalamat: '',
        gambar: '',
        namafile: ''
      })

      onMounted(() => {
        if (!token) {
          router.push({
            path: '/partnership/login'
          })
        }
      })

      function simpan(event) {
        if (this.item.namamitra == '') {
          toast.warning('Nama Mitra belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (this.item.notelp == '') {
          toast.warning('Nomer Telepon belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (this.item.norekening == '') {
          toast.warning('Nomor Rekening belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (this.item.alamat == '') {
          toast.warning('Alamat belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (this.item.linkmapsalamat == '') {
          toast.warning('Link Google Maps belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        const fromData = new FormData();
        fromData.append('image', this.item.namafile)
        fromData.append('email', this.item.email)
        fromData.append('namamitra', this.item.namamitra)
        fromData.append('notelp', this.item.notelp)
        fromData.append('norekening', this.item.norekening)
        fromData.append('alamat', this.item.alamat)
        fromData.append('linkmapsalamat', this.item.linkmapsalamat)

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post('api/pengelola/simpan-data-mitra', fromData)
          .then((res) => {
            if (res.data.success) {
              toast.success(res.data.message, {
                type: 'success',
                position: 'top-right',
                duration: '3000'
              })
            } else {
              toast.warning(res.data.message, {
                type: 'warning',
                position: 'top-right',
                duration: '3000'
              })
            }
            btn.innerHTML = 'Daftar'
            kosongkanKolom()
          })
          .catch((err) => {
            toast.error(err.message,{
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = 'Daftar'
          })
      }

      function getImage(event) {
        this.item.namafile = event.target.files[0]
      }

      function kosongkanKolom() {
        item.iduser = localStorage.getItem('iduser')
        item.email = localStorage.getItem('emailuser')
        item.namamitra = ''
        item.notelp = ''
        item.norekening = ''
        item.alamat = ''
        item.linkmapsalamat = ''
        item.gambar = ''
        item.namafile = ''
      }

      function logout(event) {
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        axios
          .post('api/register/logout')
          .then((res) => {
            if (res.data.success) {
              localStorage.removeItem('token')
              localStorage.removeItem('iduser')
              localStorage.removeItem('emailuser')
              localStorage.removeItem('roleuser')
              router.push({
                path: '/partnership/login'
              });
            }
            btn.innerHTML = 'Logout'
          })
          .catch((err) => {
            console.log(err.response.data)
            toast.error('Terjadi kesalahan coba lagi', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = 'Logout'
          });
      }

      return {
        item,
        simpan,
        getImage,
        logout,
      }
    }
  }
</script>

<template>
  <div class="registrasi-page bg-info">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-5">
        <div class="card m-5 shadow">
          <div class="card-body">
            <div class="container">
              <h4 class="card-title">
                Selamat Datang 
              </h4>
              <p class="card-text">
                Daftarkan aset Anda, biarkan Kami yang akan mendekatkan konsumen kepada Anda
              </p>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="item.email" type="email" class="form-control" disabled>
              </div>
              <div class="mb-3">
                <label class="form-label">Nama Mitra</label>
                <input v-model="item.namamitra" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">No Telp</label>
                <input v-model="item.notelp" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">No Rekening</label>
                <input v-model="item.norekening" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <input v-model="item.alamat" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Link Google Maps</label>
                <input v-model="item.linkmapsalamat" type="text" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Sample Gambar</label>
                <input type="file" class="form-control" id="input-img-daftar" @change="getImage($event)">
              </div>
              <div class="mb-3 d-grid gap-2">
                <button type="button" class="btn btn-warning text-light rounded btn-full" @click="simpan($event)">Daftar</button>
              </div>
              <div class="mb-3 text-center">
                <button type="button" class="btn btn-sm btn-info text-light" @click="logout($event)">Logout</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .registrasi-page {
    min-height: 100vh;
    min-width: 100vw;
  }
</style>