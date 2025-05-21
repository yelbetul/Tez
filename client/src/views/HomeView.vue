<template>
  <div class="dashboard-container">
    <!-- Üst Bilgi -->
    <HeaderApp :lastUpdate="lastUpdate" />


    <!-- Genel İstatistikler -->
    <section class="stats-overview">
      <div class="stat-card total">
        <h3>Toplam İş Kazası</h3>
        <p>{{ formatNumber(totalAccidents) }}</p>
      </div>
      <div class="stat-card fatal">
        <h3>Ölümlü İş Kazası</h3>
        <p>{{ formatNumber(fatalAccidents) }}</p>
      </div>
      <div class="stat-card disease">
        <h3>Meslek Hastalığı</h3>
        <p>{{ formatNumber(occupationalDiseases) }}</p>
      </div>
      <div class="stat-card avg-days">
        <h3>Ort. İş Göremezlik Süresi</h3>
        <p>{{ averageIncapacityDays }} gün</p>
      </div>
    </section>

    <!-- Analiz Kategorileri -->
    <section class="analysis-categories">
      <h2>Analiz Kategorileri</h2>
      <div class="category-grid">
        <router-link v-for="category in categories" :key="category.id" :to="category.route" class="category-card"
          :class="`cat-${category.id % 5}`">
          <div class="category-icon">
            <i :class="category.icon"></i>
          </div>
          <h3>{{ category.title }}</h3>
          <p>{{ category.description }}</p>
        </router-link>
      </div>
    </section>
    <AppProject />
    <!-- Son Güncelleme -->
    <FooterApp />
  </div>
</template>

<script setup>
import AppProject from '@/components/user/AppProject.vue'
import FooterApp from '@/components/user/FooterApp.vue'
import HeaderApp from '@/components/user/HeaderApp.vue'
import axios from 'axios'
import { ref, onMounted } from 'vue'

// Veri değişkenleri
const totalAccidents = ref(0)
const fatalAccidents = ref(0)
const occupationalDiseases = ref(0)
const averageIncapacityDays = ref(0)
const lastUpdate = ref(new Date().toLocaleDateString('tr-TR'))

