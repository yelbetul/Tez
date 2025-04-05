<template>
  <div class="container">
    <img src="../assets/logo.svg" alt="İş Kazaları Analizi Logo">
    <router-link to="/admin/login">Giriş Yap</router-link>
  </div>
</template>

<script>
  export default {
    
  }
</script>

<style scoped>
.container{
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center; align-items: center;
}
img{
  margin-bottom: 40px;
}
</style>