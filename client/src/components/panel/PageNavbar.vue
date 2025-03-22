<template>
    <div class="nav">
        <div class="nav-item-1">
            <router-link :to="data.backRoute"><i class="fa-solid fa-arrow-left"></i></router-link>
        </div>
        <div class="nav-item-2">
            <h3>{{ data.title }}</h3>
        </div>
        <div class="nav-item-3">
            <div class="date">{{ formattedDate }}
                <div class="day">{{ dayName }}</div>
            </div>
            <div class="time">{{ time }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        navData: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            data: this.navData,
            time: '',
            formattedDate: '',
            dayName: ''
        };
    },
    unmounted() {
        clearInterval(this.timer);
    },
    mounted() {
        this.updateClock();
        this.timer = setInterval(this.updateClock, 1000);
    },
    beforeUnmount() {
        clearInterval(this.timer);
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
        }
    },
}
</script>

<style scoped>
.nav {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    padding: 1% 0;
}

.nav-item-1 {
    width: 30%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.nav-item-1 a {
    font-size: 2rem;
    color: var(--main-color);
}

.nav-item-2 {
    width: 30%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.nav-item-2 h3 {
    font-size: 1.8rem;
    color: var(--main-color);
}

.nav-item-3 {
    width: 30%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
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

.time {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 10px;
}

/* Responsive Desing */
@media (max-width: 768px) {
    .nav {
        margin: 1% 2%;
        justify-content: start;
        align-items: center;
        padding: 1%;
    }

    .nav-item-2 h3 {
        font-size: 1.4rem;
    }

    .date {
        font-size: 0.95rem;
    }

    .time {
        font-size: 1.1rem;
    }

    .nav-item-2 {
        width: 50%;
    }

    .nav-item-3 {
        display: none;
    }
}

@media (max-width: 480px) {
    .nav {
        margin: 1% 2%;
        justify-content: start;
        align-items: center;
        padding: 2%;
    }

    .nav-item-1 {
        width: 10%;
        justify-content: start;
    }

    .nav-item-2 {
        width: 100%;
        text-align: center;
    }

    .nav-item-2 h3 {
        font-size: 1.5rem;
    }

    .nav-item-3 {
        display: none;
    }
}
</style>