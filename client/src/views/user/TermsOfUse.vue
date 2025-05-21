<template>
    <div class="dashboard-container">
        <HeaderApp :lastUpdate="lastUpdate" />
        <section class="terms-of-use">
            <h2>Kullanım Koşulları</h2>
            <p>
                <strong>1. Veri Kaynağı:</strong> Bu platformda kullanılan veriler, Sosyal Güvenlik Kurumu (SGK)
                tarafından
                halka açık olarak yayınlanan istatistiksel verilerden oluşmaktadır. Veriler anonimleştirilmiştir ve
                kişisel
                bilgi içermez.
            </p>
            <p>
                <strong>2. Sorumluluk Reddi:</strong> Bu platformdaki analizler ve yorumlar otomatik olarak OpenAI'nin
                yapay
                zeka modelleri tarafından oluşturulmuştur. Verilerin doğruluğu, güncelliği veya yorumların uygunluğu
                konusunda herhangi bir garanti verilmez. Kullanıcılar, bu bilgileri kendi sorumluluklarında kullanır.
            </p>
            <p>
                <strong>3. Ticari Kullanım Yasağı:</strong> Bu platformdaki veriler, analizler ve çıktılar ticari
                amaçlarla
                kullanılamaz. Akademik veya bilgilendirme amaçlı kullanımlar için kaynak gösterilmesi zorunludur.
            </p>
            <p>
                <strong>4. OpenAI Kullanımı:</strong> Yapay zeka yorumları OpenAI'nin API'si kullanılarak
                oluşturulmuştur.
                OpenAI'nin <a href="https://openai.com/policies" target="_blank">kullanım şartları</a> ve <a
                    href="https://openai.com/privacy" target="_blank">gizlilik politikası</a> bu hizmetin bir
                parçasıdır.
            </p>
            <p>
                <strong>5. Yasal Uyum:</strong> Bu platform, Türkiye Cumhuriyeti yasalarına ve SGK'nın veri kullanım
                politikalarına uygun şekilde hazırlanmıştır.
            </p>
        </section>
        <FooterApp />
    </div>
</template>

<script>
import HeaderApp from '@/components/user/HeaderApp.vue';
import FooterApp from '@/components/user/FooterApp.vue';

    export default {
        components:{
            HeaderApp,
            FooterApp
        },
        data() {
            return {
                lastUpdate : new Date().toLocaleDateString('tr-TR')
            }
        },
    }
</script>

<style scoped>
.dashboard-container {
    max-width: 100%;
    margin: 0 5%;
    padding: 20px;
}
.terms-of-use {
    max-width: 100%;
    margin: 3rem auto;
    padding: 2rem;
    background-color: #f9f9f9;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    color: #333;
    line-height: 1.7;
}

.terms-of-use h2 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
    color: #1a5f9c;
    text-align: center;
    font-weight: 600;
}

.terms-of-use p {
    margin-bottom: 1.2rem;
    font-size: 1rem;
}

.terms-of-use strong {
    display: block;
    font-weight: 600;
    margin-bottom: 0.3rem;
    color: #111;
}

.terms-of-use a {
    color: #004aad;
    text-decoration: underline;
    transition: color 0.2s ease;
}

.terms-of-use a:hover {
    color: #002b78;
}

/* Mobil uyum */
@media (max-width: 600px) {
    .terms-of-use {
        padding: 1.5rem;
    }

    .terms-of-use h2 {
        font-size: 1.6rem;
    }

    .terms-of-use p {
        font-size: 0.95rem;
    }
}
</style>