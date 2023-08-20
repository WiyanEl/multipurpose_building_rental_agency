<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import SidebarComponent from '../../components/dashboard/SidebarComponent.vue'
  import HeaderComponent from '../../components/dashboard/HeaderComponent.vue'

  export default {
    components: {
      SidebarComponent,
      HeaderComponent
    },
    setup() {
      const token = localStorage.getItem('token')
      const toast = useToast()
      const router = useRouter()
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
          <h3>Selamat Datang</h3>
        </div>
      </div>
    </div>
  </div>
</template>