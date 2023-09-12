<script>
  import { RouterLink, useRouter, useRoute } from 'vue-router'
  import { reactive, ref, onMounted, defineComponent  } from 'vue'
  import { useToast } from 'vue-toastification'
  import axios from 'axios'
  import { DataTable, TableBody, TableHead, TableBodyCell } from '@jobinsjp/vue3-datatable'
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
    setup() {
      const token = localStorage.getItem('token')
      const idAset = localStorage.getItem('idaset')
      const idMitra = localStorage.getItem('idmitra')
      const emailUser = localStorage.getItem('emailuser')
      const toast = useToast()
      const router = useRouter()
      const isLoading = ref(false)
      const item = reactive({
        images: [],
        fileImages: [],
        isTambahan: false,
        dataFasilitas: []
      })

      onMounted(() => {
        init()
      })

      function init() {
        if (idAset != undefined) {

        }
      }

      function handleFileUpload(event) {
        let images = event.target.files
        for (let i = 0;i < images.length; i++) {
          let reader = new FileReader()
          reader.onload = (e) => {
            item.images.push({
              file: images[i],
              previewUrl: e.target.result
            })
          }
          reader.readAsDataURL(images[i])
          item.fileImages.push(images[i])
        }
      }

      function removeImage(index) {
        item.images.splice(index, 1)
      }

      function getCeckTambahan() {
        item.isTambahan = !item.isTambahan
      }

      function tambahFasilitas() {
        if (item.namaTambahan == undefined) {
          toast.warning('Nama Fasilitas belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.hargaTambahan == undefined) {
          toast.warning('Harga Fasilitas belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.hargaTambahan == undefined) {
          toast.warning('Status Fasilitas belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.tersedia == undefined) {
          toast.warning('Jumlah Tersedia belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        let no = 0;
        if ( item.dataFasilitas.length > 0 ) {
          no = item.dataFasilitas.length + 1
        } else {
          no = 1
        }

        let data = {
          no: no,
          namafasilitas: item.namaTambahan,
          hargafasilitas: item.hargaTambahan,
          statusfasilitas: item.statusTambahan,
          tersedia: item.tersedia,
        }

        item.dataFasilitas.push(data)

        kosongkanTambahan()
      }

      function kosongkanTambahan() {
        item.namaTambahan = undefined
        item.hargaTambahan = undefined
        item.statusTambahan = undefined
        item.tersedia = 0
      }

      function hapusTambahan(no) {
        for (let i in item.dataFasilitas) {
          if (item.dataFasilitas[i].no == no ) {
            item.dataFasilitas.splice(i, 1)
          }
        }
      }

      function getDiskon() {
        item.hargaDiskon = 0
        if (item.hargaSewa == undefined || item.hargaSewa == 0) {
          item.persenDiskon = 0
          item.hargaDiskon = 0
          return
        }

        let hargaDiskon = 0

        hargaDiskon = Math.round(parseFloat(item.hargaSewa) * parseFloat(item.persenDiskon)) / 100
        item.hargaDiskon = hargaDiskon
      }

      function setDiskonNol() {
        if (item.hargaSewa != undefined) {
          item.persenDiskon = 0
          item.hargaDiskon = 0
        }
      }

      function batal() {
        item.images = []
        item.dataFasilitas = []
        item.namaAset = undefined
        item.deskripsi = undefined
        item.hargaSewa = undefined
        item.persenDiskon = undefined
        item.hargaDiskon = undefined
        item.ceklistTambahan = false
        getCeckTambahan()
      }

      function simpan(event) {
        let idAsetSave = ''
        if (idAset != undefined) {
          idAsetSave = idAset
        }

        if (item.namaAset == undefined) {
          toast.warning('Nama Aset belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.deskripsi == undefined) {
          toast.warning('Deskripsi belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.hargaSewa == undefined) {
          toast.warning('Harga Sewa belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.maxJam == undefined) {
          toast.warning('Maksimal Jam Penyewaan Sewa belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        if (item.images.length <= 0) {
          toast.warning('Gambar belum diisi', {
            type: 'warning',
            position: 'top-right',
            duration: '3000'
          })
          return
        }

        let jsonSave = {
          id_aset: idAsetSave,
          id_mitra: idMitra,
          namaaset: item.namaAset,
          deskripsi: item.deskripsi,
          hargasewa: item.hargaSewa,
          persendiskon: item.persenDiskon,
          hargadiskon: item.hargaDiskon,
          maxjamsewa: item.maxJam,
          kapasitas: item.kapasitas,
          statusaset: 1,
          fasilitastambaan: item.dataFasilitas
        }

        let btn = event.target
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        `
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        axios.post(`api/pengelola/save-data-aset`, jsonSave)
          .then((res) => {
            toast.success(res.data.message, {
              type: 'success',
              position: 'top-right',
              duration: '3000'
            })
            if (res.data.success) {
              const formData = new FormData()
              Array.from(item.fileImages).forEach(image => {
                formData.append('images[]', image)
              })
              formData.append('idaset', res.data.data.id)
              formData.append('emailuser', emailUser)

              axios.defaults.headers.common.Authorization = `Bearer ${token}`
              axios.post('api/pengelola/simpan-gambar-aset', formData)
                .then((resp) => {
                  if (resp.data.success) {
                    toast.success(resp.data.message, {
                      type: 'success',
                      position: 'top-right',
                      duration: '3000'
                    })
                  } 
                  btn.innerHTML = 'Simpan'
                  batal()
                })
                .catch((err) => {
                  toast.error('Terjadi Kesalahan Saat Simpan Gambar', {
                    type: 'error',
                    position: 'top-right',
                    duration: '3000'
                  })
                  btn.innerHTML = 'Simpan'
                })
            }
          })
          .catch((err) => {
            toast.error('Simpan Gagal! Cek Kembali Data', {
              type: 'error',
              position: 'top-right',
              duration: '3000'
            })
            btn.innerHTML = `Simpan`
          })
      }

      return {
        item,
        init,
        handleFileUpload,
        removeImage,
        getCeckTambahan,
        tambahFasilitas,
        hapusTambahan,
        getDiskon,
        setDiskonNol,
        batal,
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
              <li class="breadcrumb-item"><router-link to="/partnership/dashboard/kelola-aset" class="text-warning text-decoration-none">Kelola Aset</router-link></li>
              <li class="breadcrumb-item active" aria-current="page">Tambah Aset</li>
            </ol>
          </nav>
          <div class="card mb-3">
            <div class="card-header">
              Detail Aset
            </div>
            <div class="card-body">
              <div class="mb-3">
                <div class="image-row">
                  <label for="select-image" class="form-label label-gambar">Pilih Gambar</label>
                  <input type="file" id="select-image" multiple @change="handleFileUpload($event)">
                  <div class="image-container">
                    <div v-for="(image, index) in item.images" :key="index" class="image-item">
                      <img :src="image.previewUrl" alt="Preview" class="profile-pic">
                      <button class="remove-button" @click="removeImage(index)">X</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Nama Aset</label>
                <input v-model="item.namaAset" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea v-model="item.deskripsi" cols="6" rows="4" class="form-control" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Harga Sewa</label>
                <input v-model="item.hargaSewa" type="text" class="form-control" required @change="setDiskonNol()">
              </div>
              <div class="mb-3">
                <label class="form-label">Harga Diskon (%)</label>
                <div class="row">
                  <div class="col-2">
                    <input v-model="item.persenDiskon" type="text" class="form-control" @keyup="getDiskon()">
                  </div>
                  <div class="col-10">
                    <input v-model="item.hargaDiskon" type="text" class="form-control" disabled>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Jam Maksimal Sewa</label>
                <input v-model="item.maxJam" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Kapasitas (Orang)</label>
                <input v-model="item.kapasitas" type="text" class="form-control" required>
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
                          <label class="form-label">Nama Tambahan</label>
                          <input v-model="item.namaTambahan" type="text" class="form-control">
                        </div>
                        <div class="col-3">
                          <label class="form-label">Harga</label>
                          <input v-model="item.hargaTambahan" type="text" class="form-control">
                        </div>
                        <div class="col-2">
                          <label class="form-label">Tersedia</label>
                          <input v-model="item.tersedia" type="text" class="form-control">
                        </div>
                        <div class="col-1">
                          <br>
                          <input v-model="item.statusTambahan" class="form-check-input" type="checkbox">
                          <label class="form-check-label">Aktif</label>
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
</template>

<style scoped>
  .image-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 40vh;
  }

  input[type="file"] {
    display: none;
  }

  .label-gambar {
    padding: 10px 20px;
    background-color: #4285f4;
    color: #ffffff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
  }

  .label-gambar:hover {
    background-color: #3367d6;
  }

  .image-container {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  .image-item {
    position: relative;
    margin-right: 10px;
    margin-bottom: 10px;
  }

  .profile-pic {
    width: 180px;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
  }

  .remove-button {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 5px;
    background-color: #f44336;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
  }

  .remove-button:hover {
    background-color: #d32f2f;
  }
</style>