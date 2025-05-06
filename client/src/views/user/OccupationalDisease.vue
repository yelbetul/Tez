<template>
    <div class="disease-analysis-container">
        <div class="page-header">
            <h1>Tanı Gruplarına Göre Meslek Hastalıkları Analizi</h1>
            <p class="subtitle">Tanı grupları bazında meslek hastalığı verileri</p>
        </div>

        <div class="filters">
            <!-- Tanı Grubu Seçimi -->
            <div class="filter-group">
                <label for="group">Tanı Grubu:</label>
                <select id="group" v-model="selectedGroup" @change="fetchData">
                    <option value="all">Tüm Gruplar</option>
                    <option v-for="group in availableGroups" :key="group.code" :value="group.code">
                        {{ group.code }} - {{ group.name }}
                    </option>
                </select>
            </div>

            <!-- Tanı Alt Grubu Seçimi -->
            <div class="filter-group" v-if="selectedGroup !== 'all'">
                <label for="subGroup">Tanı Alt Grubu:</label>
                <select id="subGroup" v-model="selectedSubGroup" @change="fetchData">
                    <option value="all">Tüm Alt Gruplar</option>
                    <option v-for="subGroup in filteredSubGroups" :key="subGroup.code" :value="subGroup.code">
                        {{ subGroup.code }} - {{ subGroup.name }}
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
                <label for="gender">Cinsiyet:</label>
                <select id="gender" v-model="selectedGender" @change="fetchData">
                    <option value="all">Tümü</option>
                    <option value="0">Erkek</option>
                    <option value="1">Kadın</option>
                </select>
            </div>
        </div>

        <!-- Ana Grafik -->
        <div class="chart-container" v-if="selectedGroup === 'all' && !loading">
            <h2>Tanı Gruplarına Göre Hastalık Dağılımı</h2>
            <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
        </div>

        <!-- Detay Grafik (Grup seçildiğinde) -->
        <div class="detail-charts" v-if="selectedGroup !== 'all' && !loading">
            <div class="chart-container">
                <h2>{{ selectedGroupName }} - Yıllara Göre Dağılım</h2>
                <apexchart type="line" height="350" :options="yearlyChartOptions" :series="yearlySeries"></apexchart>
            </div>

            <div class="chart-container">
                <h2>{{ selectedGroupName }} - Cinsiyet Dağılımı</h2>
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
                        <th>Tanı Grubu</th>
                        <th>Tanı Alt Grubu</th>
                        <th>Yıl</th>
                        <th>Hastalık Vakaları</th>
                        <th>Erkek</th>
                        <th>Kadın</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in tableData" :key="`${item.group_code}-${item.sub_group_code}-${item.year}`">
                        <td>{{ item.group_code }} - {{ item.group_name }}</td>
                        <td>{{ item.sub_group_code }} - {{ item.sub_group_name }}</td>
                        <td>{{ item.year }}</td>
                        <td>{{ item.occupational_disease_cases || 0 }}</td>
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
        const analysisComment = ref([])
        const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
        const diagnosisGroups = ref([])
        const selectedYear = ref('all')
        const selectedGroup = ref('all')
        const selectedSubGroup = ref('all')
        const selectedGender = ref('all')

        // Grafik verileri
        const mainSeries = ref([{ name: 'Hastalık Vakaları', data: [] }])
        const yearlySeries = ref([{ name: 'Hastalık Vakaları', data: [] }])
        const maleSeries = ref([0])
        const femaleSeries = ref([0])

        // Grafik ayarları
        const mainChartOptions = ref({
            chart: {
                type: 'bar',
                height: 500,
                toolbar: { show: true }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: { enabled: true },
            xaxis: {
                type: 'category',
                title: { text: 'Tanı Grupları' },
                labels: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            yaxis: {
                title: { text: 'Hastalık Vakaları' },
                labels: {
                    formatter: function (val) {
                        return Math.round(val).toLocaleString()
                    }
                }
            },
            colors: ['#3b82f6'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' vaka'
                    }
                }
            }
        })

        const yearlyChartOptions = ref({
            chart: { type: 'line', height: 350 },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 5 },
            xaxis: { categories: availableYears.value },
            yaxis: { title: { text: 'Hastalık Vakaları' } },
            colors: ['#3b82f6'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' vaka'
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

        // Computed properties
        const availableGroups = computed(() => {
            const groups = diagnosisGroups.value.reduce((acc, group) => {
                if (!acc.some(g => g.code === group.group_code)) {
                    acc.push({
                        code: group.group_code,
                        name: group.group_name
                    });
                }
                return acc;
            }, []);
            return groups.sort((a, b) => a.code.localeCompare(b.code));
        });

        const filteredSubGroups = computed(() => {
            if (selectedGroup.value === 'all') return [];
            return diagnosisGroups.value
                .filter(group => group.group_code === selectedGroup.value)
                .map(group => ({
                    code: group.sub_group_code,
                    name: group.sub_group_name
                }))
                .sort((a, b) => a.code.localeCompare(b.code));
        });

        const selectedGroupName = computed(() => {
            if (selectedGroup.value === 'all') return '';
            const group = availableGroups.value.find(g => g.code === selectedGroup.value);
            return group ? `${group.code} - ${group.name}` : '';
        });

        // Veri çekme fonksiyonları
        const fetchData = async () => {
            loading.value = true
            try {
                const params = {
                    year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
                    group_code: selectedGroup.value !== 'all' ? selectedGroup.value : undefined,
                    sub_group_code: selectedSubGroup.value !== 'all' ? selectedSubGroup.value : undefined,
                    gender: selectedGender.value !== 'all' ? selectedGender.value : undefined
                }

                const response = await axios.get('/api/occupational-disease-by-diagnosis-user', { params })
                tableData.value = response.data.data
                analysisComment.value = response.data.analysis
                updateCharts()
            } catch (error) {
                console.error('Veri alınırken hata:', error)
            } finally {
                loading.value = false
            }
        }

        const fetchDiagnosisGroups = async () => {
            try {
                const response = await axios.get('/api/diagnosis-user')
                diagnosisGroups.value = response.data
            } catch (error) {
                console.error('Tanı grupları alınırken hata:', error)
            }
        }

        // Grafik güncelleme fonksiyonları
        const updateCharts = () => {
            if (selectedGroup.value === 'all') {
                updateMainChart()
            } else {
                updateDetailCharts()
            }
        }

        const updateMainChart = () => {
            const groupData = {}

            tableData.value.forEach(item => {
                if (!groupData[item.group_code]) {
                    groupData[item.group_code] = {
                        name: item.group_name,
                        cases: 0,
                        male_count: 0,
                        female_count: 0
                    }
                }

                groupData[item.group_code].cases += item.occupational_disease_cases || 0
                groupData[item.group_code].male_count += item.male_count || 0
                groupData[item.group_code].female_count += item.female_count || 0
            })

            // Sıralama
            const sortedGroups = Object.entries(groupData)
                .sort((a, b) => a[0].localeCompare(b[0]))

            // Grafik verilerini güncelle
            mainSeries.value = [{
                name: 'Hastalık Vakaları',
                data: sortedGroups.map(([code, data]) => ({
                    x: `${code} - ${data.name}`,
                    y: data.cases
                }))
            }]

            // Eksen etiketlerini güncelle
            mainChartOptions.value.xaxis.title.text = 'Tanı Grupları'
            mainChartOptions.value.yaxis.title.text = 'Hastalık Vakaları'
        }

        const updateDetailCharts = () => {
            const yearlyData = {}
            availableYears.value.forEach(year => {
                yearlyData[year] = 0
            })

            let maleTotal = 0
            let femaleTotal = 0

            tableData.value.forEach(item => {
                yearlyData[item.year] += item.occupational_disease_cases || 0
                maleTotal += item.male_count || 0
                femaleTotal += item.female_count || 0
            })

            yearlySeries.value = [{
                name: 'Hastalık Vakaları',
                data: availableYears.value.map(year => yearlyData[year])
            }]

            const total = maleTotal + femaleTotal
            maleSeries.value = [total > 0 ? Math.round((maleTotal / total) * 100) : 0]
            femaleSeries.value = [total > 0 ? Math.round((femaleTotal / total) * 100) : 0]
        }

        // Sayfa yüklendiğinde verileri çek
        onMounted(async () => {
            await fetchDiagnosisGroups()
            await fetchData()
        })

        return {
            loading,
            tableData,
            availableYears,
            availableGroups,
            filteredSubGroups,
            selectedYear,
            selectedGroup,
            selectedSubGroup,
            selectedGender,
            selectedGroupName,
            mainChartOptions,
            mainSeries,
            yearlyChartOptions,
            yearlySeries,
            genderChartOptions,
            maleSeries,
            femaleSeries,
            analysisComment,
            fetchData,
            updateCharts,
            formatAnalysis
        }
    }
}
</script>


<style scoped>
.disease-analysis-container {
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

.disease-analysis-container {
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