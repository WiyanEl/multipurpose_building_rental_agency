<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'

  export default {
    setup() {
      const token = localStorage.getItem('token')
      const roleuser = localStorage.getItem('roleuser')
      const toast = useToast()
      const router = useRouter()
      const item = reactive({
        email: '',
        password: '',
      })

      onMounted(() => {
        if (token) {
          if (roleuser == 1) {
            router.push({
              path: '/adminapp/dashboard'
            })
          } 
        }
      })

      function login(event) {
        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.post('api/register/login', this.item)
          .then((res) => {
            if (res.data.success) {
              localStorage.setItem('token', res.data.token)
              localStorage.setItem('iduser', res.data.data.id)
              localStorage.setItem('emailuser', res.data.data.email)
              localStorage.setItem('roleuser', res.data.data.user_role)
              if (res.data.data.user_role == 1) {
                router.push({
                  path: '/adminapp/dashboard'
                })
              } else  {
                toast.warning('Anda bukanlah Admin', {
                  type: 'warning',
                  position: 'top-right',
                  duration: '3000'
                })
                localStorage.removeItem('token')
                localStorage.removeItem('iduser')
                localStorage.removeItem('emailuser')
                localStorage.removeItem('roleuser')
              }
            }
            btn.innerHTML = 'Login'
          })
          .catch((err) => {
            btn.innerHTML = 'Login'
            toast.error('Email atau Password salah', {
              type: 'error',
              location: 'top-right',
              duration: '3000'
            })
          })
      }

      return {
        item,
        login
      }
    }
  }
</script>

<template>
  <div class="registrasi-page bg-warning">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-5">
        <div class="card m-5 shadow">
          <div class="card-body">
            <div class="container">
              <h4 class="card-title">
                Selamat Datang
              </h4>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="item.email" type="email" class="form-control">
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="item.password" type="password" class="form-control">
              </div>
              <div class="mb-3 d-grid gap-2">
                <button type="button" class="btn btn-info text-light rounded btn-full" @click="login($event)">Login</button>
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