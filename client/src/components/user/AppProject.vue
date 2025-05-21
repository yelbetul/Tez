<template>
    <!-- Proje Kapsamı Paragrafı -->
    <section class="project-scope">
        <h2>Proje Hakkında</h2>
        <br>
        <p>
            Bu proje, Sosyal Güvenlik Kurumu (SGK) tarafından sağlanan iş kazaları ve meslek hastalıklarına ilişkin ham
            verilerin sistematik biçimde işlenerek anlamlı ve eyleme dönüştürülebilir bilgilere dönüştürülmesini
            amaçlamaktadır.
            Modern veri bilimi yöntemlerinin yanı sıra, OpenAI tarafından geliştirilen ileri düzey yapay zeka modelleri
            kullanılarak gerçekleştirilen bu analiz süreci, iş sağlığı ve güvenliği alanında veriye dayalı karar alma
            mekanizmalarını destekleyecek nitelikte kapsamlı çıktılar sunmaktadır.
        </p>
        <br>
        <p>
            Proje, Türkiye'de iş kazalarının ve meslek hastalıklarının nedenlerinin daha derinlemesine anlaşılmasını
            sağlamak, bu doğrultuda önleyici politikaların geliştirilmesine katkıda bulunmak ve ilgili kamu kurumlarının
            stratejik planlamalarına bilimsel bir temel oluşturmak üzere tasarlanmıştır.
            Akademik danışmanlığını Dr. Öğr. Üyesi Mehmet Ulaş Koyuncuoğlu’nun yürüttüğü proje, Hasan Göksel ve Betül
            Yel’in ortak çalışmalarıyla hayata geçirilmiştir.
        </p>
    </section>

    <!-- Ekip Üyeleri -->
    <section class="team-section">
        <h2>Proje Ekibi</h2>
        <div class="team-grid">
            <!-- Danışman Profil Kartı -->
            <div class="profile-card">
                <img src="../../assets/mehmet-ulas-koyuncuoglu.png" alt="Dr. Mehmet Ulaş Koyuncuoğlu"
                    class="profile-photo">
                <h3>Dr. Öğr. Üyesi Mehmet Ulaş Koyuncuoğlu</h3>
                <p>Danışman</p>
                <div class="profile-links">
                    <a href="https://www.linkedin.com/in/mehmet-ula%C5%9F-koyuncuo%C4%9Flu-a074a847/" target="_blank">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                    <a href="mailto:ulas@pau.edu.tr">
                        <i class="fas fa-envelope"></i> E-posta
                    </a>
                </div>
            </div>
            <!-- Betül Yel Profil Kartı -->
            <div class="profile-card">
                <img src="../../assets/betul-yel.jpeg" alt="Betül Yel" class="profile-photo">
                <h3>Betül Yel</h3>
                <p>Backend Developer</p>
                <div class="profile-links">
                    <a href="https://www.linkedin.com/in/betulyel" target="_blank">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                    <a href="mailto:contact@betulyel.com">
                        <i class="fas fa-envelope"></i> E-posta
                    </a>
                </div>
            </div>

            <!-- Hasan Göksel Profil Kartı -->
            <div class="profile-card">
                <img src="../../assets/hasan-goksel.png" alt="Hasan Göksel" class="profile-photo">
                <h3>Hasan Göksel</h3>
                <p>Frontend Developer</p>
                <div class="profile-links">
                    <a href="https://www.linkedin.com/in/hsangoksel" target="_blank">
                        <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                    <a href="mailto:hasangoksel3@gmail.com">
                        <i class="fas fa-envelope"></i> E-posta
                    </a>
                </div>
            </div>

        </div>
    </section>
</template>


<style  scoped>
/* Proje Kapsamı ve Ekip Stilleri */
.project-scope {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.project-scope h2 {
    color: #2c3e50;
    margin-top: 0;
}

.team-section {
    margin-bottom: 3rem;
}

.team-section h2 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #eee;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.profile-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
}

.profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 4px solid #3498db;
}

.profile-card h3 {
    margin: 0.5rem 0;
    color: #2c3e50;
}

.profile-card p {
    color: #7f8c8d;
    margin-bottom: 1rem;
}

.profile-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.profile-links a {
    color: #3498db;
    text-decoration: none;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    transition: color 0.2s;
}

.profile-links a:hover {
    color: #2980b9;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .team-grid {
        grid-template-columns: 1fr;
    }
}
</style>