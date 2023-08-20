<script>
  import { RouterLink, useRouter } from 'vue-router'
  import { onMounted, ref } from 'vue'
  import axios from 'axios'

  export default {
    setup() {
      const token = localStorage.getItem('token')
      const roleuser = localStorage.getItem('roleuser')
      const router = useRouter()
      const user = ref('')

      function sidebarMenu() {
        let sidebarMenu = document.querySelector('.sidebar-menu')
        sidebarMenu.classList.toggle('active')
      }

      return {
        sidebarMenu,
        roleuser
      }
    },
  }
</script>

<template>
  <div class="sidebar-menu">
    <div class="logo-content">
      <div class="logo">
        <i class="bi bi-buildings"></i>
        <div class="logo_name">O'MBRA</div>
      </div>
      <i class="bi bi-list" id="btn_menu" @click="sidebarMenu"></i>
    </div>
    <ul class="nav" v-if="roleuser == 1">
      <li>
        <router-link to="/adminapp/dashboard">
          <i class="bi bi-layout-text-window-reverse"></i>
          <span class="link_name">Dashboard</span>
        </router-link>
        <span class="tooltip">Dashboard</span>
      </li>
      <li>
        <router-link to="/adminapp/dashboard/daftar-validasi-mitra">
          <i class="bi bi-person-check-fill"></i>
          <span class="link_name">Validasi Mitra</span>
        </router-link>
        <span class="tooltip">Validasi Mitra</span>
      </li>
      <li>
        <router-link to="/adminapp/dashboard/master-mitra">
          <i class="bi bi-person-check"></i>
          <span class="link_name">Master Mitra</span>
        </router-link>
        <span class="tooltip">Master Mitra</span>
      </li>
      <li>
        <router-link to="/adminapp/dashboard/master-user">
          <i class="bi bi-people"></i>
          <span class="link_name">Master User</span>
        </router-link>
        <span class="tooltip">Master User</span>
      </li>
      <li>
        <router-link to="/adminapp/dashboard/master-pesanan">
          <i class="bi bi-card-list"></i>
          <span class="link_name">Master Pesanan</span>
        </router-link>
        <span class="tooltip">Master Pesanan</span>
      </li>
    </ul>
    <ul class="nav" v-if="roleuser == 3">
      <li>
        <router-link to="/partnership/dashboard">
          <i class="bi bi-layout-text-window-reverse"></i>
          <span class="link_name">Dashboard</span>
        </router-link>
        <span class="tooltip">Dashboard</span>
      </li>
      <li>
        <router-link to="/partnership/dashboard/daftar-pesanan">
          <i class="bi bi-journal-check"></i>
          <span class="link_name">Daftar Pesanan</span>
        </router-link>
        <span class="tooltip">Daftar Pesanan</span>
      </li>
      <li>
        <router-link to="/partnership/dashboard/kelola-aset">
          <i class="bi bi-building-check"></i>
          <span class="link_name">Kelola Aset</span>
        </router-link>
        <span class="tooltip">Kelola Aset</span>
      </li>
      <li>
        <router-link to="/partnership/dashboard/kelola-diskon">
          <i class="bi bi-currency-exchange"></i>
          <span class="link_name">Kelola Diskon</span>
        </router-link>
        <span class="tooltip">Kelola Diskon</span>
      </li>
    </ul>
  </div>
</template>