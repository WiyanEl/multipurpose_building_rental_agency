<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { useToast } from 'vue-toastification'
  import { DataTable, TableBody, TableHead, TableBodyCell } from '@jobinsjp/vue3-datatable'
  import axios from 'axios'
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
      const item = reactive({
        data: [],
        dataTambahan: []
      })

      onMounted(() => {
        init()
      })

      function init() {
        let idmitra = ''
        if (idMitra != undefined) {
          idmitra = '?idmitra=' + idMitra
        }
        let idAset = ''
        if (item.idaset != undefined) {
          idAset = '&idaset=' + item.idaset 
        }
        let namaAset = ''
        if (item.namaaset != undefined) {
          namaAset = '&namaaset=' + item.namaaset
        }
        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-aset-mitra${idmitra}${idAset}${namaAset}`)
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

      function disabledAset(event, row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let statusAset = 1;
        if (row.statusaset == 'Aktif') {
          statusAset = 0;
        }

        let jsonSave = {
          idaset: row.idaset,
          statusaset: statusAset
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post('api/pengelola/update-status-aset', jsonSave)
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

      function detailHargaTambahan(row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        item.namaAset = row.namaaset
        item.deskripsi = row.deskripsi
        item.hargaSewa = row.hargasewaaset
        item.hargaDiskon = row.hargadiskonsewa

        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-harga-tambahan-aset?idaset=${row.idaset}`)
        .then((res) => {
          isLoading.value = false
          item.dataTambahan = res.data.data
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

      function hapusAset(event, row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let jsonSave = {
          idaset: row.idaset
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post('api/pengelola/hapus-aset', jsonSave)
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
            toast.error('Terjadi Kesalahan Saat Hapus Aset', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = event.target
          })
      }

      return {
        item,
        cari,
        isLoading,
        init,
        disabledAset,
        detailHargaTambahan,
        hapusAset
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
              <li class="breadcrumb-item active" aria-current="page">Kelola Aset</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3">
                  <label class="form-label">ID Aset</label>
                  <input v-model="item.idaset" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                  <label class="form-label">Nama Aset</label>
                  <input v-model="item.namaaset" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                  <br>
                  <button type="button" class="btn btn-sm btn-info" @click="cari()"><i class="bi bi-search"></i></button>
                </div>
              </div>
            </div>
          </div>
          <div class="card mb-3">
            <div class="card-header">
              <router-link to="/partnership/dashboard/kelola-aset/tambah" class="btn btn-warning" target="_blank">
                <i class="bi bi-plus-circle-dotted"></i> Tambah Aset
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
                  <table-head>Nama Aset</table-head>
                  <table-head>Harga</table-head>
                  <table-head>Harga Diskon</table-head>
                  <table-head>Status Aset</table-head>
                  <table-head/>
                </template>
                <template #tbody-sn="{sn}">
                  <table-head v-text="sn.toString().padStart(2, '0')"/>
                </template>
                <template #tbody="{row}">
                  <table-body v-text="row.namaaset" />
                  <table-body v-text="row.hargasewaaset" />
                  <table-body v-text="row.hargadiskonsewa" />
                  <table-body v-text="row.statusaset" />
                  <table-body>
                    <button type="button" class="btn btn-info btn-sm me-2"  data-bs-toggle="modal" data-bs-target="#modalDetailAset" @click="detailHargaTambahan(row)"><i class="bi bi-info-circle"></i></button>
                    <button type="button" class="btn btn-danger btn-sm me-2" @click="hapusAset($event, row)"><i class="bi bi-trash"></i></button>
                    <button v-if="row.statusaset == 'Aktif'" type="button" class="btn btn-warning btn-sm" @click="disabledAset($event, row)"><i class="bi bi-slash-circle-fill"></i></button>
                    <button v-if="row.statusaset != 'Aktif'" type="button" class="btn btn-warning btn-sm" @click="disabledAset($event, row)"><i class="bi bi-check-circle-fill"></i></button>
                  </table-body>
                </template>
                <template #empty>
                  <TableBodyCell colspan="6">
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

  <!-- Start Modal Detail Aset -->
  <div class="modal fade" data-bs-backdrop="static" id="modalDetailAset" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Tambahan Harga</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Aset</label>
            <input v-model="item.namaAset" type="text" class="form-control" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea v-model="item.deskripsi" cols="6" rows="4" class="form-control" disabled></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga Sewa</label>
            <input v-model="item.hargaSewa" type="text" class="form-control" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Harga Diskon</label>
            <input v-model="item.hargaDiskon" type="text" class="form-control" disabled>
          </div>
          <data-table :rows="item.dataTambahan" 
          :loading="isLoading" 
          @loadData="init" striped sn>
            <template #thead-sn>
              <table-head>SN</table-head>
            </template>
            <template #thead>
              <table-head>Tambahan</table-head>
              <table-head>Harga</table-head>
              <table-head>Tersedia</table-head>
              <table-head/>
            </template>
            <template #tbody-sn="{sn}">
              <table-head v-text="sn.toString().padStart(2, '0')"/>
            </template>
            <template #tbody="{row}">
              <table-body v-text="row.namatambahan" />
              <table-body v-text="row.hargatambahan" />
              <table-body v-text="row.sedia" />
            </template>
            <template #empty>
              <TableBodyCell colspan="5">
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
  <!-- End Modal Detail Aset -->
</template>