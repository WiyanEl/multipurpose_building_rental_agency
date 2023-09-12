<script>
  import { RouterLink, useRouter } from 'vue-router'
  import { onMounted, ref, reactive } from 'vue'
  import axios from 'axios'

  export default {
    setup() {
      const token = localStorage.getItem('token')
      const roleUser = localStorage.getItem('roleuser')
      const router = useRouter()
      const item = reactive({
        ucapan: ''
      })
      const user = ref('')

      onMounted(() => {
        if (!token) {
          return router.push({
            path: '/adminapp/login'
          })
        }

        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios
          .get('/api/user')
          .then((response) => {
            user.value = response.data
          })
          .catch((err) => {
            console.log(err.response.data)
          })

        let jam = (new Date()).getHours()
        if (jam >= 4 && jam < 10 ) {
          item.ucapan = 'Pagi'
        } else if (jam >= 10 && jam < 15 ) {
          item.ucapan = 'Siang'
        } else if (jam >= 15 && jam < 18 ) {
          item.ucapan = 'Sore'
        } else if (jam >= 18 || jam < 4 ) {
          item.ucapan = 'Malam'
        }
      })

      function dropdownUser() {
        const dropdown = document.querySelector('.dropdown-user')
        dropdown.classList.toggle('active')
      }

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
              if (roleUser == 1) {
                router.push({
                  path: '/adminapp/login'
                })
              } else {
                router.push({
                  path: '/partnership/login'
                })
              }
            }
          })
          .catch((err) => {
            console.log(err.response.data)
            toast.error('Terjadi kesalahan coba lagi', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
        }

      return {
        token,
        router,
        user,
        dropdownUser,
        logout,
        item,
      }
    },
  }
</script>

<template>
  <header class="d-flex header-dashboard">
    <div class="title-page d-flex align-items-center">
      <h5>Selamat {{ item.ucapan }}</h5>
    </div>
    <div class="user-info ms-auto d-flex align-items-center pe-4">
      <div class="dropdown-header">
        <button type="button" class="border-0 bg-transparent toggle-dropdown" @click="dropdownUser">{{ user.fullname }}</button>
        <ul class="dropdown-user shadow bg-white">
          <li>
            <router-link to="/adminapp/dashboard" class="text-decoration-none text-underline-none text-dark"><i class="bi bi-layout-text-window-reverse"></i> Dashboard</router-link>
          </li>
          <li @click.prevent="logout"><i class="bi bi-box-arrow-left text-danger"></i> <span class="text-dark">Logout</span></li>
        </ul>
      </div>
    </div>
  </header>
</template>