<script>
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import { ref,reactive } from 'vue'
  import { RouterLink, useRouter, useRoute } from 'vue-router'

  export default {
    setup() {
      const toats = useToast()
      const router = useRouter()
      const item = reactive({
        email: '',
      })

      function searchEmail(event,email) {
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.get(`api/register/get-email?email=${email}`)
          .then((res) => {
            if (res.data.data == null) {
              router.push({ 
                path: `/partnership/registrasi/${email}`, 
              })
            } else {
              toats.info('Alamat Email sudah pernah digunakan', {
                type: 'info',
                position: 'top-center',
                duration: '3000'
              })
            }
            btn.innerHTML = `Lanjut`
          })
          .catch((err) => {
            console.log(err);
            btn.innerHTML = `Lanjut`
          })
      }

      return {
        item,
        searchEmail,
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
                Buat Akun Baru
              </h4>
              <p class="card-text">
                Daftarkan aset Anda di O'mbrA dan kami akan menghubungkan Anda dengan jutaan konsumen!
              </p>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" v-model="item.email" class="form-control" placeholder="Masukkan alamat email Anda di sini">
              </div>
              <div class="mb-3 d-grid gap-2">
                <button class="btn btn-warning text-light rounded btn-full" @click="searchEmail($event,item.email)">Lanjut</button>
              </div>
              <div class="mb-3">
                <span>Sudah memiliki akun? <router-link to="/partnership/login" class="text-decoration-none">Login di sini</router-link></span>
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