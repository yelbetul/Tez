<template>
    <div class="page-container">
        <PageNavbar :navData="navbarData" />
        <div class="navbar-buttons">
            <span @click="newData"><i class="fa-solid fa-plus"></i> Yeni Veri Ekle</span>
            <span @click="import_visible = true"><i class="fa-solid fa-upload"></i> İçeri Aktar</span>
            <span @click="downloadExcel"><i class="fa-solid fa-download"></i> Dışarı Aktar</span>
            <!-- Yıl Filtresi -->
            <div class="year-filter">
                <select v-model="selectedYear" @change="filterData">
                    <option value="">Tüm Yıllar</option>
                    <option v-for="year in uniqueYears" :key="year" :value="year">{{ year }}</option>
                </select>
            </div>
        </div>
        <!-- Yükleniyor Durumu -->
        <div v-if="isLoading" class="loading-overlay">
            <div class="loader"></div>
            <p>Veriler yükleniyor...</p>
        </div>

        <!-- Hata Mesajı -->
        <div v-if="error" class="error-message">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ error }}
        </div>
        <div class="table-container" v-if="!isLoading && !error">
            <table>
                <thead>
                    <td>Yıl</td>
                    <td>Sektör Kodu</td>
                    <td>Cinsiyet</td>
                    <td>İş Kazası Sonucu <br> <span>Ölenler</span></td>
                    <td>Meslek Hastalığı Sonucu<br> <span>Ölenler</span></td>
                    <td style="width: 10%;"></td>
                </thead>
                <tbody>
                    <tr v-for="item in filteredData" :key="item.id">
                        <td>{{ item.year }}</td>
                        <td>{{ item.sector.sector_code }}</td>
                        <td>{{ item.gender === 1 ? 'Kadın' : 'Erkek' }}</td>
                        <td>{{ item.work_accident_fatalities }}</td>
                        <td>{{ item.occupational_disease_fatalities }}</td>
                        <td>
                            <i @click="updateData(item)" class="fa-solid fa-pen-to-square"></i>
                            <i @click="deleteData(item)" class="fa-solid fa-trash-can"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <WorkAccidentsBySectorCodes v-if="modal_visible" :visible="modal_visible" :data="selected_code" :state="state"
        @close="closeModal" />
    <ImportData v-if="import_visible" :visible="import_visible" @close="closeModal" />
</template>

