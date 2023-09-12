<script>
  import FooterComponentVue from '../../components/calonPenyewa/FooterComponent.vue'
  import HeaderComponent from '../../components/calonPenyewa/HeaderComponent.vue'
  import VueDatePicker from '@vuepic/vue-datepicker'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import moment from 'moment'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { DataTable, TableBody, TableHead, TableBodyCell } from '@jobinsjp/vue3-datatable'

  export default {
    components: {
      HeaderComponent,
      FooterComponentVue,
      VueDatePicker,
      TableBody,
      TableHead,
      DataTable,
      TableBodyCell,
    },
    setup() {
      const token = localStorage.getItem('token')
      const emailUser = localStorage.getItem('emailuser')
      const idUser = localStorage.getItem('iduser')
      const roleUser = localStorage.getItem('roleuser')
      const toast = useToast()
      const route = useRoute()
      const router = useRouter()
      const isLoading = ref(false)
      const dateNow = new Date()
      const baseUrl = axios.defaults.baseURL
      const item = reactive({
        idAset: route.params.id,
        isTambahan: false,
        tombolSimpan: true,
        listGambar: [],
        listHarga: [],
        dataFasilitas: []
      })
      const itemJ = reactive({
        dataJadwal: [],
      })

      onMounted(() => {
        init()
      })

      function init() {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.get(`api/penyewa/get-data-aset-mitra-detail?idaset=${item.idAset}`)
          .then((res) => {
            let data = res.data.data[0]
            item.idMitra = data.idmitra
            item.namaAset = data.namaaset
            item.maxJamSewa = data.maxjamsewa
            item.kapasitas = data.kapasitas
            item.hargaSewa = data.hargasewa
            item.hargaSewaSetelahDiskon = parseFloat(data.hargasewa) - parseFloat(data.hargadiskon)
            item.hargaDiskon = data.hargadiskon
            item.deskripsi = data.deskripsi
            item.listGambar = data.asetgambar
            item.listHarga = data.hargatambahan
          })
          .catch((err) => {
            toast.error('Terjadi kesalahan', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
          })
      }

      function cekJadwal(event) {
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
        axios.get(`api/pengelola/cek-jadwal-sewa-mitra?idmitra=${item.idMitra}&idaset=${item.idAset}&tglAwal=${tglAwalS}&tglAkhir=${tglAkhirS}`)
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

      function getCeckTambahan() {
        item.isTambahan = !item.isTambahan
      }

      function pesan() {
        if (token == undefined) {
          toast.warning('Anda belum melakukan Login', {
            type: 'error',
            position: 'top-right',
            duration: '3000'
          })
          item.tombolSimpan = false
          return
        }
      }

      function getHargaTambahan(item) {
        console.log(item);
      }

      return {
        item,
        baseUrl,
        itemJ,
        cekJadwal,
        getCeckTambahan,
        pesan,
        getHargaTambahan
      }
    }
  }
</script>

<template>
  <div>
    <HeaderComponent />
    <div class="container mt-5 mb-5 listData">
      <div class="row mb-3">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="container">
                <div class="row">
                  <div v-for="list in item.listGambar" class="col-12 col-lg-3">
                    <img v-bind:src="baseUrl + list.path" class="img-thumbnail images" alt="Gambar Mitra">
                  </div>
                  <div class="col-lg-12 mt-3">
                    <div class="mb-3">
                      <h3>{{ item.namaAset }} ({{ item.kapasitas }} Orang)</h3>
                    </div>
                    <div v-if="item.hargaDiskon != null" class="card-subtitle text-muted">
                      <del>Rp. {{ item.hargaSewa }}</del>
                    </div>
                    <div v-if="item.hargaDiskon != null" class="card-text text-warning fs-3">
                      Rp. {{ item.hargaSewa - item.hargaDiskon }} / {{ item.maxJamSewa }} Jam
                    </div>
                    <div v-if="item.hargaDiskon == null" class="card-text text-warning fs-3">
                      Rp. {{ item.hargaSewa }} / {{ item.maxJamSewa }} Jam
                    </div>
                    <div class="card-subtitle card-muted">
                      <h5>{{ item.deskripsi }}</h5>
                    </div>
                  </div>
                  <div class="col-lg-12 mt-3">
                    <div class="card-title">
                      Fasilitas Tambahan :
                    </div>
                    <div class="row">
                      <div v-for="list in item.listHarga" class="col-12">
                        {{ list.namatambahan }}, Harga Rp. {{ list.harga }} Tersedia {{ list.stok }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-body">
              <div class="row mb-3">
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
                      <table-head>Keterangan</table-head>
                      <table-head>Tgl Awal Sewa</table-head>
                      <table-head>Tgl Akhir Sewa</table-head>
                      <table-head/>
                    </template>
                    <template #tbody-sn="{sn}">
                      <table-head v-text="sn.toString().padStart(2, '0')"/>
                    </template>
                    <template #tbody="{row}">
                      <table-body>Tidak Tersedia</table-body>
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
        <div class="row mt-3">
          <div class="col-12 text-end">
            <button type="button" class="btn btn-warning"  data-bs-toggle="modal" data-bs-target="#modalPesan" @click="pesan()">Pesan</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Modal Tambah Pesanan -->
    <div class="modal fade" data-bs-backdrop="static" id="modalPesan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Pesan Sewa</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-12 mb-3">
                <label class="form-label">Aset Gedung</label>
                <input v-model="item.namaAset" type="text" class="form-control" disabled>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Tgl Awal</label>
                <VueDatePicker v-model="itemJ.tglAwalSewa"></VueDatePicker>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Tgl Akhir</label>
                <VueDatePicker v-model="itemJ.tglAkhirSewa"></VueDatePicker>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Harga Sewa</label>
                <input v-model="item.hargaSewaSetelahDiskon" type="text" class="form-control" disabled>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Tota Tagihan</label>
                <input v-model="item.totalTagihan" type="text" class="form-control" disabled>
              </div>
              <div>
                <div class="form-check">
                  <input v-model="item.ceklistTambahan" class="form-check-input" type="checkbox" id="ceklistTambahan" @click="getCeckTambahan">
                  <label class="form-check-label" for="ceklistTambahan">Fasilitas Tambahan</label>
                </div>
              </div>
              <div v-if="item.isTambahan">
                <div class="mb-3">
                  <div class="row">
                    <div class="col-10">
                      <div class="row">
                        <div class="col-6">
                          <label class="form-label">Fasilitas</label>
                          <select v-model="item.listHarga" class="form-select" aria-label="Default select example" @change="getHargaTambahan(list)">
                            <option value="0">Pilih Fasilitas Gedung</option>
                            <option v-for="list in item.listHarga" :value="list.id">{{ list.namatambahan }}</option>
                          </select>
                        </div>
                        <div class="col-3">
                          <label class="form-label">Harga</label>
                          <input v-model="item.hargaTambahan" type="text" class="form-control" disabled>
                        </div>
                        <div class="col-2">
                          <label class="form-label">Qty</label>
                          <input v-model="item.qty" type="text" class="form-control">
                        </div>
                      </div>
                    </div>
                    <div class="col-2">
                      <br>
                      <button type="button" class="btn btn-sm btn-warning" @click="tambahFasilitas()"><i class="bi bi-plus-lg"></i></button>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <data-table :rows="item.dataFasilitas" 
                    @loadData="init" striped sn>
                    <template #thead-sn>
                      <table-head>SN</table-head>
                    </template>
                    <template #thead>
                      <table-head>Fasilitas</table-head>
                      <table-head>Harga</table-head>
                      <table-head>Status</table-head>
                      <table-head/>
                    </template>
                    <template #tbody-sn="{sn}">
                      <table-head v-text="sn.toString().padStart(2, '0')"/>
                    </template>
                    <template #tbody="{row}">
                      <table-body v-text="row.namafasilitas" />
                      <table-body v-text="row.hargafasilitas" />
                      <table-body v-text="row.statusfasilitas" />
                      <table-body>
                        <button type="button" class="btn btn-sm btn-danger" @click="hapusTambahan(row.no)"><i class="bi bi-trash"></i></button>
                      </table-body>
                    </template>
                    <template #empty>
                      <TableBodyCell colspan="5">
                        <div class="text-center">No record found.</div>
                      </TableBodyCell>
                    </template>
                  </data-table> 
                </div>
              </div>
              <div v-if="item.tombolSimpan" class="mb-3 text-end">
                <button type="button" class="btn btn-warning" @click="simpanPesanan()">Simpan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal Tambah Pesanan -->
    <FooterComponentVue />
  </div>
</template>

<style scoped>
  .listData {
    min-height: 100vh;
  }

  .images {
    height: 300px;
    width: 100%;
  }

</style>