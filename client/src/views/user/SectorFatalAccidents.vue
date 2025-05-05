<template>
    <div class="sector-analysis-container">
        <div class="page-header">
            <h1>Sektörlere Göre Ölümlü İş Kazaları Analizi</h1>
            <p class="subtitle">2019-2023 yılları arası sektörel ölümlü iş kazası verileri</p>
        </div>

        <div class="filters">
            <!-- Sektör Seçimi -->
            <div class="filter-group">
                <label for="sector">Sektör:</label>
                <select id="sector" v-model="selectedSector" @change="updateGroups">
                    <option value="all">Tüm Sektörler</option>
                    <option v-for="sector in uniqueSectors" :key="sector.sector_code" :value="sector">
                        {{ sector.sector_code }} - {{ sector.sector_name }}
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

            <div v-if="selectedSector !== 'all'" class="filter-group">
                <label for="metric">Gösterge:</label>
                <select id="metric" v-model="selectedMetric" @change="updateCharts">
                    <option value="total">Toplam Ölüm</option>
                    <option value="accident">İş Kazası Kaynaklı</option>
                    <option value="disease">Meslek Hastalığı Kaynaklı</option>
                </select>
            </div>
        </div>

        <!-- Ana Grafik -->
        <div class="chart-container" v-if="selectedSector === 'all' && !loading">
            <h2>Sektörel Ölüm Karşılaştırması</h2>
            <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
        </div>

        <!-- Detay Grafikler -->
        <div class="detail-charts" v-if="selectedSector !== 'all' && !loading">
            <div class="chart-container">
                <h2>{{ selectedSectorName }} - Yıllara Göre Ölüm Dağılımı</h2>
                <apexchart type="line" height="350" :options="yearlyChartOptions" :series="yearlySeries"></apexchart>
            </div>

            <div class="chart-container">
                <h2>{{ selectedSectorName }} - Cinsiyet Dağılımı</h2>
                <div class="gender-charts">
                    <div class="gender-chart">
                        <h3>Erkek</h3>
                        <apexchart type="radialBar" height="300" :options="genderChartOptions('Erkek')"
                            :series="maleSeries"></apexchart>
                    </div>
                    <div class="gender-chart">
                        <h3>Kadın</h3>
                        <apexchart type="radialBar" height="300" :options="genderChartOptions('Kadın')"
                            :series="femaleSeries"></apexchart>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analiz Bölümü -->
        <div class="analysis-container" v-if="analysisComment && !loading">
            <h2>Risk Analizi ve Öneriler</h2>
            <p><em>Bu analiz ISO 45001 standartlarına uygun olarak yapay zeka tarafından oluşturulmuştur.</em></p>
            <div class="analysis-comment">
                <div v-html="formatAnalysis(analysisComment)"></div>
            </div>
        </div>

        <!-- Veri Tablosu -->
        <div class="data-table" v-if="tableData.length > 0 && !loading">
            <h2>Detaylı Ölüm İstatistikleri</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sektör Kodu</th>
                        <th>Sektör Adı</th>
                        <th>Yıl</th>
                        <th>Toplam Ölüm</th>
                        <th>İş Kazası Kaynaklı</th>
                        <th>Meslek Hastalığı Kaynaklı</th>
                        <th>Erkek</th>
                        <th>Kadın</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in tableData" :key="`${item.sector_code}-${item.year}`">
                        <td>{{ item.sector_code }}</td>
                        <td>{{ getFullSectorName(item.sector_code) }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ calculateTotal(item) }}</td>
                        <td>{{ item.work_accident_fatalities || 0 }}</td>
                        <td>{{ item.occupational_disease_fatalities || 0 }}</td>
                        <td>{{ item.male_count || 0 }}</td>
                        <td>{{ item.female_count || 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="loading" v-if="loading">
            <div class="spinner"></div>
            <p>Veriler ve analiz yükleniyor...</p>
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
        const sectors = ref([])
        const analysisComment = ref([])
        const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
        const selectedYear = ref('all')
        const selectedSector = ref('all')
        const selectedMetric = ref('total')

        // Grafik verileri
        const mainSeries = ref([{ name: 'Ölüm Sayısı', data: [] }])
        const yearlySeries = ref([{ name: 'Ölüm Sayısı', data: [] }])
        const maleSeries = ref([0])
        const femaleSeries = ref([0])

        // Sektör hiyerarşisi
        const uniqueSectors = computed(() => {
            const sectorsMap = {}
            sectors.value.forEach(item => {
                if (!sectorsMap[item.sector_code]) {
                    sectorsMap[item.sector_code] = {
                        sector_code: item.sector_code,
                        sector_name: item.pure_name
                    }
                }
            })
            return Object.values(sectorsMap)
        })

        const selectedSectorName = computed(() => {
            if (selectedSector.value === 'all') return 'Tüm Sektörler'
            const sector = sectors.value.find(s => s.sector_code === selectedSector.value.sector_code)
            return sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : ''
        })

        // Grafik ayarları
        const mainChartOptions = ref({
            chart: {
                type: 'bar',
                height: 500,
                toolbar: { show: true }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: { enabled: true },
            xaxis: {
                title: { text: 'Ölüm Sayısı' },
                labels: {
                    formatter: function (val) {
                        return Math.round(val).toLocaleString()
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        const sector = sectors.value.find(s => s.sector_code === value)
                        return sector ? sector.pure_name : value
                    }
                }
            },
            colors: ['#e53e3e'],
            tooltip: {
                y: {
                    formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                        const sectorCode = w.config.series[seriesIndex].data[dataPointIndex].x
                        const sector = sectors.value.find(s => s.sector_code === sectorCode)
                        const sectorName = sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : sectorCode
                        return `${sectorName}: ${value} ölüm`
                    }
                }
            }
        })

        const yearlyChartOptions = ref({
            chart: { type: 'line', height: 350 },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 5 },
            xaxis: { categories: availableYears.value },
            yaxis: { title: { text: 'Ölüm Sayısı' } },
            colors: ['#e53e3e'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' ölüm'
                    }
                }
            }
        })

        const genderChartOptions = (gender) => ({
            chart: { type: 'radialBar', height: 300 },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: { margin: 0, size: '70%' },
                    dataLabels: {
                        name: { fontSize: '16px', color: gender === 'Erkek' ? '#3182ce' : '#d53f8c' },
                        value: { fontSize: '24px', formatter: (val) => val + '%' }
                    }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'dark', shadeIntensity: 0.15 } },
            colors: [gender === 'Erkek' ? '#3182ce' : '#d53f8c'],
            labels: [gender]
        })

        const formatAnalysis = (text) => {
            return text.replace(/\n/g, '<br>')
        }

        // Veri çekme fonksiyonları
        const fetchData = async () => {
            loading.value = true
            try {
                const params = {
                    year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
                    sector_code: selectedSector.value !== 'all' ? selectedSector.value.sector_code : undefined
                }

                const response = await axios.get('/api/fatal-work-accidents-by-sector-user', { params })
                tableData.value = response.data.data
                analysisComment.value = response.data.analysis
                updateCharts()
            } catch (error) {
                console.error('Veri alınırken hata:', error)
            } finally {
                loading.value = false
            }
        }

        const fetchSectors = async () => {
            try {
                const response = await axios.get('/api/sectors-user')
                sectors.value = response.data
            } catch (error) {
                console.error('Sektör verileri alınırken hata:', error)
            }
        }

        // Grafik güncelleme fonksiyonları
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
                        name: item.pure_name,
                        total: 0,
                        accident: 0,
                        disease: 0,
                        male_count: 0,
                        female_count: 0
                    }
                }

                const total = calculateTotal(item)
                sectorData[item.sector_code].total += total
                sectorData[item.sector_code].accident += item.work_accident_fatalities || 0
                sectorData[item.sector_code].disease += item.occupational_disease_fatalities || 0
                sectorData[item.sector_code].male_count += item.male_count || 0
                sectorData[item.sector_code].female_count += item.female_count || 0
            })

            // Sıralama ve en iyi 20 sektör
            const sortedSectors = Object.entries(sectorData)
                .sort((a, b) => b[1][selectedMetric.value] - a[1][selectedMetric.value])
                .slice(0, 20)

            // Grafik verilerini güncelle
            mainSeries.value = [{
                name: getMetricTitle(),
                data: sortedSectors.map(([code, data]) => ({
                    x: data.name,
                    y: data[selectedMetric.value],
                    sector_code: code
                }))
            }]

            // Eksen etiketlerini güncelle
            mainChartOptions.value.xaxis.title.text = getMetricTitle()
        }

        const updateDetailCharts = () => {
            const yearlyData = {}
            availableYears.value.forEach(year => {
                yearlyData[year] = 0
            })

            let maleTotal = 0
            let femaleTotal = 0

            tableData.value.forEach(item => {
                if (selectedMetric.value === 'total') {
                    yearlyData[item.year] += calculateTotal(item)
                } else if (selectedMetric.value === 'accident') {
                    yearlyData[item.year] += item.work_accident_fatalities || 0
                } else {
                    yearlyData[item.year] += item.occupational_disease_fatalities || 0
                }

                maleTotal += item.male_count || 0
                femaleTotal += item.female_count || 0
            })

            yearlySeries.value = [{
                name: getMetricTitle(),
                data: availableYears.value.map(year => yearlyData[year])
            }]

            const total = maleTotal + femaleTotal
            maleSeries.value = [total > 0 ? Math.round((maleTotal / total) * 100) : 0]
            femaleSeries.value = [total > 0 ? Math.round((femaleTotal / total) * 100) : 0]
        }

        // Yardımcı fonksiyonlar
        const calculateTotal = (item) => {
            return (item.work_accident_fatalities || 0) + (item.occupational_disease_fatalities || 0)
        }

        const getMetricTitle = () => {
            switch (selectedMetric.value) {
                case 'total': return 'Toplam Ölüm Sayısı'
                case 'accident': return 'İş Kazası Kaynaklı Ölümler'
                case 'disease': return 'Meslek Hastalığı Kaynaklı Ölümler'
                default: return 'Ölüm Sayısı'
            }
        }

        const getFullSectorName = (sectorCode) => {
            const sector = sectors.value.find(s => s.sector_code === sectorCode)
            return sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : sectorCode
        }

        const updateGroups = () => {
            fetchData()
        }

        // Sayfa yüklendiğinde verileri çek
        onMounted(async () => {
            await fetchSectors()
            await fetchData()
        })

        return {
            loading,
            tableData,
            availableYears,
            selectedYear,
            selectedSector,
            selectedMetric,
            uniqueSectors,
            selectedSectorName,
            mainChartOptions,
            mainSeries,
            yearlyChartOptions,
            yearlySeries,
            genderChartOptions,
            maleSeries,
            femaleSeries,
            analysisComment,
            fetchData,
            updateGroups,
            calculateTotal,
            getFullSectorName,
            updateCharts,
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