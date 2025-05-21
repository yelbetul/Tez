<template>
    <header class="dashboard-header">
        <div class="header-gradient">
            <div class="header-content">
                <div class="logo-container">
                    <img @click="$router.push('/')" src="@/assets/logo.svg" alt="Kurum Logosu" class="header-logo" />
                </div>
                <div class="header-text">
                    <h1>İş Kazaları ve Meslek Hastalıkları Analizi</h1>
                    <p class="subtitle">2019-2023 Yılları Arası Kapsamlı Veri Analizleri</p>
                    <p class="warn">Veriler SGK'nın yayınladığı 2019-2023 yılları arasındaki verilerdir.</p>
                </div>
                <div class="header-badge">
                    <span class="badge-update">Son Güncelleme: {{ lastUpdate }}</span>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue'

// Türkçe formatlı bugünün tarihi
const today = new Date()
const lastUpdate = ref(today.toLocaleDateString('tr-TR'))
</script>


<style scoped>
.dashboard-header {
    border-radius: 8px;
    margin-bottom: 2rem;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.header-gradient {
    background: linear-gradient(135deg, var(--main-color) 0%, #1a5f9c 100%);
    padding: 1.5rem 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.header-text h1 {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
    color: white;
    line-height: 1.3;
}

.subtitle {
    margin: 0.5rem 0 0;
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    font-weight: 300;
}

.logo-container {
    flex-shrink: 0;
    display: flex;
    align-items: center;
}

.header-logo {
    height: 100px;
    width: auto;
    filter: brightness(0) invert(1);
    /* Logoyu beyaz yapar */
    cursor: pointer;
}

.header-badge {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.badge-version {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.badge-update {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    font-style: italic;
}
.warn {
    font-size: .7rem;
    color: white;
    font-style: italic;
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
}

@media (max-width: 768px) {
    .header-text h1 {
        font-size: 1.5rem;
    }

    .subtitle {
        font-size: 0.9rem;
    }
}


</style>