<script setup>
import HeaderComponent from '../../components/pengelolaGedung/NavbarComponent.vue'
import FooterComponent from '../../components/pengelolaGedung/FooterComponent.vue'
</script>

<template>
  <div>
    <HeaderComponent />
    <div class="jumbotron">
      <div class="container">
        <div class="row">
          <div class="col-12 title-jumbotron text-light text-center">
            <h3>
              Bermitra dengan Kami Platform Sewa Gedung Serbaguna <br>
              Dapat Membuat Konsumen Semakin Dekat
            </h3>
            <router-link to="/partnership/register" class="btn btn-info text-light"><i class="bi bi-arrow-right"></i> Bermintra</router-link>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-evenly m-5">
      <div class="col-lg-4 text-center">
        O'Partnership adalah singkatan dari Online Multipurpose Building Rental Agency Partnership. Ini adalah sistem ekstranet yang dibangun oleh Kami untuk memungkinkan pemilik aset atau mitra mengelola properti. Pemilik properti dapat mengakses O'Partnership melalui web desktop dan aplikasi seluler
      </div>
      <div class="col-lg-4 text-center">
        Masukan Aset Anda dan jelaskan keunggulan Aset Anda kepada jutaan konsumen. Kami juga memungkinkan Anda mengelola Aset Anda dengan cara yang mudah, Tidak ada kerumitan dan tidak ada keributan.
      </div>
    </div>
    <FooterComponent />
  </div>
</template>

<style scoped>
  .jumbotron {
    width: 100vw;
    height: 100vh;
    background-image: url('https://source.unsplash.com/1200x400?meeting room');
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
    display: flex;
  }

  .jumbotron::before {
    content: '';
    width: 100%;
    height: 100%;
    background-image: linear-gradient(rgba(0,0,0,0),rgba(0,0,0,1));
    position: absolute;
    top: 0;
    z-index: 0;
  }

  .jumbotron .container {
    margin: auto;
  } 

  .title-jumbotron {
    position: relative;
    z-index: 1;
  }

  .title-jumbotron h3 {
    text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
  }
</style>