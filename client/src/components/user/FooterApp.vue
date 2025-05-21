<template>
    <footer class="dashboard-footer">
        <div class="footer-content">
            <div @click="goToHome" class="footer-logo">
                <img src="../../assets/logo.svg" alt="Logo" class="logo-img" />
            </div>
            <div class="footer-links">
                <a href="/privacy-policy" class="footer-link">Gizlilik Politikası</a>
                <a href="/terms-of-use" class="footer-link">Kullanım Koşulları</a>
            </div>
            <div class="footer-copyright">
                <p>© 2025 Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>
</template>
<script>
export default{
    methods: {
        goToHome(){
            this.$router.push('/')
        }
    }
}
</script>
<style scoped>
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
    cursor: pointer;
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
    .subtitle {
        font-size: 0.9rem;
    }

    .footer-links {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>