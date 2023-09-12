<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { onMounted, ref, reactive } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'

  export default {
    setup() {
      const token = localStorage.getItem('token')
      const toast = useToast()
      const router = useRouter()
      const user = ref({})

      onMounted(() => {
        if (token != undefined) {

          axios.defaults.headers.common.Authorization = `Bearer ${token}`
          axios
            .get('/api/user')
            .then((response) => {
              user.value = response.data
            })
            .catch((err) => {
              console.log(err.response.data)
            })
        }
      })

      function logout() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios
          .post('/api/register/logout')
          .then((res) => {
            if (res.data.success) {
              localStorage.removeItem('token')
              localStorage.removeItem('iduser')
              localStorage.removeItem('emailuser')
              localStorage.removeItem('roleuser')
              localStorage.removeItem('idmitra')
              router.push({
                path: '/login'
              })
            }
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan coba lagi', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
        }

      return {
        user,
        token,
        logout
      }
    }
  }
</script>

<template>
  <div>
    <nav class="navbar navbar-expand-lg bg-white shadow">
      <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
          <router-link to="/" class="navbar-brand">O'BuildingRents</router-link>
          <div class="ms-auto">
            <div v-if="token != undefined">
              <ul class="navbar-nav">
                <li class="nav-item dropdown">
                  <button class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    {{ user.fullname }}
                  </button>
                  <ul class="dropdown-menu">
                    <li><button class="dropdown-item">{{ user.fullname }}</button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><button class="dropdown-item" @click="logout()">Log Out</button></li>
                  </ul>
                </li>
              </ul>
            </div>
            <div v-else>
              <router-link to="/login" class="btn btn-sm text-light btn-info me-2">Login</router-link>
              <router-link to="/registrasi" class="btn btn-sm text-light btn-info">Registrasi</router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>