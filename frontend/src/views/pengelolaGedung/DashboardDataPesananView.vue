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
    setup() {
      const token = localStorage.getItem('token')
      const idMitra = localStorage.getItem('idmitra')
      const toast = useToast()
      const router = useRouter()
      const isLoading = ref(false)
      const dateNow = new Date()
      const item = reactive({
        data: [],
        listAset: [],
        dataDetailPesanan: [],
        dataDetailPembayaran: [],
        tglawal: new Date(),
        tglakhir: dateNow.setDate(dateNow.getDate() + 30),
      })
      const itemJ = reactive({
        dataJadwal: [],
      })

      onMounted(() => {
        getAset()
        init()
      })

      function getAset() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-list-aset?idmitra=${idMitra}`)
          .then((res) => {
            item.listAset = res.data.data
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function init() {
        let id_mitra = ''
        if (idMitra != undefined) {
          id_mitra = '&idmitra=' + idMitra
        }
        let noPesanan = ''
        if (item.nopesanan != undefined) {
          noPesanan = '&nopesanan=' + item.nopesanan
        }
        let namaPemesan = ''
        if (item.namapemesan != undefined) {
          namaPemesan = '&namapemesan=' + item.namapemesan 
        }
        let tglAwal = ''
        if (item.tglawal != undefined) {
          tglAwal = moment(item.tglawal).format('YYYY-MM-DD HH:mm')
        }
        let tglAkhir = ''
        if (item.tglakhir != undefined) {
          tglAkhir = moment(item.tglakhir).format('YYYY-MM-DD HH:mm')
        }
        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-pesanan-sewa-mitra?tglawal=${tglAwal}&tglakhir=${tglAkhir}${id_mitra}${noPesanan}${namaPemesan}`)
          .then((res) => {
            isLoading.value = false
            item.data = res.data.data
            for (let i = 0; i < item.data.length; i++) {
              item.data[i].tglsewa = item.data[i].tglawalsewa + ' - ' + item.data[i].tglakhirsewa
            }
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

      function cekJadwal(event) {
        if (itemJ.aset == undefined && itemJ.aset == 0) {
          toast.warning('Aset Gedung Belum Dipilih', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        if (itemJ.tglAwalSewa == undefined) {
          toast.warning('Tgl Awal Sewa Belum Diisi', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        if (itemJ.tglAkhirSewa == undefined) {
          toast.warning('Tgl Akhir Sewa Belum Diisi', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let tglAwalS = moment(itemJ.tglAwalSewa).format('YYYY-MM-DD 00:00')
        let tglAkhirS = moment(itemJ.tglAkhirSewa).format('YYYY-MM-DD 23:59')

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/cek-jadwal-sewa-mitra?idmitra=${idMitra}&idaset=${itemJ.aset}&tglAwal=${tglAwalS}&tglAkhir=${tglAkhirS}`)
          .then((res) => {
            if (res.data.data.length > 0) {
              itemJ.dataJadwal = res.data.data
            } else {
              toast.info(`Jadwal tersedia diantara tgl ${tglAwalS} - ${tglAkhirS}`, {
                type: 'info',
                position: 'top-right',
                duration: '3000'
              })
            }
            btn.innerHTML = `Cek Jadwal`
          })
          .catch((err) => {
            toast.error('Terjadi Kesalahan Saat Menarik Data', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Cek Jadwal`
          })
      }

      function simpanJadwal(event) {
        if (itemJ.aset == undefined && itemJ.aset == 0) {
          toast.warning('Aset Gedung Belum Dipilih', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        if (itemJ.tglAwalSewa == undefined) {
          toast.warning('Tgl Awal Sewa Belum Diisi', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        if (itemJ.tglAkhirSewa == undefined) {
          toast.warning('Tgl Akhir Sewa Belum Diisi', {
              type: 'warning',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let tglAwalS = moment(itemJ.tglAwalSewa).format('YYYY-MM-DD HH:mm')
        let tglAkhirS = moment(itemJ.tglAkhirSewa).format('YYYY-MM-DD HH:mm')

        let jsonSave = {
          idmitra: idMitra,
          idaset: itemJ.aset,
          statuspesanan: 6,
          tglawalsewa: tglAwalS,
          tglakhirsewa: tglAkhirS
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post(`api/pengelola/save-jadwal-sewa-manual`, jsonSave)
          .then((res) => {
            toast.info(res.data.message, {
              type: 'info',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
          })
          .catch((err) => {
            toast.error(res.data.message, {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
          })
      }

      function gantiJadwalSewa(row) {
        if (row == undefined) {
          toast.warning('Data belum terload', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        itemJ.aset = undefined
        itemJ.tglAwalSewa = undefined
        itemJ.tglAkhirSewa = undefined

        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-sewa-byid?idsewa=${row.idpesanan}`)
          .then((res) => {
            let data = res.data.data
            itemJ.aset = data.idaset
            itemJ.tglAwalSewa = data.tglawalsewa
            itemJ.tglAkhirSewa = data.tglakhirsewa
          })
          .catch((err) => {
            toast.error('Data Tidak Ditemukan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function detailPembayaran(row) {
        if (row == undefined) {
          toast.warning('Data belum terload', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-pembayaran-sewa-byid?idsewa=${row.idpesanan}`)
          .then((res) => {
            isLoading.value = false
            item.dataDetailPembayaran = res.data.data
          })
          .catch((err) => {
            isLoading.value = false
            toast.error('Data Tidak Ditemukan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function detailPesanan(row) {
        if (row == undefined) {
          toast.warning('Data belum terload', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        isLoading.value = true
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/pengelola/get-data-pesanan-tambahan?idsewa=${row.idpesanan}`)
          .then((res) => {
            isLoading.value = false
            item.dataDetailPesanan = res.data.data
          })
          .catch((err) => {
            isLoading.value = false
            toast.error('Data Tidak Ditemukan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function batalPesanan(event, row) {
        if (row == undefined) {
          toast.error('Data belum terload', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          return
        }

        let jsonSave = {
          idpesanan: row.idpesanan,
          statuspesanan: 5
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post('api/pengelola/batal-pesanan-sewa', jsonSave)
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
            toast.error('Terjadi Kesalahan Saat Membatalkan Pesanan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = event.target
          })
      }

      return {
        item,
        itemJ,
        cari,
        isLoading,
        cekJadwal,
        simpanJadwal,
        gantiJadwalSewa,
        detailPembayaran,
        detailPesanan,
        batalPesanan
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
              <li class="breadcrumb-item active" aria-current="page">Data Pesanan</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3">
                  <label class="form-label">No Pesanan</label>
                  <input v-model="item.nopesanan" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3">
                  <label class="form-label">Nama Pemesan</label>
                  <input v-model="item.namapemesan" type="text" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Tgl Awal</label>
                  <VueDatePicker v-model="item.tglawal"></VueDatePicker>
                </div>
                <div class="col-lg-2">
                  <label class="form-label">Tgl Akhir</label>
                  <VueDatePicker v-model="item.tglakhir"></VueDatePicker>
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
              <button type="button" class="btn btn-warning btn-sm me-2"  data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                <i class="bi bi-plus-circle-dotted"></i> Tambah Jadwal
              </button>
            </div>
            <div class="card-body">
              <data-table :rows="item.data" 
              :loading="isLoading" 
              @loadData="init" striped sn>
                <template #thead-sn>
                  <table-head>SN</table-head>
                </template>
                <template #thead>
                  <table-head>No Pesanan</table-head>
                  <table-head>Nama Pemesan</table-head>
                  <table-head>No Telp</table-head>
                  <table-head>Email</table-head>
                  <table-head>Alamat</table-head>
                  <table-head>Aset</table-head>
                  <table-head>Tgl Sewa</table-head>
                  <table-head>Total Tagihan</table-head>
                  <table-head>Total Dibayar</table-head>
                  <table-head>Sisa Tagihan</table-head>
                  <table-head>Tgl Jatuh Tempo</table-head>
                  <table-head>Status Pesanan</table-head>
                  <table-head/>
                </template>
                <template #tbody-sn="{sn}">
                  <table-head v-text="sn.toString().padStart(2, '0')"/>
                </template>
                <template #tbody="{row}">
                  <table-body v-text="row.nopesanan" />
                  <table-body v-text="row.namapemesan" />
                  <table-body v-text="row.notelp" />
                  <table-body v-text="row.email" />
                  <table-body v-text="row.alamat" />
                  <table-body v-text="row.namaaset" />
                  <table-body v-text="row.tglsewa" />
                  <table-body v-text="row.totaltagihan" />
                  <table-body v-text="row.totaldibayar" />
                  <table-body v-text="row.sisatagihan" />
                  <table-body v-text="row.tgljatuhtempo" />
                  <table-body v-text="row.namastatus" />
                  <table-body>
                    <button type="button" class="btn btn-info btn-sm me-2"  data-bs-toggle="modal" data-bs-target="#modalTambahJadwal" @click="gantiJadwalSewa(row)"><i class="bi bi-pen-fill"></i></button>
                    <button type="button" class="btn btn-info btn-sm me-2"  data-bs-toggle="modal" data-bs-target="#modalDetailPesanan" @click="detailPesanan(row)"><i class="bi bi-info-circle"></i></button>
                    <button type="button" class="btn btn-info btn-sm me-2"  data-bs-toggle="modal" data-bs-target="#modalDetailPembayaran" @click="detailPembayaran(row)"><i class="bi bi-list-ul"></i></button>
                    <button type="button" class="btn btn-danger btn-sm" @click="batalPesanan($event, row)"><i class="bi bi-x-lg"></i></button>
                  </table-body>
                </template>
                <template #empty>
                  <TableBodyCell colspan="14">
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

  <!-- Start Modal Tambah Pesanan -->
  <div class="modal fade" data-bs-backdrop="static" id="modalTambahJadwal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Jadwal</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-3">
              <label class="form-label">Aset Gedung</label>
              <select v-model="itemJ.aset" class="form-select" aria-label="Default select example">
                <option value="0">Pilih Aset Gedung</option>
                <option v-for="list in item.listAset" :value="list.id">{{ list.namaaset }}</option>
              </select>
            </div>
            <div class="col-3">
              <label class="form-label">Tgl Awal</label>
              <VueDatePicker v-model="itemJ.tglAwalSewa"></VueDatePicker>
            </div>
            <div class="col-3">
              <label class="form-label">Tgl Akhir</label>
              <VueDatePicker v-model="itemJ.tglAkhirSewa"></VueDatePicker>
            </div>
            <div class="col-3">
              <label class="form-label">#</label><br>
              <button type="button" class="btn btn-info btn-sm me-2" @click="cekJadwal($event)">Cek Jadwal</button>
              <button type="button" class="btn btn-warning btn-sm" @click="simpanJadwal($event)">Simpan</button>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <data-table :rows="itemJ.dataJadwal" 
              :loading="isLoading" 
              @loadData="init" striped sn>
                <template #thead-sn>
                  <table-head>SN</table-head>
                </template>
                <template #thead>
                  <table-head>No Pesanan</table-head>
                  <table-head>Tgl Awal Sewa</table-head>
                  <table-head>Tgl Akhir Sewa</table-head>
                  <table-head/>
                </template>
                <template #tbody-sn="{sn}">
                  <table-head v-text="sn.toString().padStart(2, '0')"/>
                </template>
                <template #tbody="{row}">
                  <table-body v-text="row.nosewa" />
                  <table-body v-text="row.tglawalsewa" />
                  <table-body v-text="row.tglakhirsewa" />
                  <table-body />
                </template>
                <template #empty>
                  <TableBodyCell colspan="4">
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
  <!-- End Modal Tambah Pesanan -->

  <!-- Start Modal Detail Pesanan -->
  <div class="modal fade" data-bs-backdrop="static" id="modalDetailPesanan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Pesanan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <data-table :rows="item.dataDetailPesanan" 
          :loading="isLoading" 
          @loadData="init" striped sn>
            <template #thead-sn>
              <table-head>SN</table-head>
            </template>
            <template #thead>
              <table-head>Pesanan</table-head>
              <table-head>Harga</table-head>
              <table-head>Jumlah</table-head>
              <table-head/>
            </template>
            <template #tbody-sn="{sn}">
              <table-head v-text="sn.toString().padStart(2, '0')"/>
            </template>
            <template #tbody="{row}">
              <table-body v-text="row.namatambahan" />
              <table-body v-text="row.hargatambahan" />
              <table-body v-text="row.jumlah" />
              <table-body />
            </template>
            <template #empty>
              <TableBodyCell colspan="4">
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
  <!-- End Modal Detail Pesanan -->

  <!-- Start Modal Detail Pembayaran -->
  <div class="modal fade" data-bs-backdrop="static" id="modalDetailPembayaran" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Pembayaran</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <data-table :rows="item.dataDetailPembayaran" 
          :loading="isLoading" 
          @loadData="init" striped sn>
            <template #thead-sn>
              <table-head>SN</table-head>
            </template>
            <template #thead>
              <table-head>Tgl Bayar</table-head>
              <table-head>Total Dibayar</table-head>
              <table-head/>
            </template>
            <template #tbody-sn="{sn}">
              <table-head v-text="sn.toString().padStart(2, '0')"/>
            </template>
            <template #tbody="{row}">
              <table-body v-text="row.tglbayar" />
              <table-body v-text="row.totalbayar" />
              <table-body />
            </template>
            <template #empty>
              <TableBodyCell colspan="3">
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
  <!-- End Modal Detail Pembayaran -->
</template>