<script>
import PageNavbar from '@/components/panel/PageNavbar.vue';
import { useAuthStore } from '@/stores/AuthStore';
import axios from 'axios';
import Swal from 'sweetalert2';
import ExcelJS from 'exceljs';
import WorkAccidentsBySectorCodes from '@/components/panel/tables/FatalWorkAccidentsBySectorCodes.vue';
import ImportData from '@/components/panel/tables/import/FatalWorkAccidentsBySectorCodes.vue';
export default {
    components: {
        PageNavbar,
        WorkAccidentsBySectorCodes,
        ImportData
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    data() {
        return {
            navbarData: {
                title: 'Sektörlere Göre Ölümlü İş Kazaları',
                backRoute: '/admin/tables',
            },
            state: null,
            modal_visible: false,
            selected_code: null,
            data: [],
            import_visible: false,
            selectedYear: '',
            isLoading: false,
            error: null
        }
    },
    computed: {
        // Filtrelenmiş veri
        filteredData() {
            if (!this.selectedYear) return this.data
            return this.data.filter(item => item.year == this.selectedYear)
        },
        // Benzersiz yıllar
        uniqueYears() {
            return [...new Set(this.data.map(item => item.year))].sort((a, b) => b - a)
        }
    },
    methods: {
        newData() {
            this.state = 'new'
            this.modal_visible = true
        },
        updateData(item) {
            this.state = 'update'
            this.modal_visible = true
            this.selected_code = item
        },
        closeModal() {
            this.state = null
            this.modal_visible = false
            this.selected_code = null
            this.import_visible = false
            this.initializeAuth()
        },
        deleteData(item) {
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
                    axios.delete('https://iskazalarianaliz.com/api/fatal-work-accidents-by-sector/delete/' + item.id)
                        .then(res => {
                            if (res.data.success) {
                                this.initializeAuth()
                                Swal.fire({
                                    title: 'Silindi!',
                                    text: 'Veri başarıyla silindi.',
                                    icon: 'success',
                                });
                            }
                        })
                }
            });
        }, 
        downloadExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Ölümlü İş Kazaları Sektöre Göre');

            worksheet.columns = [
                { header: 'Yıl', key: 'year', width: 10 },
                { header: 'Sektör Kodu', key: 'sector_code', width: 15 },
                { header: 'Cinsiyet', key: 'gender', width: 10 },
                { header: 'İş Kazası Sonucu Ölenler', key: 'work_accident_fatalities', width: 15 },
                { header: 'Meslek Hastalığı Sonucu Ölenler', key: 'occupational_disease_fatalities', width: 20 },
            ];

            // Verileri ekleyin
            this.data.forEach((item) => {
                worksheet.addRow({
                    year: item.year,
                    sector_code: item.sector.sector_code,  // Sektör Kodu
                    gender: item.gender === 1 ? 'Kadın' : 'Erkek',  // Cinsiyet
                    work_accident_fatalities: item.work_accident_fatalities,
                    occupational_disease_fatalities: item.occupational_disease_fatalities,
                });
            });

            // Header stil ayarı
            const headerRow = worksheet.getRow(1);
            headerRow.font = {
                bold: true,
                size: 14,
                color: { argb: 'FFFFFFFF' },
            };

            headerRow.eachCell((cell) => {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF003049' },
                };
                cell.alignment = {
                    horizontal: 'center',
                    vertical: 'middle',
                };
            });

            // Dosyayı indir
            workbook.xlsx.writeBuffer().then((buffer) => {
                const blob = new Blob([buffer], { type: 'application/octet-stream' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Olumlu_Is_Kazalari_Sektor_Gore.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });
        },
        async initializeAuth() {
            this.isLoading = true
            this.error = null
            try {
                await this.authStore.fetchAuthData()
                const response = await axios.get('https://iskazalarianaliz.com/api/fatal-work-accidents-by-sector')
                this.data = response.data
            } catch (err) {
                this.error = 'Veriler yüklenirken bir hata oluştu: ' + (err.response?.data?.message || err.message)
            } finally {
                this.isLoading = false
            }
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

.navbar-buttons {
    width: 100%;
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin-top: 1%;
}

.navbar-buttons span {
    width: 20%;
    border: 1px solid var(--main-color);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px 0;
    border-radius: 10px;
    cursor: pointer;
    transition: all ease .3s;
}

.navbar-buttons span i {
    margin-right: 10px;
}

.navbar-buttons span:hover {
    background-color: var(--main-color);
    color: var(--second-color);
}

.table-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

table {
    margin-top: 2%;
    width: 90%;
    border-collapse: collapse;
    table-layout: fixed;
    background: var(--panel-bg);
}

thead {
    background: var(--main-color);
    color: var(--second-color);
}
thead td span{
    font-size: .8rem;
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
    vertical-align: middle;
}

tbody tr td:last-child i {
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--main-color);
}

tbody tr td:last-child i:hover {
    color: var(--penn-red);
}

.fa-pen-to-square {
    margin-right: 10px;
}
/* Yıl Filtresi Stili */
.year-filter {
    display: inline-block;
    margin-left: 20px;
}

.year-filter select {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #003049;
    background-color: white;
}

/* Yükleniyor Stili */
.loading-overlay {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 1000;
}

.loader {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #003049;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin: 0 auto 10px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

/* Hata Mesajı Stili */
.error-message {
    padding: 15px;
    background-color: #ffebee;
    color: #b71c1c;
    border-radius: 4px;
    margin: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>