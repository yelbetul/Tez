<template>
    <div class="welcome-container">
        <PageNavbar title="İlgili Gruplar"/>
        <div class="go-to-page-container">
            <button @click.prevent="goToPage('/admin/groups/sector-codes')">Sektör Kodları</button>
            <button @click.prevent="goToPage('/admin/groups/occupation-groups')">Meslek Grupları</button>
            <button @click.prevent="goToPage('/admin/groups/province-codes')">İl Kodları</button>
            <button @click.prevent="goToPage('/admin/groups/months')">Aylar</button>
            <button @click.prevent="goToPage('/admin/groups/ages')">Yaşlar</button>
            <button @click.prevent="goToPage('/admin/groups/diagnosis')">Tanılar</button>
            <button @click.prevent="goToPage('/admin/groups/injury-types')">Yara Türleri</button>
            <button @click.prevent="goToPage('/admin/groups/injury-locations')">Yaranın Vücuttaki Yeri</button>
            <button @click.prevent="goToPage('/admin/groups/injury-causes')">Yaralanma Nedenleri</button>
            <button @click.prevent="goToPage('/admin/groups/general-activities')">Genel Faaliyet</button>
            <button @click.prevent="goToPage('/admin/groups/special-activities')">Özel Faaliyet</button>
            <button @click.prevent="goToPage('/admin/groups/deviations')">Sapmalar</button>
            <button @click.prevent="goToPage('/admin/groups/materials')">Materyaller</button>
            <button @click.prevent="goToPage('/admin/groups/work-environments')">Çalışılan Çevre</button>
            <button @click.prevent="goToPage('/admin/groups/work-stations')">Çalışma Ortamları</button>
            <button @click.prevent="goToPage('/admin/groups/work-times')">Çalışma Saatleri</button>
            <button @click.prevent="goToPage('/admin/groups/employee-groups')">Çalışma Grupları</button>
            <button @click.prevent="goToPage('/admin/groups/employee-times')">Çalışma Süreleri</button>
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
    min-height: 100vh;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
}

.go-to-page-container button {
    width: 30%;
    min-height: 12vh;
    background-color: var(--main-color);
    color: var(--text-white);
    border: none;
    padding: 8px 0;
    border-radius: 10px;
    font-size: 1.7rem;
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