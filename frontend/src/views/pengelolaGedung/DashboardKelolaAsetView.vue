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
      const toast = useToast()
      const router = useRouter()
      const isLoading = ref(false)
      const item = reactive({
        data: []
      })

      onMounted(() => {
        init()
      })

      function init() {
        let userId = ''
        if (item.userid != undefined || item.userid != '') {
          userId = '?userid=' + item.userid
        }
        let namaMitra = ''
        if (item.namamitra != undefined || item.namamitra != '') {
          namaMitra = '&namamitra=' + item.namamitra 
        }
        let eMail = ''
        if (item.email != undefined || item.email != '') {
          eMail = '&email=' + item.email
        }
        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`;
        axios.get(`api/adminapp/get-data-mitra-nonvalidasi${userId}${namaMitra}${eMail}&status=0`)
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

      return {
        item,
        cari,
        isLoading,
        init,
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
              <router-link to="/partnership/dashboard/kelola-aset/tambah" class="btn btn-warning">
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
                  <table-body>Gedung A</table-body>
                  <table-body>Rp. 4000000</table-body>
                  <table-body>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" aria-label="Recipient's username" :value="100000">
                      <button class="btn btn-warning" type="button"><i class="bi bi-save"></i></button>
                    </div>
                  </table-body>
                  <table-body>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" role="switch" value="false">
                      <label class="form-check-label">Aktif</label>
                    </div>
                  </table-body>
                  <!-- <table-body v-text="row.email" />
                  <table-body v-text="row.alamat"/> -->
                  <table-body>
                    <router-link :to="{ path: '/partnership/dashboard/detail-aset-mitra/' + row.idmiitra }" class="btn btn-sm btn-success"><i class="bi bi-pencil-square"></i></router-link>
                  </table-body>
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
    </div>
  </div>
</template>