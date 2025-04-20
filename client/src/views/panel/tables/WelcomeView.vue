<template>
    <div class="welcome-container">
        <PageNavbar title="Tablolar" />
        <div class="go-to-page-container">
            <button @click.prevent="goToPage('/admin/tables/sector-codes/work-accidents')">
                <i class="fas fa-hard-hat"></i>
                <span>3.1.1 | Sektörlere Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/sector-codes/fatal-work-accidents')">
                <i class="fas fa-cross"></i>
                <span>3.1.2 | Sektörlere Göre Ölümlü İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/sector-codes/temporary-disability-days')">
                <i class="fas fa-wheelchair"></i>
                <span>3.1.3 | Sektörlere Göre Geçici İş Göremezlik Süreleri</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/provinces/work-accidents')">
                <i class="fas fa-map-marked-alt"></i>
                <span>3.1.4 | İllere Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/provinces/fatal-work-accidents')">
                <i class="fas fa-map-marked"></i>
                <span>3.1.5 | İllere Göre Ölümlü İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/provinces/temporary-disability-days')">
                <i class="fas fa-procedures"></i>
                <span>3.1.6 | İllere Göre Geçici İş Göremezlik Süreleri</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/provinces/disability-days')">
                <i class="fas fa-procedures"></i>
                <span>3.1.7 | İllere Göre Meslek Hastalığına Yakalananların Geçici İş Göremezlik Süreleri</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/ages/work-accidents')">
                <i class="fas fa-user-clock"></i>
                <span>3.1.8 | Yaşlara Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/ages/fatal-work-accidents')">
                <i class="fas fa-user-times"></i>
                <span>3.1.9 | Yaşlara Göre Ölümlü İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/diagnosis-groups/occupational-diasese')">
                <i class="fas fa-virus"></i>
                <span>3.1.10 | Tanılara Göre Meslek Hastalığına Yakalananlar</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/months/work-accidents')">
                <i class="fas fa-calendar-alt"></i>
                <span>3.1.11 | Aylara Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/months/temporary-disability-days')">
                <i class="fas fa-calendar-week"></i>
                <span>3.1.12 | Aylara Göre Geçici İş Göremezlik Süreleri</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/months/fatal-work-accidents')">
                <i class="fas fa-calendar-times"></i>
                <span>3.1.13 | Aylara Göre Ölümlü İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/occupations/work-accidents')">
                <i class="fas fa-user-tie"></i>
                <span>3.1.14 | Mesleklere Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/occupations/disease-fatalities')">
                <i class="fas fa-lungs-virus"></i>
                <span>3.1.15 | Mesleklere Göre Meslek Hastalığı</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/injury-types/work-accidents')">
                <i class="fas fa-band-aid"></i>
                <span>3.1.16 | Yaranın Türüne Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/injury-locations/work-accidents')">
                <i class="fas fa-user-injured"></i>
                <span>3.1.17 | Yaranın Vücuttaki Yerine Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/injury-causes/work-accidents')">
                <i class="fas fa-exclamation-triangle"></i>
                <span>3.1.18 | Yaralanmaya Sebep Olaya Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/general-activities/work-accidents')">
                <i class="fas fa-tools"></i>
                <span>3.1.19 | Genel Faaliyete Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/special-activities/work-accidents')">
                <i class="fas fa-toolbox"></i>
                <span>3.1.20 | Özel Faaliyete Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/deviations/work-accidents')">
                <i class="fas fa-random"></i>
                <span>3.1.21 | Sapmalara Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/materials/work-accidents')">
                <i class="fas fa-boxes"></i>
                <span>3.1.22 | Materyallere Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/environments/work-accidents')">
                <i class="fas fa-tree"></i>
                <span>3.1.23 | Çalışılan Çevreye Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/work-stations/work-accidents')">
                <i class="fas fa-warehouse"></i>
                <span>3.1.24 | Çalışılan Ortama Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/time-intervals/work-accidents')">
                <i class="fas fa-clock"></i>
                <span>3.1.25 | Çalışma Saatlerine Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/employee/work-accidents')">
                <i class="fas fa-users"></i>
                <span>3.1.26 | Çalışan Sayısına Göre İş Kazaları</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/employee/disease-fatalities')">
                <i class="fas fa-users"></i>
                <span>3.1.27 | Çalışan Sayısına Göre Meslek Hastalığı</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/employer-durations/disease-fatalities')">
                <i class="far fa-clock"></i>
                <span>3.1.28 | Çalışma Süresine Göre Meslek Hastalığı</span>
            </button>

            <button @click.prevent="goToPage('/admin/tables/employer-durations/work-accidents')">
                <i class="far fa-clock"></i>
                <span>3.1.29 | Çalışma Süresine Göre İş Kazaları</span>
            </button>

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
    created() {
        const is_logged_in = localStorage.getItem('is_logged_in') === 'true'
        if (!is_logged_in) this.$router.push('/admin/login')
        else this.initializeAuth()
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
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.go-to-page-container button {
    height: 120px;
    background-color: var(--main-color);
    color: var(--text-white);
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.go-to-page-container button::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.go-to-page-container button:hover::after {
    opacity: 1;
}

.go-to-page-container button:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.go-to-page-container button i {
    font-size: 2rem;
    margin-bottom: 12px;
    color: var(--second-color);
}

.go-to-page-container button span {
    display: block;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .go-to-page-container {
        grid-template-columns: 1fr;
    }

    .welcome-container {
        padding: 1rem;
    }

    .go-to-page-container button {
        height: 100px;
    }
}
</style>