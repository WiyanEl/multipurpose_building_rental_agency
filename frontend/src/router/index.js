import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('../views/calonPenyewa/HomeView.vue')
    },
    {
      path: '/login',
      name: 'loginPenyewa',
      component: () => import('../views/calonPenyewa/LoginView.vue')
    },
    {
      path: '/registrasi',
      name: 'registrasiPenyewa',
      component: () => import('../views/calonPenyewa/RegistrasiView.vue')
    },
    {
      path: '/daftar-gedung/:lokasi/:kapasitas/:nMitra',
      name: 'daftarGedungMitra',
      component: () => import('../views/calonPenyewa/DaftarMitraView.vue')
    },
    {
      path: '/daftar-gedung/detail/:id',
      name: 'dataDetailMitra',
      component: () => import('../views/calonPenyewa/DaftarMitraDetailView.vue')
    },
    {
      path: '/daftar-gedung/detail/aset/:id',
      name: 'dataDetailAsetMitra',
      component: () => import('../views/calonPenyewa/DetailAsetMitraView.vue')
    },
    {
      path: '/partnership',
      name: 'partnership',
      component: () => import('../views/pengelolaGedung/HomeView.vue')
    },
    {
      path: '/partnership/register',
      name: 'partnershipRegister',
      component: () => import('../views/pengelolaGedung/RegistrasiView.vue')
    },
    {
      path: '/partnership/registrasi/:email',
      name: 'partnershipRegistrasi',
      component: () => import('../views/pengelolaGedung/RegistrasiAkunView.vue'),
    },
    {
      path: '/partnership/login',
      name: 'partnershipLogin',
      component: () => import('../views/pengelolaGedung/LoginView.vue')
    },
    {
      path: '/partnership/pendaftaran-mitra',
      name: 'partnershipPendaftaranMitra',
      component: () => import('../views/pengelolaGedung/PendaftaranMitraView.vue')
    },
    {
      path: '/partnership/dashboard',
      name: 'partnershipDashboard',
      component: () => import('../views/pengelolaGedung/DashboardMitraView.vue')
    },
    {
      path: '/partnership/dashboard/daftar-pesanan',
      name: 'partnershipDashboardDataPesanan',
      component: () => import('../views/pengelolaGedung/DashboardDataPesananView.vue')
    },
    {
      path: '/partnership/dashboard/kelola-aset',
      name: 'partnershipDashboardKelolaAset',
      component: () => import('../views/pengelolaGedung/DashboardKelolaAsetView.vue')
    },
    {
      path: '/partnership/dashboard/kelola-aset/tambah',
      name: 'partnershipDashboardTambahAset',
      component: () => import('../views/pengelolaGedung/DashboardTambahAsetView.vue')
    },
    {
      path: '/partnership/dashboard/kelola-diskon',
      name: 'partnershipDashboardKelolaDiskon',
      component: () => import('../views/pengelolaGedung/DashboardKelolaDiskonView.vue')
    },
    {
      path: '/partnership/dashboard/kelola-diskon/tambah',
      name: 'partnershipDashboardKelolaDiskonTambah',
      component: () => import('../views/pengelolaGedung/DashboardKelolaDiskonTambahView.vue')
    },
    {
      path: '/adminapp/login',
      name: 'adminAppLogin',
      component: () => import('../views/pengelolaAplikasi/LoginView.vue')
    },
    {
      path: '/adminapp/dashboard',
      name: 'adminAppDashboard',
      component: () => import('../views/pengelolaAplikasi/DashboardHomeView.vue')
    },
    {
      path: '/adminapp/dashboard/daftar-validasi-mitra',
      name: 'adminAppDashboardValidasiMitra',
      component: () => import('../views/pengelolaAplikasi/DashboardValidasiMitraView.vue')
    },
    {
      path: '/adminapp/dashboard/master-mitra',
      name: 'adminAppDashboardMasterMitra',
      component: () => import('../views/pengelolaAplikasi/DashboardMasterMitraView.vue')
    },
    {
      path: '/adminapp/dashboard/detail-validasi-mitra/:email',
      name: 'adminAppDashboardDetailValidasiMitra',
      component: () => import('../views/pengelolaAplikasi/DashboardValidasiMitraDetailView.vue')
    }
  ]
})

export default router
