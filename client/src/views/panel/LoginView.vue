<template>
    <div class="container">
        <form class="login-form" @submit.prevent="login">
            <h2>Giriş Ekranı</h2>
            <div v-if="error" class="form-error">
                <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
            </div>
            <div class="login-form-element">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" v-model="login_data.username">
            </div>
            <div class="login-form-element">
                <label for="password">Parola</label>
                <input type="password" id="password" v-model="login_data.password">
            </div>
            <div class="login-form-button">
                <button type="submit">Giriş Yap</button>
            </div>
        </form>
    </div>
</template>

<script>
import { useAuthStore } from '@/stores/AuthStore';
import axios from 'axios';

export default {
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    data() {
        return {
            login_data: {
                username: null,
                password: null
            },
            error: false,
            errorMessage: null
        }
    },
    methods: {
        login() {
            axios.post('https://iskazalarianaliz.com/api/admin/login', this.login_data)
            .then(res => {
                if(res.data.success){
                    localStorage.setItem('is_logged_in', true)
                    localStorage.setItem('id', res.data.admin.id)
                    localStorage.setItem('username', res.data.admin.username)
                    localStorage.setItem('name_surname', res.data.admin.name_surname)
                    this.authStore.fetchAuthData()
                    this.$router.push('/admin/welcome')
                }else{
                    this.error = true
                    this.errorMessage = res.data.message
                }
            })
        }
    }
}
</script>

<style scoped>
.container {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: var(--panel-bg);
}

.login-form {
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    width: 38%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 70vh;
    border-radius: 20px;
    padding: 0 20px;
}

.login-form h2 {
    margin-bottom: 20px;
    text-align: center;
    font-size: 2rem;
    font-weight: 600;
    color: var(--main-color);
}

.login-form-element {
    width: 100%;
    display: flex;
    flex-direction: column;
    margin-bottom: 6%;
}

.login-form-element label {
    color: var(--main-color);
    font-weight: 500;
    font-size: 1rem;
    margin-bottom: 1%;
}

.login-form-element input {
    border: 1px solid gray;
    border-radius: 10px;
    padding: 8px 18px;
    font-size: 1.2rem;
    font-family: 'Poppins', sans-serif;
    background-color: var(--panel-bg);
}

.login-form-button {
    width: 100%;
    text-align: center;
}

.login-form-button button {
    width: 50%;
    background-color: var(--second-color);
    border: 1px solid var(--main-color);
    color: var(--main-color);
    padding: 8px 0;
    border-radius: 10px;
    font-family: 'Poppins', sans-serif;
    font-size: 1.3rem;
    cursor: pointer;
    transition: all .3s ease;
}

.login-form-button button:hover{
    background-color: var(--main-color);
    color: var(--second-color);
}
.form-error {
    width: 100%;
    padding: 10px 20px;
    background-color: var(--main-color);
    color: white;
    border-radius: 10px;
    margin-bottom: 4%;
}
</style>