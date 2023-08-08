<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import { ref,reactive } from 'vue'

  export default {
    setup() {
      const route = useRoute()
      const router = useRouter()
      const toats = useToast()
      const item = reactive({
        email: route.params.email,
        username: '',
        fullname: '',
        password: '',
        passwordconfirmation: '',
        roleuser: 2
      })

      function cekPassword() {
        if (item.password != item.passwordconfirmation) {
          toats.warning('Konfirmasi Password tidak sesuai dengan Password',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
        }
      }

      function simpan(event) {
        if (item.email == '') {
          toats.warning('Email belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.username == '') {
          toats.warning('Username belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.fullname == '') {
          toats.warning('Nama Akun belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.password == '') {
          toats.warning('Password belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.passwordconfirmation == '') {
          toats.warning('Password Konfirmasi belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.post(`/api/register/save-registrasi-user`, this.item)
          .then((res) => {
            if (res.data.success) {
              toats.success(res.data.message,{
                type: 'success',
                position: 'top-right',
                duration: '3000'
              })
            }
            btn.innerHTML = `Simpan`
            router.push({ path: '/partnership/login' })
          })
          .catch((err) => {
            toats.error('Terjadi kesalahan cek kembali data',{
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
          })
      }

      return {
        item,
        simpan,
        cekPassword,
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
                Daftar Akun Baru
              </h4>
              <p class="card-text">
                Daftarkan aset Anda di O'mbrA dan kami akan menghubungkan Anda dengan jutaan konsumen!
              </p>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" v-model="item.email" class="form-control" placeholder="Masukkan alamat email Anda di sini" disabled required>
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" v-model="item.username" class="form-control" placeholder="Masukkan username Anda di sini" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nama Akun</label>
                <input type="text" v-model="item.fullname" class="form-control" placeholder="Masukkan nama akun Anda di sini" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" v-model="item.password" minlength="8" class="form-control" placeholder="Masukkan password Anda di sini" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" v-model="item.passwordconfirmation" minlength="8" class="form-control" placeholder="Masukkan password Anda di sini" @change="cekPassword()" required>
              </div>
              <div class="mb-3 d-grid gap-2">
                <button class="btn btn-warning text-light rounded btn-full" @click="simpan($event)">Simpan</button>
              </div>
              <div class="mb-3 text-center">
                <router-link to="/partnership/register" class="text-decoration-none">Kembali</router-link>
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