<template>
    <div class="welcome-container">
        <PageNavbar title="Tablolar"/>
        <div class="go-to-page-container">
            <button @click.prevent="goToPage('/admin/tables/sector-codes/work-accidents')">Sektörlere Göre İş Kazaları</button>
            <button @click.prevent="goToPage('/admin/tables/sector-codes/fatal-work-accidents')">Sektörlere Göre Ölümlü İş Kazaları</button>
            <button @click.prevent="goToPage('/admin/tables/sector-codes/temporary-disability-days')">Sektörlere Göre Geçici İş Göremezlik Süreleri</button>
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
    data() {
        return {
        }
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
.welcome-container {
    width: 100%;
    min-height: 100vh;
    padding: 2% 3%;
    background-color: var(--panel-main-bg);
}

.go-to-page-container {
    min-height: 80vh;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-items: center;
}

.go-to-page-container button {
    width: 40%;
    min-height: 12vh;
    background-color: var(--main-color);
    color: var(--text-white);
    border: none;
    padding: 8px 0;
    border-radius: 10px;
    font-size: 1.3rem;
    cursor: pointer;
    transition: all .3s ease;
    display: flex;
    justify-content: center;
    align-items: center;
}

.go-to-page-container button i {
    margin-right: 24px;
    font-size: 1.7rem;
}

.go-to-page-container button:hover {
    background-color: var(--main-color);
    color: var(--second-color);
}
</style>