<template>
    <div class="developer-container">

        <PageNavbar title="Geliştiriciler" />

        <div class="developer-container__content">
            <div class="add-developer-button">
                <span @click="newDeveloper"><i class="fa-solid fa-plus"></i> Geliştirici Ekle</span>
            </div>
            <div  class="developers-card-container">
                <div v-for="item in admins" :key="item.id" class="developer-card">
                    <div class="icon">
                        <div class="fa-solid fa-user-secret"></div>
                    </div>
                    <h2>{{ item.name_surname }}</h2>
                    <p>{{ item.username }}</p>
                    <div class="buttons">
                        <span @click="updateDeveloper(item)"><i class="fa-solid fa-pen-fancy"></i> Güncelle</span>
                        <span @click="deleteDeveloper(item.id)"><i class="fa-solid fa-trash-can"></i> Sil</span>
                    </div>
                </div>
            </div>
        </div>


        <DeveloperModal :visible="modal_visible" :state="state" :data="selected_developer" @close="closeModal" :key="key" />

    </div>
</template>

<script>
import DeveloperModal from '@/components/panel/developers/DeveloperModal.vue';
import PageNavbar from '@/components/panel/NavbarPage.vue';
import { useAuthStore } from '@/stores/AuthStore';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    components: {
        PageNavbar,
        DeveloperModal
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    data() {
        return {
            modal_visible: false,
            state: 'new',
            admins: [],
            selected_developer: null,
            key: 0
        }
    },
    methods: {
        newDeveloper() {
            this.state = 'new'
            this.selected_developer = null;
            this.modal_visible = true
        },
        updateDeveloper(item) {
            this.state = 'update'
            this.selected_developer = { ...item }; 
            this.modal_visible = true
        },
        closeModal() {
            this.state = 'new'
            this.selected_developer = null;
            this.modal_visible = false
            this.key += 1
            this.initializeAuth()
        },
        async initializeAuth() {
            await this.authStore.fetchAuthData()

            axios.get('https://iskazalarianaliz.com/api/admin')
                .then(res => {
                    if (res.data.success) {
                        this.admins = res.data.admins
                    }
                })
        },

        deleteDeveloper(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu işlemi geri alamazsınız!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios
                        .delete('https://iskazalarianaliz.com/api/admin/' + id)
                        .then((res) => {
                            if (res.data.success) {
                                this.initializeAuth()
                                Swal.fire({
                                    title: 'Silindi!',
                                    text: 'Kayıt başarıyla silindi.',
                                    icon: 'success',
                                    confirmButtonColor: '#306B34',
                                })
                            }
                        })
                        .catch((error) => {
                            console.error('Silme işlemi sırasında hata oluştu:', error)
                        })
                }
            })
        },
    },
    created() {
        const is_logged_in = localStorage.getItem('is_logged_in') === 'true'

        if (!is_logged_in) {
            this.$router.push('/admin/login')
            return
        }

        this.initializeAuth()
    }
}
</script>

<style scoped>
.developer-container {
    width: 100%;
    min-height: 100vh;
    padding: 2% 3%;
    background-color: var(--panel-bg);
}
.developer-container__content{
    width: 100%;
    min-height: 80vh;
}
.add-developer-button{
    width: 100%;
    display: flex; justify-content: center; align-items: center;
    margin-top: 2%;
}
.add-developer-button span{
    width: 20%;
    color: var(--main-color);
    border: 1px solid var(--main-color);
    border-radius: 10px;
    padding: .5% 0;
    display: flex; justify-content: center; align-items: center;
    cursor: pointer;
    transition: all .2s ease;
}
.add-developer-button span i{
    font-size: 1.4rem;
    margin-right: 10px;
}

.add-developer-button span:hover {
    background-color: var(--main-color);
    color: var(--second-color);
}
.developers-card-container {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-top: 20px;
}

.developer-card {
    width: 25%;
    background: var(--panel-bg);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.icon {
    width: 60px;
    height: 60px;
    background: var(--main-color);
    color: var(--second-color);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 24px;
    margin-bottom: 10px;
}

h2 {
    font-size: 1.2rem;
    color: var(--text-color);
    margin-bottom: 10px;
}

.buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.buttons span {
    background: var(--main-color);
    color: var(--second-color);
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.3s ease-in-out;
    display: flex;
    align-items: center;
}

.buttons span i {
    margin-right: 5px;
}

.buttons span:hover {
    background: var(--text-color);
    color: var(--main-color);
}
</style>