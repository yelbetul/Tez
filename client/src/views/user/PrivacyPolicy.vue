<template>
    <div class="dashboard-container">
        <HeaderApp :lastUpdate="lastUpdate" />
        <section class="privacy-policy">
            <h1>Gizlilik Politikası</h1>
            <p><strong>Son Güncelleme:</strong> 21 Mayıs 2025</p>

            <h2>1. Toplanan Bilgiler</h2>
            <p>
                Bu web sitesi kullanıcıdan doğrudan kişisel bilgi toplamaz. Ancak, IP adresi, tarayıcı bilgisi, işletim
                sistemi gibi teknik veriler otomatik olarak toplanabilir. Bu veriler yalnızca istatistiksel analiz
                amacıyla
                kullanılır.
            </p>

            <h2>2. Çerezler (Cookies)</h2>
            <p>
                Kullanıcı deneyimini geliştirmek adına çerezler kullanılabilir. Tarayıcınızdan çerez ayarlarınızı
                değiştirebilir veya çerezleri silebilirsiniz.
            </p>

            <h2>3. Üçüncü Taraf Hizmetleri</h2>
            <p>
                Platform, aşağıdaki hizmetleri kullanır:
            </p>
            <ul>
                <li><strong>Google Fonts:</strong> Yazı tiplerini düzgün görüntülemek için.</li>
                <li><strong>Google Analytics:</strong> Trafik ve kullanım istatistiklerini analiz etmek için.</li>
                <li><strong>Google Search Console:</strong> SEO görünürlüğünü ölçmek için.</li>
            </ul>
            <p>
                Detaylı bilgi için: <a href="https://policies.google.com/privacy" target="_blank">Google Gizlilik
                    Politikası</a>
            </p>

            <h2>4. Verilerin Kullanım Amacı</h2>
            <p>
                Toplanan veriler sadece kullanıcı deneyimini ve site performansını geliştirme amacıyla kullanılır.
            </p>

            <h2>5. Veri Güvenliği</h2>
            <p>
                Teknik güvenlik önlemleri alınmıştır. Ancak internet üzerinden aktarılan verilerin %100 güvenliği
                garanti
                edilemez.
            </p>

            <h2>6. Kullanıcı Hakları</h2>
            <p>
                KVKK kapsamında verilerinizle ilgili bilgi alma, silme, düzeltme ve itiraz etme haklarına sahipsiniz.
                Talepleriniz için lütfen bize ulaşın:
                <a href="mailto:contact@betulyel.com">contact@betulyel.com</a>
            </p>

            <h2>7. Politika Güncellemeleri</h2>
            <p>
                Bu gizlilik politikası zaman zaman güncellenebilir. Güncellemeler bu sayfada yayınlanır.
            </p>

            <h2>8. İletişim</h2>
            <p>
                Her türlü soru için bizimle iletişime geçebilirsiniz:
                <a href="mailto:contact@betulyel.com">contact@betulyel.com</a>
            </p>
        </section>
        <FooterApp />
    </div>
</template>

<script>
import HeaderApp from '@/components/user/HeaderApp.vue';
import FooterApp from '@/components/user/FooterApp.vue';

export default {
    components: {
        HeaderApp,
        FooterApp
    },
    data() {
        return {
            lastUpdate: new Date().toLocaleDateString('tr-TR')
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
.privacy-policy {
    max-width: 100%;
    margin: 40px auto;
    padding: 24px;
    background-color: #f9f9f9;
    border-radius: 16px;
    line-height: 1.6;
    color: #333;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}

.privacy-policy h1 {
    font-size: 2rem;
    color: #1a5f9c;
    margin-bottom: 1rem;
}

.privacy-policy h2 {
    font-size: 1.4rem;
    margin-top: 2rem;
    color: #222;
}

.privacy-policy p {
    margin-top: 0.6rem;
}

.privacy-policy ul {
    list-style-type: disc;
    padding-left: 1.5rem;
}

.privacy-policy a {
    color: #1a5f9c;
    text-decoration: underline;
}
</style>