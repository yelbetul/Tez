<template>
    <div class="panel-nav">
        <div class="panel-nav-item1">
            <div class="date">{{ formattedDate }}
                <div class="day">{{ dayName }}</div>
            </div>
            <div class="time">{{ time }}</div>
        </div>
        <div class="panel-nav-item2">
            <h2>İş Kazaları Analizi <br> {{ title }}</h2>
        </div>
        <div class="panel-nav-item3">
            <div class="logout-button" @click="logout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span @click="logout">Çıkış Yap</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        title: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            time: '',
            formattedDate: '',
            dayName: '',
        };
    },
    methods: {
        updateClock() {
            const now = new Date();
            this.time = now.toLocaleTimeString();
            this.formattedDate = now.toLocaleDateString();
            this.dayName = this.getDayName(now.getDay());
        },
        getDayName(dayIndex) {
            const days = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
            return days[dayIndex];
        },
        logout() {
            localStorage.setItem('is_logged_in', false)
            localStorage.removeItem('id')
            localStorage.removeItem('username')
            localStorage.removeItem('name_surname')
            this.$router.push('/admin/login');
        }
    },
    mounted() {
        this.updateClock();
        this.timer = setInterval(this.updateClock, 1000);
    },
    beforeUnmount() {
        clearInterval(this.timer);
    }
}
</script>

<style scoped>
.panel-nav-item1 {
    width: 33.33%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: start;
    color: var(--main-color);
}

.date {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-bottom: 1%;
    font-size: 1rem;
    font-weight: bold;
}

.day {
    margin-left: 10px;
}

.panel-nav {
    background-color: var(--panel-bg);
    color: var(--main-color);
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: stretch;
    width: 100%;
    padding: .5% 5%;
    border-radius: 10px;
    box-shadow: rgba(0, 0, 0, 0.1) 0px 8px 24px;
}

.panel-nav-item2 {
    width: 33.33%;
    height: 125px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.panel-nav-item2 img {
    width: 55%;
    height: 125px;
}

.panel-nav-item3 {
    width: 33.33%;
    display: flex;
    justify-content: end;
    align-items: center;

}

.panel-nav-item2 h1 {
    text-align: center;
}

/* Genel buton stili */
.notifications,
.logout-button {
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: end;
    color: var(--main-color);
    padding: 10px;
    border-radius: 5px;
    position: relative;
    width: 45%;
    height: 50px;
    transition: width 0.3s ease;
    margin: 0 20px;
}

.notifications i,
.logout-button i {
    font-size: 1.5rem;
    margin-right: 12px;
}

.text {
    opacity: 0;
    margin-left: 10px;
    font-size: 1rem;
    font-weight: bold;
    transition: opacity 0.3s ease;
}

.notifications:hover .text,
.logout-button:hover .text {
    opacity: 1;
}

.time {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 10px;
}


@media (max-width: 768px) {
    .panel-nav-item1 {
        display: none;
    }

    .panel-nav-item2 {
        width: 50%;
        justify-content: start;
    }

    .panel-nav-item2 h1 {
        font-size: 1.4rem;
    }

    .panel-nav-item3 {
        width: 50%;
    }

    .panel-nav-item3 i {
        font-size: 1.4rem;
    }
}

@media (max-width: 768px) {

    .panel-nav-item2 h1 {
        font-size: 1.2rem;
    }

    .panel-nav-item3 i {
        font-size: 1.2rem;
    }

    .notifications,
    .logout-button {
        margin: 0 10px;
    }

    .notifications strong {
        height: 20px;
    }
}

@media (max-width: 414px) {
    .panel-nav {
        margin: 5% 0;
    }

    .notifications-modal {
        top: 60px;
        right: -70px;
        width: 350px;
        max-height: 400px;
    }
}

@media (max-width: 375px) {
    .notifications-modal {
        width: 320px;
    }
}
</style>