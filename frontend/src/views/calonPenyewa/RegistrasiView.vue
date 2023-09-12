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
        email: '',
        username: '',
        fullname: '',
        password: '',
        passwordconfirmation: '',
        roleuser: 4
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
        if (item.notelepon == '') {
          toats.warning('No Telp belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.norekening == '') {
          toats.warning('No Rekening belum diisi',{
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }
        if (item.alamat == '') {
          toats.warning('Alamat belum diisi',{
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
        axios.post(`api/register/save-registrasi-user`, this.item)
          .then((res) => {
            if (res.data.success) {
              simpanDataPenyewa(res.data.data.id)
              toats.success(res.data.message,{
                type: 'success',
                position: 'top-right',
                duration: '3000'
              })
            }
            btn.innerHTML = `Simpan`
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

      function simpanDataPenyewa(userId) {
        let jsonSave = {
          iduser: userId,
          namalengkap: item.fullname,
          email: item.email,
          alamat: item.alamat,
          notelp: item.notelelpon,
          norekening: item.norekening,
        }
        axios.post(`api/penyewa/save-akun-user-penyewa`, jsonSave)
          .then((res) => {
            if (res.data.success) {
              toats.success(res.data.message, {
                type: 'success',
                position: 'top-right',
                duration: '3000'
              })
            }
            router.push({
              path: '/login'
            })
          })
          .catch((err) => {
            toats.error('Terjadi kesalahan cek kembali data',{
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
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
  <div class="registrasi-page">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-5">
        <div class="card m-5 shadow">
          <div class="card-body">
            <div class="container">
              <h4 class="card-title">
                Daftar Akun Baru
              </h4>
              <p class="card-text">
                Daftar akun Anda untuk melihat semua gedung dan melakukan sewa
              </p>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" v-model="item.email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" v-model="item.username" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" v-model="item.fullname" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">No Telp</label>
                <input type="text" v-model="item.notelelpon" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">No Rekening</label>
                <input type="text" v-model="item.norekening" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea cols="4" rows="4" v-model="item.alamat" class="form-control" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" v-model="item.password" minlength="8" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" v-model="item.passwordconfirmation" minlength="8" class="form-control" @change="cekPassword()" required>
              </div>
              <div class="mb-3 d-grid gap-2">
                <button class="btn btn-info text-light rounded btn-full" @click="simpan($event)">Registrasi</button>
              </div>
              <div class="mb-3 text-center">
                <span>Sudah memiliki akun? <router-link to="/login" class="text-decoration-none">Login di sini</router-link></span>
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