// Kategoriler
const categories = ref([
  {
    id: 1,
    title: '3.1.1 | Sektörlere Göre İş Kazaları',
    description: 'İş kazalarının sektör bazında dağılımı ve karşılaştırması',
    icon: 'fas fa-industry',
    route: '/analysis/3.1.1'
  },
  {
    id: 2,
    title: '3.1.2 | Sektörlere Göre Ölümlü İş Kazaları',
    description: 'Sektörlerdeki ölümlü iş kazalarının analizi',
    icon: 'fas fa-skull-crossbones',
    route: '/analysis/3.1.2'
  },
  {
    id: 3,
    title: '3.1.3 | Sektörlere Göre Geçici İş Göremezlik',
    description: 'Sektörel bazda iş göremezlik sürelerinin değerlendirilmesi',
    icon: 'fas fa-procedures',
    route: '/analysis/3.1.3'
  },
  {
    id: 4,
    title: '3.1.4 | İllere Göre İş Kazaları',
    description: 'İş kazalarının coğrafi dağılım analizi',
    icon: 'fas fa-map-marked-alt',
    route: '/analysis/3.1.4'
  },
  {
    id: 5,
    title: '3.1.5 | İllere Göre Ölümlü İş Kazaları',
    description: 'Bölgesel ölümlü iş kazalarının yoğunluk analizi',
    icon: 'fas fa-map-marked',
    route: '/analysis/3.1.5'
  },
  {
    id: 6,
    title: '3.1.6 | İllere Göre Geçici İş Göremezlik',
    description: 'Bölgelere göre iş göremezlik sürelerinin kıyaslaması',
    icon: 'fas fa-map',
    route: '/analysis/3.1.6'
  },
  {
    id: 7,
    title: '3.1.7 | İllere Göre Meslek Hastalığı İş Göremezlik',
    description: 'Coğrafi bölgelere göre meslek hastalıkları kaynaklı iş göremezlik',
    icon: 'fas fa-disease',
    route: '/analysis/3.1.7'
  },
  {
    id: 8,
    title: '3.1.8 | Yaşlara Göre İş Kazaları',
    description: 'Yaş gruplarına göre iş kazası istatistikleri',
    icon: 'fas fa-user-friends',
    route: '/analysis/3.1.8'
  },
  {
    id: 9,
    title: '3.1.9 | Yaşlara Göre Ölümlü İş Kazaları',
    description: 'Yaş bazında ölümlü iş kazalarının dağılımı',
    icon: 'fas fa-user-times',
    route: '/analysis/3.1.9'
  },
  {
    id: 10,
    title: '3.1.10 | Tanı Gruplarına Göre Meslek Hastalıkları',
    description: 'Hastalık tanılarına göre meslek hastalıkları sınıflandırması',
    icon: 'fas fa-diagnoses',
    route: '/analysis/3.1.10'
  },
  {
    id: 11,
    title: '3.1.11 | Aylara Göre İş Kazaları',
    description: 'Mevsimsel iş kazası trendlerinin analizi',
    icon: 'fas fa-calendar-alt',
    route: '/analysis/3.1.11'
  },
  {
    id: 12,
    title: '3.1.12 | Aylara Göre Geçici İş Göremezlik',
    description: 'Aylık bazda iş göremezlik sürelerinin değişimi',
    icon: 'fas fa-calendar-week',
    route: '/analysis/3.1.12'
  },
  {
    id: 13,
    title: '3.1.13 | Aylara Göre Ölümlü İş Kazaları',
    description: 'Aylara göre ölümlü iş kazalarının yoğunluk analizi',
    icon: 'fas fa-calendar-times', // Takvim ve çarpı işareti (ölüm vurgusu)
    route: '/analysis/3.1.13'
  },
  {
    id: 14,
    title: '3.1.14 | Mesleklere Göre İş Kazaları',
    description: 'Mesleklere göre iş kazalarının yoğunluk analizi',
    icon: 'fas fa-hard-hat', // İşçi bareti
    route: '/analysis/3.1.14'
  },
  {
    id: 15,
    title: '3.1.15 | Mesleklere Göre Meslek Hastalığı Sonucu Ölümler',
    description: 'Mesleklere göre meslek hastalığı sonucu ölümlerin analizi',
    icon: 'fas fa-virus', // Virüs (hastalık temsili)
    route: '/analysis/3.1.15'
  },
  {
    id: 16,
    title: '3.1.16 | Yaralanma Türlerine Göre İş Kazaları',
    description: 'Yaralanma türlerine göre iş kazalarının yoğunluk analizi',
    icon: 'fas fa-lungs-virus', // Kırık kemik
    route: '/analysis/3.1.16'
  },
  {
    id: 17,
    title: '3.1.17 | Yaranın Vücuttaki Yerine Göre İş Kazaları',
    description: 'Yaranın vücuttaki yerine göre iş kazalarının yoğunluk analizi',
    icon: 'fas fa-user-injured', // Yaralı insan silüeti
    route: '/analysis/3.1.17'
  },
  {
    id: 18,
    title: '3.1.18 | Yaralanma Nedenlerine Göre İş Kazaları',
    description: 'Yaralanma nedenlerine göre iş kazalarının yoğunluk analizi',
    icon: 'fas fa-search-plus', // Büyüteç (neden analizi)
    route: '/analysis/3.1.18'
  },
  {
    id: 19,
    title: '3.1.19 | Genel Faaliyete Göre İş Kazaları',
    description: 'Genel faaliyet türlerine göre iş kazası analizi',
    icon: 'fas fa-industry',
    route: '/analysis/3.1.19'
  },
  {
    id: 20,
    title: '3.1.20 | Özel Faaliyete Göre İş Kazaları',
    description: 'Spesifik faaliyetlere göre iş kazası analizi',
    icon: 'fas fa-tasks',
    route: '/analysis/3.1.20'
  },
  {
    id: 21,
    title: '3.1.21 | Sapmalara Göre İş Kazaları',
    description: 'Sapma türlerine göre iş kazalarının incelenmesi',
    icon: 'fas fa-random',
    route: '/analysis/3.1.21'
  },
  {
    id: 22,
    title: '3.1.22 | Materyallere Göre İş Kazaları',
    description: 'Kullanılan materyallere göre kazaların dağılımı',
    icon: 'fas fa-box-open',
    route: '/analysis/3.1.22'
  },
  {
    id: 23,
    title: '3.1.23 | Çalışılan Çevreye Göre İş Kazaları',
    description: 'Çalışma çevresine göre kaza analizi',
    icon: 'fas fa-globe',
    route: '/analysis/3.1.23'
  },
  {
    id: 24,
    title: '3.1.24 | Çalışma Ortamına Göre İş Kazaları',
    description: 'Çalışma ortamı koşullarına göre kaza verileri',
    icon: 'fas fa-warehouse',
    route: '/analysis/3.1.24'
  },
  {
    id: 25,
    title: '3.1.25 | Çalışma Saatlerine Göre İş Kazaları',
    description: 'Çalışma saatleriyle ilişkilendirilen kazalar',
    icon: 'fas fa-clock',
    route: '/analysis/3.1.25'
  },
  {
    id: 26,
    title: '3.1.26 | Çalışan Gruplarına Göre İş Kazaları',
    description: 'Çalışan gruplarına göre iş kazası istatistikleri',
    icon: 'fas fa-users-cog',
    route: '/analysis/3.1.26'
  },
  {
    id: 27,
    title: '3.1.27 | Çalışan Gruplarına Göre Meslek Hastalıkları',
    description: 'Çalışan gruplarına göre meslek hastalıkları analizi',
    icon: 'fas fa-user-injured',
    route: '/analysis/3.1.27'
  },
  {
    id: 28,
    title: '3.1.28 | Çalışma Süresine Göre İş Kazaları ve Meslek Hastalıkları',
    description: 'Çalışma süresine göre kaza ve hastalık analizi',
    icon: 'fas fa-business-time',
    route: '/analysis/3.1.28'
  },
  {
    id: 29,
    title: '3.1.29 | Çalışma Süresine Göre İş Kazaları',
    description: 'Çalışma süresine göre iş kazası verileri',
    icon: 'fas fa-stopwatch',
    route: '/analysis/3.1.29'
  }
]);

