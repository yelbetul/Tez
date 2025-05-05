<template>
    <div class="sector-analysis-container">
        <div class="page-header">
            <h1>Sektörlere Göre Geçici İş Göremezlik Günleri Analizi</h1>
            <p class="subtitle">2019-2023 yılları arası sektörlere geçici iş göremezlik verileri</p>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="sector">Sektör:</label>
                <select id="sector" v-model="selectedSector" @change="fetchData">
                    <option value="all">Tüm Sektörler</option>
                    <option v-for="sector in uniqueSectors" :key="sector.sector_code" :value="sector.sector_code">
                        {{ sector.sector_code }} - {{ sector.group_name }}
                    </option>
                </select>
            </div>

            <div class="filter-group">
                <label for="year">Yıl:</label>
                <select id="year" v-model="selectedYear" @change="fetchData">
                    <option value="all">Tüm Yıllar</option>
                    <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                </select>
            </div>
        </div>

        <!-- Ana Grafik -->
        <div class="chart-container" v-if="!loading && selectedSector === 'all'">
            <h2>Sektörel Geçici İş Göremezlik Vaka Dağılımı</h2>
            <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
        </div>

        <!-- Detay Grafikler -->
        <div class="detail-charts" v-if="!loading && selectedSector !== 'all'">
            <div class="chart-container">
                <h2>Vaka Tipine Göre Dağılım</h2>
                <apexchart type="pie" height="350" :options="caseTypeChartOptions" :series="caseTypeSeries"></apexchart>
            </div>

            <div class="chart-container">
                <h2>Cinsiyet ve Tedavi Türü Dağılımı</h2>
                <div class="distribution-charts">
                    <div class="distribution-chart">
                        <h3>Cinsiyet Dağılımı</h3>
                        <apexchart type="donut" height="300" :options="genderChartOptions" :series="genderSeries">
                        </apexchart>
                    </div>
                    <div class="distribution-chart">
                        <h3>Tedavi Türü</h3>
                        <apexchart type="donut" height="300" :options="treatmentChartOptions" :series="treatmentSeries">
                        </apexchart>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analiz Bölümü -->
        <div class="analysis-container" v-if="analysis && !loading">
            <h2>Analiz ve Yorumlar</h2>
            <p><em>Bu analiz ISO 45001 standartlarına uygun olarak yapay zeka tarafından oluşturulmuştur.</em></p>
            <div class="analysis-comment" v-html="formatAnalysis(analysis)"></div>
        </div>

        <!-- Veri Tablosu -->
        <div class="data-table" v-if="tableData.length > 0 && !loading">
            <h2>Detaylı Veriler</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sektör Kodu</th>
                        <th>Grup Adı</th>
                        <th>Yıl</th>
                        <th>1 Gün</th>
                        <th>2 Gün</th>
                        <th>3 Gün</th>
                        <th>4 Gün</th>
                        <th>5+ Gün</th>
                        <th>Erkek</th>
                        <th>Kadın</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in tableData" :key="index">
                        <td>{{ item.sector_code }}</td>
                        <td>{{ item.group_name }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ item.one_day_unfit.toLocaleString() }}</td>
                        <td>{{ item.two_days_unfit.toLocaleString() }}</td>
                        <td>{{ item.three_days_unfit.toLocaleString() }}</td>
                        <td>{{ item.four_days_unfit.toLocaleString() }}</td>
                        <td>{{ item.five_or_more_days_unfit.toLocaleString() }}</td>
                        <td>{{ item.male_count.toLocaleString() }}</td>
                        <td>{{ item.female_count.toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="loading" v-if="loading">
            <div class="spinner"></div>
            <p>Veriler ve analiz yükleniyor..</p>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'

export default {
    components: {
        apexchart: VueApexCharts
    },
    setup() {
        const loading = ref(true)
        const tableData = ref([])
        const summary = ref(null)
        const analysis = ref('')
        const selectedYear = ref('all')
        const selectedSector = ref('all')

        // Grafik verileri
        const mainSeries = ref([])
        const caseTypeSeries = ref([])
        const genderSeries = ref([])
        const treatmentSeries = ref([])

        // Grafik ayarları
        const mainChartOptions = ref({
            chart: { type: 'bar', height: 500 },
            plotOptions: { bar: { horizontal: true } },
            dataLabels: { enabled: true },
            xaxis: { title: { text: 'Vaka Sayısı' } },
            yaxis: { title: { text: 'Sektörler' } },
            colors: ['#3B82F6']
        })

        const caseTypeChartOptions = ref({
            chart: { type: 'pie' },
            labels: ['1 Gün', '2 Gün', '3 Gün', '4 Gün', '5+ Gün'],
            colors: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6']
        })

        const genderChartOptions = ref({
            chart: { type: 'donut' },
            labels: ['Erkek', 'Kadın'],
            colors: ['#3B82F6', '#EC4899']
        })

        const treatmentChartOptions = ref({
            chart: { type: 'donut' },
            labels: ['Ayakta Tedavi', 'Yatarak Tedavi'],
            colors: ['#10B981', '#EF4444']
        })

        // Mevcut yılları hesapla
        const availableYears = computed(() => {
            const years = new Set()
            tableData.value.forEach(item => years.add(item.year))
            return Array.from(years).sort()
        })

        // Benzersiz sektörleri hesapla
        const uniqueSectors = computed(() => {
            const sectors = {}
            tableData.value.forEach(item => {
                if (!sectors[item.sector_code]) {
                    sectors[item.sector_code] = {
                        sector_code: item.sector_code,
                        group_name: item.group_name
                    }
                }
            })
            return Object.values(sectors)
        })

        // Veri çekme fonksiyonu
        const fetchData = async () => {
            loading.value = true
            try {
                const params = {
                    year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
                    sector_code: selectedSector.value !== 'all' ? selectedSector.value : undefined
                }

                const response = await axios.get('/api/temporary-disability-day-by-sector-user', { params })
                tableData.value = response.data.data
                summary.value = response.data.summary
                analysis.value = response.data.analysis
                updateCharts()
            } catch (error) {
                console.error('Veri alınırken hata:', error)
            } finally {
                loading.value = false
            }
        }

        // Grafikleri güncelleme
        const updateCharts = () => {
            if (selectedSector.value === 'all') {
                updateMainChart()
            } else {
                updateDetailCharts()
            }
        }

        const updateMainChart = () => {
            const sectorData = {}

            tableData.value.forEach(item => {
                if (!sectorData[item.sector_code]) {
                    sectorData[item.sector_code] = {
                        name: item.group_name,
                        total: 0
                    }
                }

                sectorData[item.sector_code].total +=
                    item.one_day_unfit + item.two_days_unfit + item.three_days_unfit +
                    item.four_days_unfit + item.five_or_more_days_unfit
            })

            // Sıralama ve en iyi 20 sektör
            const sortedSectors = Object.entries(sectorData)
                .sort((a, b) => b[1].total - a[1].total)
                .slice(0, 20)

            mainSeries.value = [{
                name: 'Toplam Vaka',
                data: sortedSectors.map(([code, data]) => ({
                    x: data.name,
                    y: data.total,
                    sector_code: code
                }))
            }]
        }

        const updateDetailCharts = () => {
            // Vaka tipi dağılımı
            caseTypeSeries.value = [
                summary.value.one_day_cases,
                summary.value.two_days_cases,
                summary.value.three_days_cases,
                summary.value.four_days_cases,
                summary.value.five_or_more_days_cases
            ]

            // Cinsiyet dağılımı
            genderSeries.value = [
                summary.value.male_count,
                summary.value.female_count
            ]

            // Tedavi türü dağılımı
            treatmentSeries.value = [
                summary.value.outpatient_count,
                summary.value.inpatient_count
            ]
        }

        function textToHtml(text) {
            // Convert numbered headings
            text = text.replace(/^(\d+\.\s+.+)$/gm, '<h2>$1</h2>');

            // Convert bullet points
            text = text.replace(/^- (.+)$/gm, '<li>$1</li>');
            text = text.replace(/(<li>.*<\/li>)+/gs, '<ul>$&</ul>');

            // Convert line breaks
            text = text.replace(/\n/g, '<br>');

            return text;
        }

        const formatAnalysis = (text) => {
            // First convert plain text to HTML
            text = textToHtml(text);

            // Then apply your existing formatting
            text = text.replace(/<h2>(.*?)<\/h2>/g, '<h2 class="analysis-heading">$1</h2>');
            // ... rest of your existing formatAnalysis logic

            return text;
        };

        onMounted(fetchData)

        return {
            loading,
            tableData,
            summary,
            analysis,
            availableYears,
            uniqueSectors,
            selectedYear,
            selectedSector,
            mainSeries,
            caseTypeSeries,
            genderSeries,
            treatmentSeries,
            mainChartOptions,
            caseTypeChartOptions,
            genderChartOptions,
            treatmentChartOptions,
            fetchData,
            formatAnalysis
        }
    }
}
</script>

<style scoped>
.sector-analysis-container {
    max-width: 90%;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-header h1 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    margin-top: 0;
}

.filters {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    margin-bottom: 8px;
    font-weight: 500;
    color: #34495e;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.data-table {
    margin-top: 30px;
    overflow-x: auto;
}

.data-table h2 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th,
td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #34495e;
}

tr:hover {
    background-color: #f5f5f5;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #3b82f6;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.gender-charts {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
}

.gender-chart {
    flex: 1;
    min-width: 300px;
    text-align: center;
}

.table-actions {
    margin-bottom: 15px;
    text-align: right;
}

.export-btn {
    background-color: #1d6f42;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.export-btn:hover {
    background-color: #165834;
}

.export-btn i {
    margin-right: 5px;
}

/* Daha kompakt bir tablo için */
table {
    font-size: 13px;
}

th,
td {
    padding: 8px 12px;
}

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
    }

    .filter-group {
        width: 100%;
    }

    .gender-charts {
        flex-direction: column;
    }
}

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
        gap: 15px;
    }

    .filter-group {
        width: 100%;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .subtitle {
        font-size: 1rem;
    }
}

.sector-analysis-container {
    max-width: 90%;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
    text-align: center;
}

.page-header h1 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 10px;
}

.subtitle {
    font-size: 1.1rem;
    color: #7f8c8d;
    margin-top: 0;
}

.filters {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    margin-bottom: 8px;
    font-weight: 500;
    color: #34495e;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 30px;
}

.data-table {
    margin-top: 30px;
    overflow-x: auto;
}

.data-table h2 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

th,
td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #34495e;
}

tr:hover {
    background-color: #f5f5f5;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: #3b82f6;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.gender-charts {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
}

.gender-chart {
    flex: 1;
    min-width: 300px;
    text-align: center;
}

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
        gap: 15px;
    }

    .filter-group {
        width: 100%;
    }

    .gender-charts {
        flex-direction: column;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .subtitle {
        font-size: 1rem;
    }
}
</style>