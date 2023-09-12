<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { useToast } from 'vue-toastification'
  import { DataTable, TableBody, TableHead, TableBodyCell } from '@jobinsjp/vue3-datatable'
  import VueDatePicker from '@vuepic/vue-datepicker'
  import axios from 'axios'
  import moment from 'moment'
  import SidebarComponent from '../../components/dashboard/SidebarComponent.vue'
  import HeaderComponent from '../../components/dashboard/HeaderComponent.vue'

  export default {
    components: {
      SidebarComponent,
      HeaderComponent,
      TableBody,
      TableHead,
      DataTable,
      TableBodyCell,
      VueDatePicker
    },
    props: {
      data: {
        type: Array,
        required: true
      }
    },
    setup() {
      const token = localStorage.getItem('token')
      const idMitra = localStorage.getItem('idmitra')
      const toast = useToast()
      const router = useRouter()
      const isLoading = ref(false)
      const dateNow = new Date()
      const item = reactive({
        data: [],
      })

      onMounted(() => {
        init()
      })

      function init() {
        let namaDiskon = ''
        if (item.namaDiskon != undefined) {
          namaDiskon = item.namaDiskon
        }
        let tglAwal = ''
        if (item.tglAwal != undefined) {
          tglAwal = '&tglAwal=' + moment(item.tglAwal).format('YYYY-MM-DD HH:mm')
        }
        let tglAkhir = ''
        if (item.tglAkhir != undefined) {
          tglAkhir = '&tglAkhir=' + moment(item.tglAkhir).format('YYYY-MM-DD HH:mm')
        }
        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-diskon-mitra?namadiskon=${namaDiskon}${tglAwal}${tglAkhir}`)
          .then((res) => {
            isLoading.value = false
            item.data = res.data.data
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

      function cari() {
        init()
      }

      function disabledDiskon(event, row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let statusDiskon = 1;
        if (row.statusdiskon == 'Aktif') {
          statusDiskon = 0;
        }

        let jsonSave = {
          iddiskon: row.iddiskon,
          statusdiskon: statusDiskon
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post('api/pengelola/update-status-diskon', jsonSave)
          .then((res) => {
            toast.success(res.data.message, {
              type: 'success',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = event.target
            item.data = []
            init()
          })
          .catch((err) => {
            toast.error('Terjadi Kesalahan Saat Simpan Gambar', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = event.target
          })
      }

      function duplicatDiskon(row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        localStorage.setItem('iddiskon', row.iddiskon)
        router.push({
          path: '/partnership/dashboard/kelola-diskon/tambah'
        })
      }

      return {
        item,
        cari,
        isLoading,
        init,
        disabledDiskon,
        duplicatDiskon
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
              <li class="breadcrumb-item"><router-link to="/partnership/dashboard" class="text-warning text-decoration-none">Home</router-link></li>
              <li class="breadcrumb-item active" aria-current="page">Kelola Diskon</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3">
                  <label class="form-label">Nama Diskon</label>
                  <input v-model="item.namaDiskon" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Tgl Awal</label>
                  <VueDatePicker v-model="item.tglAwal"></VueDatePicker>
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Tgl Akhir</label>
                  <VueDatePicker v-model="item.tglAkhir"></VueDatePicker>
                </div>
                <div class="col-lg-2">
                  <br>
                  <button type="button" class="btn btn-sm btn-info" @click="cari()"><i class="bi bi-search"></i></button>
                </div>
              </div>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-header">
              <router-link to="/partnership/dashboard/kelola-diskon/tambah" class="btn btn-warning btn-sm">
                <i class="bi bi-plus-circle-dotted"></i> Tambah Diskon
              </router-link>
            </div>
            <div class="card-body">
              <data-table :rows="item.data" 
              :loading="isLoading" 
              @loadData="init" striped sn>
                <template #thead-sn>
                  <table-head>SN</table-head>
                </template>
                <template #thead>
                  <table-head>Nama Diskon</table-head>
                  <table-head>Persen Diskon</table-head>
                  <table-head>Harga Diskon</table-head>
                  <table-head>Tgl Mulai</table-head>
                  <table-head>Tgl Akhir</table-head>
                  <table-head>Status Diskon</table-head>
                  <table-head/>
                </template>
                <template #tbody-sn="{sn}">
                  <table-head v-text="sn.toString().padStart(2, '0')"/>
                </template>
                <template #tbody="{row}">
                  <table-body v-text="row.namadiskon" />
                  <table-body v-text="row.persendiskon" />
                  <table-body v-text="row.hargadiskon" />
                  <table-body v-text="row.tglawaldiskon" />
                  <table-body v-text="row.tglakhirdiskon" />
                  <table-body v-text="row.statusdiskon" />
                  <table-body>
                    <button type="button" class="btn btn-info btn-sm me-2" @click="duplicatDiskon(row)"><i class="bi bi-patch-plus-fill"></i></button>
                    <button v-if="row.statusdiskon == 'Aktif'" type="button" class="btn btn-warning btn-sm" @click="disabledDiskon($event, row)"><i class="bi bi-slash-circle-fill"></i></button>
                    <button v-if="row.statusdiskon != 'Aktif'" type="button" class="btn btn-warning btn-sm" @click="disabledDiskon($event, row)"><i class="bi bi-check-circle-fill"></i></button>
                  </table-body>
                </template>
                <template #empty>
                  <TableBodyCell colspan="8">
                    <div class="text-center">No record found.</div>
                  </TableBodyCell>
                </template>
                <template #loading>
                  <div class="text-center">Loading...</div>
                </template>
              </data-table> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>