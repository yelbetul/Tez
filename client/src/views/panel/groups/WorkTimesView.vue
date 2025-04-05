<template>
    <div class="page-container">
        <PageNavbar :navData="navbarData" />
        <div class="table-container">
            <div class="table">
                <table>
                    <thead>
                        <td>Kod</td>
                        <td>Çalışma Saati</td>
                        <td style="width: 20%;"></td>
                    </thead>
                    <tbody>
                        <tr v-for="item in work_times" :key="item.id">
                            <td>{{ item.code }}</td>
                            <td>{{ item.time_interval }}</td>
                            <td>
                                <i @click="updateModal(item)" class="fa-solid fa-pen-to-square"></i>
                                <i @click="deleteTimeCode(item)" class="fa-solid fa-trash-can"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <WorkTimes v-if="modal_visible" :visible="modal_visible" :state="state" :data="update_time"
                @update="updateState" @close="closeModal" />
        </div>
    </div>
</template>

<script>
import WorkTimes from '@/components/panel/groups/WorkTimes.vue';
import PageNavbar from '@/components/panel/PageNavbar.vue';
import { useAuthStore } from '@/stores/AuthStore';
import axios from 'axios';
import Swal from 'sweetalert2';
export default {
    components: {
        PageNavbar,
        WorkTimes
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    data() {
        return {
            navbarData: {
                title: 'Çalışma Saatleri',
                backRoute: '/admin/groups',
            },
            state: 'new',
            modal_visible: true,
            work_times: [],
            update_time: null
        }
    },
    methods: {
        updateModal(item) {
            this.update_time = item
            this.state = 'update'
        },
        updateState() {
            this.state = 'new'
            this.update_time = null
        },
        closeModal() {
            this.state = 'new'
            this.initializeAuth()
        },
        deleteTimeCode(item) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#003049',
                cancelButtonColor: '#92140cff',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('https://iskazalarianaliz.com/api/time-intervals/delete/' + item.id)
                        .then(res => {
                            if (res.data.success) {
                                this.initializeAuth()
                            }
                        })
                }
            });
        },
        async initializeAuth() {
            await this.authStore.fetchAuthData();

            axios.get('https://iskazalarianaliz.com/api/time-intervals')
                .then(res => {
                    this.work_times = res.data.data
                })
        },
    },
    created() {
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
.page-container {
    background-color: var(--panel-bg);
    min-height: 100vh;
}

.table-container {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 0 5%;
}

.table {
    overflow-y: auto;
    max-height: 80vh;
    width: 50%;
}

/* Scrollbar Genel Ayarları */
.table::-webkit-scrollbar {
    width: 8px;
    /* Scrollbar genişliği */
}

/* Scrollbar Kanalı (Boş Kısım) */
.table::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    /* Hafif transparan */
    border-radius: 10px;
}

/* Scrollbar Çubuğu (Kaydırılabilir Alan) */
.table::-webkit-scrollbar-thumb {
    background: var(--main-color);
    /* Ana renk */
    border-radius: 10px;
    transition: background 0.3s ease;
}

/* Hover Efekti */
.table::-webkit-scrollbar-thumb:hover {
    background: var(--penn-red);
    /* Hover'da farklı renk */
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: var(--panel-bg);
}

thead {
    background: var(--main-color);
    color: var(--second-color);
}

th,
td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    height: 50px;
    vertical-align: middle;
}

tr:hover {
    background-color: #f5e7cd;
}

tbody tr td:last-child {
    text-align: center;
}

tbody tr td:last-child i {
    font-size: 1.2rem;
    margin: 0 10px;
    cursor: pointer;
    color: var(--main-color);
}

tbody tr td:last-child i:hover {
    color: var(--penn-red);
}
</style>