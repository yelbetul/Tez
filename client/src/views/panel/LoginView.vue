<template>
    <div class="container">
        <div class="logo">
            <img src="../../assets/logo.svg" alt="">
        </div>
        <form class="login-form" @submit.prevent="login">
            <h2>Yönetim Paneli</h2>
            <div v-if="error" class="form-error">
                <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
            </div>
            <div class="login-form-element">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" id="username" v-model="login_data.username">
            </div>
            <div class="login-form-element password-field">
                <label for="password">Parola</label>
                <div class="input-wrapper">
                    <input :type="showPassword ? 'text' : 'password'" id="password" v-model="login_data.password" />
                    <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="toggle-password"
                        @click="togglePasswordVisibility"></i>
                </div>
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
            errorMessage: null,
            showPassword: false
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
        },
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword
        }
    }
}
</script>

<style scoped>
.container {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: space-around;
    align-items: center;
    background: linear-gradient(to right, #f0f4f8, #d9e2ec);
    padding: 20px;
}

.login-form {
    background-color: white;
    width: 100%;
    max-width: 450px;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.login-form:hover {
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.15);
}

.login-form h2 {
    margin-bottom: 30px;
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    color: var(--main-color);
}

.login-form-element {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.login-form-element label {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 5px;
    color: var(--main-color);
}

.login-form-element input {
    padding: 12px 16px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}

.login-form-element input:focus {
    border-color: var(--main-color);
    outline: none;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(0, 48, 73, 0.1);
}

.login-form-button {
    text-align: center;
    margin-top: 20px;
}

.login-form-button button {
    width: 100%;
    background: var(--main-color);
    border: none;
    padding: 12px 0;
    border-radius: 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.login-form-button button:hover {
    background: linear-gradient(135deg, var(--main-color), var(--second-color));
    transform: translateY(-2px);
}

.form-error {
    padding: 12px 16px;
    background-color: #ff1d35;
    color: white;
    border-radius: 10px;
    font-size: 0.95rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.password-field .input-wrapper {
    position: relative;
}

.password-field .input-wrapper input {
    width: 100%;
    padding-right: 45px;
    /* sağa boşluk bırak göz için */
}

.password-field .toggle-password {
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 1rem;
    transition: color 0.3s ease;
}

.password-field .toggle-password:hover {
    color: var(--main-color);
}
</style>