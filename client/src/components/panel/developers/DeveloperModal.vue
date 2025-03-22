<template>
    <div v-if="visible" class="modal-overlay" @click.self="closeModal">
        <div class="developers-modal">
            <div class="close" @click="closeModal">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h2>Geliştirici {{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</h2>
            <form class="form" @submit.prevent="submitForm">
                <div v-if="error" class="form-error">
                    <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
                </div>
                <div class="form-element">
                    <label for="name_surname">Ad Soyad</label>
                    <input type="text" id="name_surname" v-model="formData.name_surname"
                        placeholder="Ad soyad giriniz...">
                </div>
                <div class="form-element">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" v-model="formData.username" placeholder="Kullanıcı adı giriniz...">
                </div>
                <div class="form-element password-field">
                    <label for="password">Parola</label>
                    <input :type="showPassword ? 'text' : 'password'" id="password" placeholder="Parola giriniz..."
                        v-model="formData.password" />
                    <i v-if="showPassword" class="fa-regular fa-eye" @click="togglePasswordVisibility"></i>
                    <i v-else class="fa-regular fa-eye-slash" @click="togglePasswordVisibility"></i>
                </div>
                <div class="form-element password-field">
                    <label for="confirmPassword">Parola Tekrar</label>
                    <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword"
                        placeholder="Parolayı tekrar giriniz..." v-model="formData.confirm_password" />
                    <i v-if="showConfirmPassword" class="fa-regular fa-eye"
                        @click="toggleConfirmPasswordVisibility"></i>
                    <i v-else class="fa-regular fa-eye-slash" @click="toggleConfirmPasswordVisibility"></i>
                </div>
                <div class="form-button">
                    <button type="submit">{{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    props: {
        visible: {
            type: Boolean,
            required: true
        },
        state: {
            type: String,
            required: true
        },
        data: {
            type: Object,
            required: false
        }
    },
    watch: {
        data(newData) {
            if (newData) {
                this.formData = { ...newData };
            }
        }
    },
    data() {
        return {
            formData: this.data ? { ...this.data } : {
                name_surname: '',
                username: '',
                password: '',
                confirm_password: ''
            },
            showPassword: false,
            showConfirmPassword: false,
            error: false,
            errorMessage: null
        };
    },
    methods: {
        updateState() {
            this.$emit('update');
        },
        submitForm() {
            if(this.state == 'new'){
                axios.post('https://iskazalarianaliz.com/api/admin/store', this.formData)
                    .then(res => {
                        if (!res.data.success) {
                            this.error = true
                            this.errorMessage = res.data.message
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Kayıt Oluşturuldu !',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                this.closeModal();
                            });
                        }
                    })
            }else{
                axios.put('https://iskazalarianaliz.com/api/admin/' + this.formData.id , this.formData)
                    .then(res => {
                        if (!res.data.success) {
                            this.error = true
                            this.errorMessage = res.data.message
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Kayıt Güncellendi !',
                                confirmButtonText: 'Tamam'
                            }).then(() => {
                                this.closeModal();
                            });
                        }
                    })
            }
        },
        closeModal(){
            this.$emit('close');
        },
        togglePasswordVisibility() {
            this.showPassword = !this.showPassword;
        },
        toggleConfirmPasswordVisibility() {
            this.showConfirmPassword = !this.showConfirmPassword;
        }
    }
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(10px);
}

.developers-modal {
    background-color: var(--panel-bg);
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    padding: 40px;
    width: 100%;
    max-width: 750px;
    z-index: 4500;
    position: relative;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.close {
    position: absolute;
    top: 15px;
    right: 20px;
    cursor: pointer;
    color: var(--penn-red);
    font-size: 1.8rem;
    transition: color 0.3s;
}

.close:hover {
    color: #c0392b;
}

h2 {
    margin: 0 0 25px;
    color: var(--main-color);
    font-size: 1.6rem;
    text-align: center;
}

.form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.form-element {
    margin-bottom: 20px;
    width: 48%;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="password"],
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #dcdcdc;
    border-radius: 8px;
    transition: border-color 0.3s;
    appearance: none;
    -webkit-appearance: none;
    font-family: "Poppins", sans-serif;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
input[type="password"]:focus,
select:focus {
    border-color: var(--main-color);
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="password"],
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 30px;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
input[type="password"]:focus,
select:focus {
    outline: none;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="password"],
select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
    font-family: "Poppins", sans-serif;
    -webkit-user-select: none;
    user-select: none;
}

select::-webkit-scrollbar {
    display: none;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="password"],
select {
    padding: 12px 15px;
}
.password-field {
    position: relative;
}

.password-field i {
    position: absolute;
    top: 70%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 1.2rem;
    transition: color 0.3s;
}

.password-field i:hover {
    color: var(--second-color);
}
.form-button {
    width: 100%;
    text-align: center;
}

button {
    background-color: var(--main-color);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 1.2rem;
    width: 100%;
}

.form-error {
    width: 100%;
    padding: 10px 20px;
    background-color: var(--main-color);
    color: white;
    border-radius: 10px;
    margin-bottom: 4%;
}

@media (max-width: 480px) {
    .general-activities-modal {
        max-width: 90%;
        height: 92vh;
    }

    h2 {
        font-size: 1.4rem;
    }

    .form-element {
        margin-bottom: 30px;
    }
}
</style>