// Formatlama fonksiyonu
const formatNumber = (num) => {
  return new Intl.NumberFormat('tr-TR').format(num)
}

const fetchData = async () => {
  try {
    const res = await axios.get(`/api/summary`);
    const mockResponse = res.data

    totalAccidents.value = mockResponse.total_accidents
    fatalAccidents.value = mockResponse.total_fatal_accidents
    occupationalDiseases.value = mockResponse.total_diseases
    averageIncapacityDays.value = mockResponse.avg_unfit_days

  } catch (error) {
    console.error('Veri yükleme hatası:', error)
  }
}

// Bileşen yüklendiğinde verileri çek
onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.dashboard-container {
  max-width: 100%;
  margin: 0 5%;
  padding: 20px;
}

.stats-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(22%, 1fr));
  gap: 1rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-card h3 {
  margin-top: 0;
  font-size: 1rem;
  color: #555;
}

.stat-card p {
  font-size: 1.8rem;
  font-weight: bold;
  margin: 0.5rem 0 0;
}

.stat-card.total {
  border-top: 4px solid #3498db;
}

.stat-card.fatal {
  border-top: 4px solid #e74c3c;
}

.stat-card.disease {
  border-top: 4px solid #9b59b6;
}

.stat-card.avg-days {
  border-top: 4px solid #2ecc71;
}

.analysis-categories {
  margin-bottom: 3rem;
}

.analysis-categories h2 {
  color: #2c3e50;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #eee;
}

.category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(30%, 1fr));
  gap: 1.5rem;
}

.category-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  text-decoration: none;
  color: #333;
  transition: transform 0.2s, box-shadow 0.2s;
  display: flex;
  flex-direction: column;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.category-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #3498db;
}

.warn {
  font-size: .7rem;
  color: white;
  font-style: italic;
}

.category-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1.1rem;
}

.category-card p {
  margin: 0;
  font-size: 0.9rem;
  color: #666;
  flex-grow: 1;
}

/* Kategori kartı renkleri */
.cat-0 {
  border-left: 4px solid #3498db;
}

.cat-1 {
  border-left: 4px solid #e74c3c;
}

.cat-2 {
  border-left: 4px solid #2ecc71;
}

.cat-3 {
  border-left: 4px solid #f39c12;
}

.cat-4 {
  border-left: 4px solid #9b59b6;
}



/* Yenilenmiş Footer Stilleri */
.dashboard-footer {
  background: linear-gradient(135deg, var(--main-color) 0%, #1a5f9c 100%);
  color: #ecf0f1;
  padding: 2rem 0;
  margin-top: 3rem;
  border-radius: 8px;
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  align-items: center;
}

.footer-logo {
  display: flex;
  align-items: center;
  filter: brightness(0) invert(1);
}

.logo-img {
  height: 80px;
  margin-right: 1rem;
}

.logo-text {
  font-weight: 600;
  font-size: 1rem;
  color: white;
}

.footer-links {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
}

.footer-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.2s;
}

.footer-link:hover {
  color: white;
  text-decoration: underline;
}

.footer-copyright {
  text-align: right;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
}

@media (max-width: 992px) {
  .header-content {
    flex-direction: column;
    text-align: center;
  }

  .header-badge {
    margin-top: 1rem;
    align-items: center;
  }

  .footer-content {
    grid-template-columns: 1fr;
    gap: 1.5rem;
    text-align: center;
  }

  .footer-links {
    flex-wrap: wrap;
  }

  .footer-copyright {
    text-align: center;
  }
}

@media (max-width: 768px) {
  .header-text h1 {
    font-size: 1.5rem;
  }

  .subtitle {
    font-size: 0.9rem;
  }

  .footer-links {
    flex-direction: column;
    gap: 0.5rem;
  }
}

@media (max-width: 768px) {
  .stats-overview {
    grid-template-columns: 1fr 1fr;
  }

  .category-grid {
    grid-template-columns: 1fr;
  }
}
</style>
