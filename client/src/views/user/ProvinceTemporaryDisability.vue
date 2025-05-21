<template>
    <div class="province-analysis-container">
        <HeaderApp />
        <div class="page-header">
            <h1>İllere Göre Geçici İş Göremezlik Analizi</h1>
            <p class="subtitle">2019-2023 yılları arası il bazlı geçici iş göremezlik verileri</p>
        </div>

        <div class="filters">
            <!-- Şehir Seçimi -->
            <div class="filter-group">
                <label for="province">İl:</label>
                <select id="province" v-model="selectedProvince" @change="fetchData">
                    <option value="all">Tüm İller</option>
                    <option v-for="province in provinces" :key="province.province_code" :value="province">
                        {{ province.province_code }} - {{ province.province_name }}
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

            <div class="filter-group">
                <label for="treatment">Tedavi Türü:</label>
                <select id="treatment" v-model="selectedTreatment" @change="fetchData">
                    <option value="all">Tüm Tedaviler</option>
                    <option value="1">Ayakta Tedavi</option>
                    <option value="0">Yatarak Tedavi</option>
                </select>
            </div>

            <div v-if="selectedProvince !== 'all'" class="filter-group">
                <label for="duration">Süre:</label>
                <select id="duration" v-model="selectedDuration" @change="updateCharts">
                    <option value="total">Toplam Vakalar</option>
                    <option value="1">1 Gün</option>
                    <option value="2">2 Gün</option>
                    <option value="3">3 Gün</option>
                    <option value="4">4 Gün</option>
                    <option value="5+">5+ Gün</option>
                </select>
            </div>
        </div>

        <!-- Ana Grafik -->
        <div class="chart-container" v-if="selectedProvince === 'all' && !loading">
            <h2>İl Bazlı İş Göremezlik Karşılaştırması</h2>
            <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
        </div>

        <!-- Detay Grafik (Tek il seçildiğinde) -->
        <div class="detail-charts" v-if="selectedProvince !== 'all' && !loading">
            <div class="chart-container">
                <h2>{{ selectedProvinceName }} - Yıllara Göre Dağılım</h2>
                <apexchart type="line" height="350" :options="yearlyChartOptions" :series="yearlySeries"></apexchart>
            </div>

            <div class="chart-container">
                <h2>{{ selectedProvinceName }} - Süre Dağılımı</h2>
                <apexchart type="pie" height="350" :options="durationChartOptions" :series="durationSeries"></apexchart>
            </div>

            <div class="chart-container">
                <h2>{{ selectedProvinceName }} - Cinsiyet Dağılımı</h2>
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

        <div class="analysis-container" v-if="analysisComment && !loading">
            <h2>Analiz ve Yorum</h2>
            <p><em>Bu analiz ISO 45001 standartlarına uygun olarak yapay zeka tarafından oluşturulmuştur.</em></p>
            <div class="analysis-comment">
                <div v-html="formatAnalysis(analysisComment)"></div>
            </div>
        </div>

        <!-- Veri Tablosu -->
        <div class="data-table" v-if="tableData.length > 0 && !loading">
            <h2>Detaylı Veri Tablosu</h2>
            <table>
                <thead>
                    <tr>
                        <th>İl Kodu</th>
                        <th>İl Adı</th>
                        <th>Yıl</th>
                        <th>Tedavi Türü</th>
                        <th>1 Gün</th>
                        <th>2 Gün</th>
                        <th>3 Gün</th>
                        <th>4 Gün</th>
                        <th>5+ Gün</th>
                        <th>Toplam Gün</th>
                        <th>Erkek</th>
                        <th>Kadın</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in tableData" :key="`${item.province_code}-${item.year}-${item.is_outpatient}`">
                        <td>{{ item.province_code }}</td>
                        <td>{{ item.province_name }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ item.is_outpatient ? 'Ayakta' : 'Yatarak' }}</td>
                        <td>{{ item.one_day_unfit || 0 }}</td>
                        <td>{{ item.two_days_unfit || 0 }}</td>
                        <td>{{ item.three_days_unfit || 0 }}</td>
                        <td>{{ item.four_days_unfit || 0 }}</td>
                        <td>{{ item.five_or_more_days_unfit || 0 }}</td>
                        <td>{{ calculateTotalDays(item) }}</td>
                        <td>{{ item.male_count || 0 }}</td>
                        <td>{{ item.female_count || 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="loading" v-if="loading">
            <div class="spinner"></div>
            <p>Analiz ve veriler yükleniyor. Lütfen Bekleyiniz...</p>
        </div>
        <FooterApp />
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import VueApexCharts from 'vue3-apexcharts'
import HeaderApp from '@/components/user/HeaderApp.vue'
import FooterApp from '@/components/user/FooterApp.vue'

export default {
    components: {
        apexchart: VueApexCharts,
        HeaderApp,
        FooterApp
    },
    setup() {
        const loading = ref(true)
        const tableData = ref([])
        const provinces = ref([])
        const analysisComment = ref([])
        const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
        const selectedYear = ref('all')
        const selectedProvince = ref('all')
        const selectedTreatment = ref('all')
        const selectedDuration = ref('total')

        // Grafik verileri
        const mainSeries = ref([{ name: 'Vaka Sayısı', data: [] }])
        const yearlySeries = ref([{ name: 'Vaka Sayısı', data: [] }])
        const durationSeries = ref([])
        const maleSeries = ref([0])
        const femaleSeries = ref([0])

        const selectedProvinceName = computed(() => {
            return selectedProvince.value === 'all' ? 'Tüm İller' : selectedProvince.value.province_name
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
                title: { text: 'Vaka Sayısı' },
                labels: {
                    formatter: function (val) {
                        return Math.round(val).toLocaleString()
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        const province = provinces.value.find(p => p.province_code === value)
                        return province ? province.province_name : value
                    }
                }
            },
            colors: ['#3b82f6'],
            tooltip: {
                y: {
                    formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                        const provinceCode = w.config.series[seriesIndex].data[dataPointIndex].x
                        const province = provinces.value.find(p => p.province_code === provinceCode)
                        return `${province?.province_name || provinceCode}: ${value} vaka`
                    }
                }
            }
        })

        const yearlyChartOptions = ref({
            chart: { type: 'line', height: 350 },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 5 },
            xaxis: { categories: availableYears.value },
            yaxis: { title: { text: 'Vaka Sayısı' } },
            colors: ['#3b82f6'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' vaka'
                    }
                }
            }
        })

        const durationChartOptions = ref({
            chart: { type: 'pie', height: 350 },
            labels: ['1 Gün', '2 Gün', '3 Gün', '4 Gün', '5+ Gün'],
            colors: ['#4299e1', '#3182ce', '#2b6cb0', '#2c5282', '#2a4365'],
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 } }
            }]
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
            if (!text) return '';

            // Convert numbered headings
            text = text.replace(/^(\d+\.\s+.+)$/gm, '<h2 class="analysis-heading">$1</h2>');

            // Convert bullet points
            text = text.replace(/^- (.+)$/gm, '<li class="analysis-list-item">$1</li>');
            text = text.replace(/(<li class="analysis-list-item">.*<\/li>)+/gs,
                '<ul class="analysis-list">$&</ul>');

            // Convert line breaks
            text = text.replace(/\n/g, '<br>');

            // Format paragraphs
            text = text.replace(/^(?!<h\d|<\/?ul>)(.+)$/gm,
                '<p class="analysis-paragraph">$1</p>');

            return text;
        };

        // Veri çekme fonksiyonları
        const fetchData = async () => {
            loading.value = true
            try {
                const params = {
                    year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
                    province_code: selectedProvince.value !== 'all' ? selectedProvince.value.province_code : undefined,
                    is_outpatient: selectedTreatment.value !== 'all' ? selectedTreatment.value : undefined
                }

                const response = await axios.get('/api/temporary-disability-day-by-province-user', { params })
                tableData.value = response.data.data
                analysisComment.value = response.data.analysis
                updateCharts()
            } catch (error) {
                console.error('Veri alınırken hata:', error)
            } finally {
                loading.value = false
            }
        }

        const fetchProvinces = async () => {
            try {
                const response = await axios.get('/api/provinces')
                provinces.value = response.data
            } catch (error) {
                console.error('İl verileri alınırken hata:', error)
            }
        }

        // Grafik güncelleme fonksiyonları
        const updateCharts = () => {
            if (selectedProvince.value === 'all') {
                updateMainChart()
            } else {
                updateDetailCharts()
            }
        }

        const updateMainChart = () => {
            const provinceData = {}

            tableData.value.forEach(item => {
                if (!provinceData[item.province_code]) {
                    provinceData[item.province_code] = {
                        name: item.province_name,
                        total: 0,
                        days: [0, 0, 0, 0, 0], // 1-5+ günler
                        male_count: 0,
                        female_count: 0
                    }
                }

                provinceData[item.province_code].total += calculateTotalCases(item)
                provinceData[item.province_code].days[0] += item.one_day_unfit || 0
                provinceData[item.province_code].days[1] += item.two_days_unfit || 0
                provinceData[item.province_code].days[2] += item.three_days_unfit || 0
                provinceData[item.province_code].days[3] += item.four_days_unfit || 0
                provinceData[item.province_code].days[4] += item.five_or_more_days_unfit || 0
                provinceData[item.province_code].male_count += item.male_count || 0
                provinceData[item.province_code].female_count += item.female_count || 0
            })

            // Sıralama ve en fazla 20 il
            const sortedProvinces = Object.entries(provinceData)
                .sort((a, b) => {
                    if (selectedDuration.value === 'total') {
                        return b[1].total - a[1].total
                    } else if (selectedDuration.value === '5+') {
                        return b[1].days[4] - a[1].days[4]
                    } else {
                        const dayIndex = parseInt(selectedDuration.value) - 1
                        return b[1].days[dayIndex] - a[1].days[dayIndex]
                    }
                })
                .slice(0, 20)

            // Grafik verilerini güncelle
            mainSeries.value = [{
                name: getDurationTitle(),
                data: sortedProvinces.map(([code, data]) => ({
                    x: data.name,
                    y: selectedDuration.value === 'total' ? data.total :
                        selectedDuration.value === '5+' ? data.days[4] :
                            data.days[parseInt(selectedDuration.value) - 1],
                    province_code: code
                }))
            }]

            // Eksen etiketlerini güncelle
            mainChartOptions.value.xaxis.title.text = getDurationTitle()
        }

        const updateDetailCharts = () => {
            const yearlyData = {}
            availableYears.value.forEach(year => {
                yearlyData[year] = 0
            })

            const durationData = [0, 0, 0, 0, 0] // 1-5+ günler
            let maleTotal = 0
            let femaleTotal = 0

            tableData.value.forEach(item => {
                const cases = calculateTotalCases(item)
                yearlyData[item.year] += cases

                durationData[0] += item.one_day_unfit || 0
                durationData[1] += item.two_days_unfit || 0
                durationData[2] += item.three_days_unfit || 0
                durationData[3] += item.four_days_unfit || 0
                durationData[4] += item.five_or_more_days_unfit || 0

                maleTotal += item.male_count || 0
                femaleTotal += item.female_count || 0
            })

            yearlySeries.value = [{
                name: 'Vaka Sayısı',
                data: availableYears.value.map(year => yearlyData[year])
            }]

            durationSeries.value = durationData

            const total = maleTotal + femaleTotal
            maleSeries.value = [total > 0 ? Math.round((maleTotal / total) * 100) : 0]
            femaleSeries.value = [total > 0 ? Math.round((femaleTotal / total) * 100) : 0]
        }

        // Yardımcı fonksiyonlar
        const calculateTotalCases = (item) => {
            return (item.one_day_unfit || 0) +
                (item.two_days_unfit || 0) +
                (item.three_days_unfit || 0) +
                (item.four_days_unfit || 0) +
                (item.five_or_more_days_unfit || 0)
        }

        const calculateTotalDays = (item) => {
            return (item.one_day_unfit || 0) * 1 +
                (item.two_days_unfit || 0) * 2 +
                (item.three_days_unfit || 0) * 3 +
                (item.four_days_unfit || 0) * 4 +
                (item.five_or_more_days_unfit || 0) * 5 // Minimum 5 gün varsayımı
        }

        const getDurationTitle = () => {
            switch (selectedDuration.value) {
                case 'total': return 'Toplam Vaka Sayısı'
                case '1': return '1 Günlük Vakalar'
                case '2': return '2 Günlük Vakalar'
                case '3': return '3 Günlük Vakalar'
                case '4': return '4 Günlük Vakalar'
                case '5+': return '5+ Günlük Vakalar'
                default: return 'Vaka Sayısı'
            }
        }

        // Sayfa yüklendiğinde verileri çek
        onMounted(async () => {
            await fetchProvinces()
            await fetchData()
        })

        return {
            loading,
            tableData,
            provinces,
            availableYears,
            selectedYear,
            selectedProvince,
            selectedTreatment,
            selectedDuration,
            selectedProvinceName,
            mainChartOptions,
            mainSeries,
            yearlyChartOptions,
            yearlySeries,
            durationChartOptions,
            durationSeries,
            genderChartOptions,
            maleSeries,
            femaleSeries,
            analysisComment,
            fetchData,
            calculateTotalCases,
            calculateTotalDays,
            updateCharts,
            formatAnalysis
        }
    }
}
</script>


<style scoped>
.province-analysis-container {
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

.province-analysis-container {
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