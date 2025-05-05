<template>
    <div class="sector-analysis-container">
        <div class="page-header">
            <h1>Sektörlere Göre İş Kazaları Analizi</h1>
            <p class="subtitle">2019-2023 yılları arası sektörel iş kazası verileri</p>
        </div>

        <div class="filters">
            <!-- Sektör Seçimi (En Üst Seviye) -->
            <div class="filter-group">
                <label for="sector">Sektör:</label>
                <select id="sector" v-model="selectedSector" @change="updateGroups">
                    <option value="all">Tüm Sektörler</option>
                    <option v-for="sector in uniqueSectors" :key="sector.sector_code" :value="sector">
                        {{ sector.sector_code }} - {{ sector.sector_name }}
                    </option>
                </select>
            </div>
            <!-- Diğer filtreler aynı kalacak -->
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
                    <option value="total">Toplam Kaza Sayısı</option>
                    <option value="unfit">İş Göremezlik</option>
                    <option value="disease">Meslek Hastalığı</option>
                </select>
            </div>
        </div>

        <!-- Ana Grafik -->
        <div class="chart-container" v-if="selectedSector === 'all' && !loading">
            <h2>Sektör Karşılaştırması</h2>
            <p v-if="selectedGroup !== 'all'">{{ selectedGroup.group_name }} sektörleri karşılaştırması</p>
            <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
        </div>

        <!-- Detay Grafik (Tek sektör seçildiğinde) -->
        <div class="detail-charts" v-if="selectedSector !== 'all' && !loading">
            <div class="chart-container">
                <h2>{{ selectedSectorName }} - Yıllara Göre Dağılım</h2>
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
                        <th>Sektör Kodu</th>
                        <th>Tam Sektör Adı</th>
                        <th>Yıl</th>
                        <th>Toplam Kaza</th>
                        <th>İş Göremezlik</th>
                        <th>Meslek Hastalığı</th>
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
                        <td>{{ calculateUnfit(item) }}</td>
                        <td>{{ item.occupational_disease_cases }}</td>
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
        const sectors = ref([])
        const analysisComment = ref([])
        const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
        const selectedYear = ref('all')
        const selectedSector = ref('all')
        const selectedGroup = ref('all')
        const selectedSubGroup = ref('all')
        const selectedMetric = ref('total')

        // Grafik verileri
        const mainSeries = ref([{ name: 'Kaza Sayısı', data: [] }])
        const yearlySeries = ref([{ name: 'Kaza Sayısı', data: [] }])
        const maleSeries = ref([0])
        const femaleSeries = ref([0])

        // Sektör hiyerarşisi için computed özellikler
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

        const filteredGroups = computed(() => {
            if (selectedSector.value === 'all') return []
            const groupsMap = {}
            sectors.value.forEach(item => {
                if (item.sector_code === selectedSector.value.sector_code && !groupsMap[item.group_code]) {
                    groupsMap[item.group_code] = {
                        group_code: item.group_code,
                        group_name: item.group_name
                    }
                }
            })
            return Object.values(groupsMap)
        })

        const selectedSectorName = computed(() => {
            if (selectedSector.value === 'all') return 'Tüm Sektörler'
            const sector = sectors.value.find(s => s.sector_code === selectedSector.value)
            return sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : ''
        })

        // Grafik ayarları
        const mainChartOptions = ref({
            chart: {
                type: 'bar',
                height: 500,
                toolbar: { show: true },
                animations: { enabled: false }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: { enabled: true, style: { fontSize: '10px' } },
            xaxis: { categories: [], title: { text: 'Kaza Sayısı' } },
            yaxis: {
                labels: {
                    show: true,
                    style: { fontSize: '10px' },
                    formatter: function (value) {
                        const sector = sectors.value.find(s => s.sector_code === value)
                        return sector ? sector.pure_name : value
                    }
                }
            },
            colors: ['#3b82f6'],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                        const sectorCode = w.config.series[seriesIndex].data[dataPointIndex].x
                        const sector = sectors.value.find(s => s.sector_code === sectorCode)
                        const sectorName = sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : sectorCode
                        return `${sectorName}: ${value}`
                    }
                }
            }
        })

        const yearlyChartOptions = ref({
            chart: { type: 'line', height: 350 },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 5 },
            xaxis: { categories: availableYears.value },
            yaxis: { title: { text: 'Kaza Sayısı' } },
            colors: ['#3b82f6'],
            tooltip: { shared: true }
        })

        const genderChartOptions = (gender) => ({
            chart: { type: 'radialBar', height: 300 },
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: { margin: 0, size: '70%' },
                    dataLabels: {
                        name: { fontSize: '16px', color: gender === 'Erkek' ? '#3b82f6' : '#ec4899' },
                        value: { fontSize: '24px', formatter: (val) => val + '%' }
                    }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'dark', shadeIntensity: 0.15 } },
            colors: [gender === 'Erkek' ? '#3b82f6' : '#ec4899'],
            labels: [gender]
        })

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
        // Filtre değişiklikleri
        // Filtre Değişiklik Fonksiyonları
        const updateGroups = () => {
            selectedGroup.value = 'all'
            selectedSubGroup.value = 'all'
            fetchData()
        }

        const updateSubGroups = () => {
            selectedSubGroup.value = 'all'
            fetchData()
        }
        // Veri çekme
        const fetchData = async () => {
            loading.value = true
            try {
                const params = {
                    year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
                    sector_code: selectedSector.value !== 'all' ? selectedSector.value : undefined,
                    group_code: selectedGroup.value !== 'all' ? selectedGroup.value.group_code : undefined,
                    sub_group_code: selectedSubGroup.value !== 'all' ? selectedSubGroup.value.sub_group_code : undefined
                }

                const response = await axios.get('https://iskazalarianaliz.com/api/work-accidents-by-sector-user', { params })
                tableData.value = response.data.data
                analysisComment.value = response.data.analysis;
                updateCharts()
            } catch (error) {
                console.error('Veri alınırken hata:', error)
            } finally {
                loading.value = false
            }
        }

        // Sektör verilerini yükle
        const fetchSectors = async () => {
            try {
                const response = await axios.get('https://iskazalarianaliz.com/api/sectors-user')
                sectors.value = response.data
            } catch (error) {
                console.error('Sektör verileri alınırken hata:', error)
            }
        }

        // Grafikleri güncelle
        const updateCharts = () => {
            if (selectedSector.value === 'all') {
                updateMainChart()
            } else {
                updateDetailCharts()
            }
        }

        // Ana grafiği güncelle (tüm sektörler)
        const updateMainChart = () => {
            const sectorData = {};
            const sectorCodeToData = {}; // Sektör koduna göre veri erişimi için

            tableData.value.forEach(item => {
                if (!sectorData[item.sector_code]) {
                    sectorData[item.sector_code] = {
                        name: item.pure_name,
                        total: 0,
                        works_on_accident_day: 0,
                        unfit_on_accident_day: 0,
                        two_days_unfit: 0,
                        three_days_unfit: 0,
                        four_days_unfit: 0,
                        five_or_more_days_unfit: 0,
                        disease: 0,
                        male_count: 0,
                        female_count: 0
                    };
                    sectorCodeToData[item.sector_code] = sectorData[item.sector_code]; // Eşleme ekle

                }

                // Toplamları güncelle
                const total = calculateTotal(item);
                sectorData[item.sector_code].total += total;
                sectorData[item.sector_code].works_on_accident_day += item.works_on_accident_day || 0;
                sectorData[item.sector_code].unfit_on_accident_day += item.unfit_on_accident_day || 0;
                sectorData[item.sector_code].two_days_unfit += item.two_days_unfit || 0;
                sectorData[item.sector_code].three_days_unfit += item.three_days_unfit || 0;
                sectorData[item.sector_code].four_days_unfit += item.four_days_unfit || 0;
                sectorData[item.sector_code].five_or_more_days_unfit += item.five_or_more_days_unfit || 0;
                sectorData[item.sector_code].disease += item.occupational_disease_cases || 0;
                sectorData[item.sector_code].male_count += item.male_count || 0;
                sectorData[item.sector_code].female_count += item.female_count || 0;
            });

            // Sıralama ve en iyi 20 sektörü seçme
            const sortedSectors = Object.entries(sectorData)
                .sort((a, b) => b[1][selectedMetric.value] - a[1][selectedMetric.value])
                .slice(0, 20);

            // Grafik seçeneklerini güncelleme
            mainChartOptions.value = {
                ...mainChartOptions.value,
                chart: {
                    ...mainChartOptions.value.chart,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                xaxis: {
                    ...mainChartOptions.value.xaxis,
                    title: {
                        text: getMetricTitle(),
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            color: '#333'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px',
                            colors: '#666'
                        },
                        formatter: function (value) {
                            return value.toLocaleString();
                        }
                    }
                },
                yaxis: {
                    ...mainChartOptions.value.yaxis,
                    labels: {
                        show: true, // Bu mutlaka true olmalı
                        style: {
                            fontSize: '12px',
                            colors: '#333'
                        },
                        formatter: function (value) {
                            // Sektör adını doğrudan döndür
                            return value;
                        }
                    },
                    title: {
                        text: 'Sektörler',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            color: '#333'
                        }
                    }
                },
                tooltip: {
                    ...mainChartOptions.value.tooltip,
                    enabled: true,
                    shared: true,
                    intersect: false,
                    custom: ({ seriesIndex, dataPointIndex, w }) => {
                        try {
                            // Veri noktasını doğru şekilde al
                            const dataPoint = w.config.series[seriesIndex].data[dataPointIndex];
                            const sectorCode = dataPoint.sector_code || dataPoint.x; // Geriye dönük uyumluluk
                            const sector = sectorCodeToData[sectorCode] || sectorData[sectorCode];

                            if (!sector) {
                                console.error('Sector data not found for code:', sectorCode);
                                return '<div class="tooltip-error">Veri yükleniyor...</div>';
                            }

                            return `
                        <div class="apexcharts-tooltip-custom">
                            <div class="tooltip-title">${sector.name || 'Bilinmeyen Sektör'}</div>
                            <div class="tooltip-row"><span>Toplam Kaza:</span> ${(sector.total || 0).toLocaleString()}</div>
                            <div class="tooltip-section-title">İş Göremezlik:</div>
                            <div class="tooltip-row"><span>Aynı Gün:</span> ${(sector.unfit_on_accident_day || 0).toLocaleString()}</div>
                            <div class="tooltip-row"><span>2 Gün:</span> ${(sector.two_days_unfit || 0).toLocaleString()}</div>
                            <div class="tooltip-row"><span>3 Gün:</span> ${(sector.three_days_unfit || 0).toLocaleString()}</div>
                            <div class="tooltip-row"><span>4 Gün:</span> ${(sector.four_days_unfit || 0).toLocaleString()}</div>
                            <div class="tooltip-row"><span>5+ Gün:</span> ${(sector.five_or_more_days_unfit || 0).toLocaleString()}</div>
                        </div>
                    `;
                        } catch (error) {
                            console.error('Tooltip rendering error:', error);
                            return '<div class="tooltip-error">Veri gösterilemiyor</div>';
                        }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toLocaleString();
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold',
                        colors: ['#fff']
                    },
                    background: {
                        enabled: true,
                        foreColor: '#333',
                        borderRadius: 4,
                        opacity: 0.8
                    }
                }
            };

            // Grafik serisini güncelleme
            mainSeries.value = [{
                name: getMetricTitle(),
                data: sortedSectors.map(([code, data]) => ({
                    x: data.name, // Sektör adını x ekseninde kullan
                    y: selectedMetric.value === 'total' ? data.total :
                        selectedMetric.value === 'unfit' ? (data.unfit_on_accident_day + data.two_days_unfit +
                            data.three_days_unfit + data.four_days_unfit +
                            data.five_or_more_days_unfit) :
                            data.disease,
                    sector_code: code // Sektör kodunu meta olarak sakla
                }))
            }];
        };

        // Detay grafikleri güncelle (tek sektör)
        const updateDetailCharts = () => {
            // Yıllara göre veri
            const yearlyData = {}
            availableYears.value.forEach(year => {
                yearlyData[year] = 0
            })

            // Cinsiyet dağılımı
            let maleTotal = 0
            let femaleTotal = 0

            tableData.value.forEach(item => {
                if (selectedMetric.value === 'total') {
                    yearlyData[item.year] += calculateTotal(item)
                } else if (selectedMetric.value === 'unfit') {
                    yearlyData[item.year] += calculateUnfit(item)
                } else {
                    yearlyData[item.year] += item.occupational_disease_cases || 0
                }

                maleTotal += item.male_count || 0
                femaleTotal += item.female_count || 0
            })

            // Yıllık grafik
            yearlySeries.value = [{
                name: getMetricTitle(),
                data: availableYears.value.map(year => yearlyData[year])
            }]

            // Cinsiyet grafikleri
            const total = maleTotal + femaleTotal
            maleSeries.value = [total > 0 ? Math.round((maleTotal / total) * 100) : 0]
            femaleSeries.value = [total > 0 ? Math.round((femaleTotal / total) * 100) : 0]
        }

        // Yardımcı fonksiyonlar
        const calculateTotal = (item) => {
            return (item.works_on_accident_day || 0) +
                (item.unfit_on_accident_day || 0) +
                (item.two_days_unfit || 0) +
                (item.three_days_unfit || 0) +
                (item.four_days_unfit || 0) +
                (item.five_or_more_days_unfit || 0)
        }

        const calculateUnfit = (item) => {
            return (item.unfit_on_accident_day || 0) +
                (item.two_days_unfit || 0) +
                (item.three_days_unfit || 0) +
                (item.four_days_unfit || 0) +
                (item.five_or_more_days_unfit || 0)
        }

        const getMetricTitle = () => {
            switch (selectedMetric.value) {
                case 'total': return 'Toplam Kaza Sayısı'
                case 'unfit': return 'İş Göremezlik Vakaları'
                case 'disease': return 'Meslek Hastalığı Vakaları'
                default: return 'Kaza Sayısı'
            }
        }

        const getFullSectorName = (sectorCode) => {
            const sector = sectors.value.find(s => s.sector_code === sectorCode)
            return sector ? `${sector.group_name} > ${sector.sub_group_name} > ${sector.pure_name}` : sectorCode
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
            selectedGroup,
            selectedSubGroup,
            selectedSector,
            selectedMetric,
            uniqueSectors,
            filteredGroups,
            selectedSectorName,
            mainChartOptions,
            mainSeries,
            yearlyChartOptions,
            yearlySeries,
            genderChartOptions,
            maleSeries,
            femaleSeries,
            fetchData,
            updateSubGroups,
            updateGroups,
            calculateTotal,
            calculateUnfit,
            getFullSectorName,
            updateCharts,
            formatAnalysis,
            analysisComment
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