<template>
    <div class="welcome-container">

        <PageNavbar title="Yönetim Paneli" />

        <div class="go-to-page-container">
            <button @click.prevent="goToPage('/admin/groups')"><i class="fa-solid fa-list-ol"></i>İlgili
                Gruplar</button>
            <button @click.prevent="goToPage('/admin/tables')"><i class="fa-solid fa-database"></i>Tablolar</button>
            <button @click.prevent="goToPage('/admin/developers')"><i
                    class="fa-solid fa-user-secret"></i>Geliştiriciler</button>
        </div>
    </div>
</template>

<script>
import PageNavbar from '@/components/panel/NavbarPage.vue';
import { useAuthStore } from '@/stores/AuthStore';

export default {
    components: {
        PageNavbar
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    methods: {
        goToPage(route) {
            this.$router.push(route); 
        },
        async initializeAuth() {
            await this.authStore.fetchAuthData()
        },
    },
    created(){
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
.welcome-container{
    width: 100%;
    height: 100vh;
    padding: 2% 3%;
    background-color: var(--pane-main-bg);
}

.go-to-page-container{
    height: 70vh;
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.go-to-page-container button {
    width: 30%;
    height: 25vh;
    background-color: var(--main-color);
    color: var(--text-white);
    border: none;
    padding: 8px 0;
    border-radius: 10px;
    font-size: 2.4rem;
    cursor: pointer;
    transition: all .3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
}
.go-to-page-container button i{
    margin-right: 24px;
    font-size: 2.8rem;
}
.go-to-page-container button:hover {
    background-color: var(--main-color);
    color: var(--second-color);
}
</style>