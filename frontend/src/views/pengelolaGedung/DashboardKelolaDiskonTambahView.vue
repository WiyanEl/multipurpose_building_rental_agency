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
      const idDiskon = localStorage.getItem('iddiskon')
      const toast = useToast()
      const router = useRouter()
      const isLoading = ref(false)
      const dateNow = new Date()
      const item = reactive({
        data: [],
        cekDisabledHargaDiskon: true,
        idDiskon: idDiskon
      })

      onMounted(() => {
        init()
      })

      function init() {
        if (idDiskon != undefined) {
          axios.defaults.headers.common.Authorization = `Bearer ${token}`
          axios.get(`api/pengelola/get-data-diskon-mitra-byid?iddiskon=${idDiskon}`)
          .then((res) => {
            let data = res.data.data
            item.namaDiskon = data.namadiskon
            item.tglAwalDiskon = data.tglawaldiskon
            item.tglAkhirDiskon = data.tglakhirdiskon
            item.persenDiskon = data.persendiskon
            item.hargaDiskon = data.hargadiskon
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
        }
      }

      function kosongkanKolom() {
        item.namaDiskon = undefined
        item.tglAwalDiskon = undefined
        item.tglAkhirDiskon = undefined
        item.persenDiskon = undefined
        item.hargaDiskon = undefined
      }

      function batal() {
        kosongkanKolom()
      }

      function disabledHargaDiskon() {
        item.cekDisabledHargaDiskon = !item.cekDisabledHargaDiskon
        if (item.cekDisabledHargaDiskon) {
          item.hargaDiskon = undefined
        } else {
          item.persenDiskon = undefined
        }
      }

      function simpan(event) {
        if (item.namaDiskon == undefined) {
          toast.warning('Nama Diskon belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.tglAwalDiskon == undefined) {
          toast.warning('Tgl Awal Diskon belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.tglAkhirDiskon == undefined) {
          toast.warning('Tgl Akhir Diskon belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.persenDiskon == undefined && item.hargaDiskon == undefined) {
          toast.warning('Diskon belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        var jsonSave = {
          iddiskon: '',
          idmitra: idMitra,
          namadiskon: item.namaDiskon,
          tglawaldiskon: moment(item.tglAwalDiskon).format('YYYY-MM-DD HH:mm'),
          tglakhirdiskon: moment(item.tglAkhirDiskon).format('YYYY-MM-DD HH:mm'),
          persendiskon: item.persenDiskon != undefined ? item.persenDiskon : null,
          hargadiskon: item.hargaDiskon != undefined ? item.hargaDiskon : null,
          statusdiskon: 1
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post(`api/pengelola/save-diskon-mitra`, jsonSave)
          .then((res) => {
            toast.success(res.data.message, {
              type: 'success',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
            kosongkanKolom()
            localStorage.removeItem('iddiskon')
            router.push({
              path: '/partnership/dashboard/kelola-diskon'
            })
          })
          .catch((err) => {
            toast.error('Simpan Gagal!', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
          })
      }

      return {
        item,
        isLoading,
        init,
        batal,
        disabledHargaDiskon,
        simpan
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
              <li class="breadcrumb-item"><router-link to="/partnership/dashboard/kelola-diskon" class="text-warning text-decoration-none">Kelola Diskon</router-link></li>
              <li class="breadcrumb-item active" aria-current="page">Tambah</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-12">
                  <div class="mb-3">
                    <label class="form-label">Nama Diskon</label>
                    <input v-model="item.namaDiskon" type="text" class="form-control" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Periode Diskon</label>
                    <div class="row">
                      <div class="col-4">
                        <VueDatePicker v-model="item.tglAwalDiskon"></VueDatePicker>
                      </div>
                      <div class="col-4">
                        <VueDatePicker v-model="item.tglAkhirDiskon"></VueDatePicker>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <div class="form-check">
                      <input v-model="item.cekDisabledHargaDiskon" class="form-check-input" type="checkbox" id="cekDisabledHargaDiskon" @click="disabledHargaDiskon()">
                      <label class="form-check-label" for="cekDisabledHargaDiskon">Diskon Persentasi</label>
                    </div>
                    <div class="row">
                      <div class="col-2">
                      <label class="form-label">Persen Diskon (%)</label>
                        <input v-model="item.persenDiskon" type="text" class="form-control" :disabled="item.cekDisabledHargaDiskon == false">
                      </div>
                      <div class="col-10">
                        <label class="form-label">Harga Diskon</label>
                        <input v-model="item.hargaDiskon" type="text" class="form-control" :disabled="item.cekDisabledHargaDiskon == true">
                      </div>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-danger me-2" @click="batal()">Batal</button>
                    <button type="button" class="btn btn-warning" @click="simpan($event)">Simpan</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>