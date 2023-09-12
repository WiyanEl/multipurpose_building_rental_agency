<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import moment from 'moment'
  import FullCalendar from '@fullcalendar/vue3'
  import dayGridPlugin from '@fullcalendar/daygrid'
  import interactionPlugin from '@fullcalendar/interaction'
  import SidebarComponent from '../../components/dashboard/SidebarComponent.vue'
  import HeaderComponent from '../../components/dashboard/HeaderComponent.vue'

  export default {
    components: {
      SidebarComponent,
      HeaderComponent,
      FullCalendar
    },
    setup() {
      const token = localStorage.getItem('token')
      const emailUser = localStorage.getItem('emailuser')
      const idUser = localStorage.getItem('iduser')
      const toast = useToast()
      const router = useRouter()
      const item = reactive({
        dataJadwal: [],
        dateEvents: []
      })
      const calendarOptions = {
        plugins: [ dayGridPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        events: item.dateEvents
      }

      onMounted(() => {
        init()
      })

      function init() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-mitra?email=${emailUser}&iduser=${idUser}`)
        .then((res) => {
          localStorage.setItem('idmitra', res.data.data.id)
          getDataPesanan(res.data.data.id)
        })
        .catch((err) => {
          toast.error('Terjadi kesalahan', {
            type: 'error',
            position: 'top-right',
            duration: '3000'
          })
        })
      }

      function getDataPesanan(idmitra) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/cek-jadwal-sewa-mitra?idmitra=${idmitra}`)
          .then((res) => {
            item.dataJadwal = res.data.data
            for (let key of item.dataJadwal) {
              let tglAwal = moment(key.tglawalsewa).format('YYYY-MM-DD')
              item.dateEvents.push({
                title: key.namaaset,
                date: tglAwal
              })
            }
          })
          .catch((err) => {
            toast.error('Terjadi Kesalahan Saat Menarik Data', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      return {
        calendarOptions
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
          <div class="card">
            <div class="card-body">
              <FullCalendar :options="calendarOptions" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>