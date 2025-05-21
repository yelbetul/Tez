<template>
  <div class="workstation-analysis-container">
    <HeaderApp />
    <div class="page-header">
      <h1>Çalışma Saatlerine Göre İş Kazaları Analizi</h1>
      <p class="subtitle">2019-2023 yılları arası çalışma saatlerine göre iş kazası verileri</p>
    </div>

    <div class="filters">
      <!-- Zaman Aralığı Seçimi -->
      <div class="filter-group">
        <label for="timeInterval">Zaman Aralığı:</label>
        <select id="timeInterval" v-model="selectedTimeInterval" @change="fetchData">
          <option value="all">Tüm Zaman Aralıkları</option>
          <option v-for="interval in availableTimeIntervals" :key="interval.code" :value="interval.code">
            {{ interval.time_interval }}
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

      <div class="filter-group">
        <label for="metric">Gösterge:</label>
        <select id="metric" v-model="selectedMetric" @change="updateCharts">
          <option value="total">Toplam Vaka</option>
          <option value="unfit">İş Göremezlik</option>
        </select>
      </div>
    </div>

    <!-- Ana Grafik -->
    <div class="chart-container" v-if="selectedTimeInterval === 'all' && !loading">
      <h2>Zaman Aralığına Göre Dağılım</h2>
      <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
    </div>

    <!-- Yıllara Göre Grafik (Zaman aralığı seçildiğinde ve tüm yıllar seçiliyse) -->
    <div class="chart-container" v-if="selectedTimeInterval !== 'all' && selectedYear === 'all' && !loading">
      <h2>{{ selectedTimeIntervalName }} - Yıllara Göre Dağılım</h2>
      <apexchart type="line" height="350" :options="yearlyTrendChartOptions" :series="yearlyTrendSeries"></apexchart>
    </div>

    <!-- Cinsiyet Dağılımı -->
    <div class="chart-container" v-if="!loading">
      <h2>Cinsiyet Dağılımı</h2>
      <div class="gender-charts">
        <div class="gender-chart">
          <h3>Erkek</h3>
          <apexchart type="radialBar" height="300" :options="genderChartOptions('Erkek')" :series="maleSeries">
          </apexchart>
        </div>
        <div class="gender-chart">
          <h3>Kadın</h3>
          <apexchart type="radialBar" height="300" :options="genderChartOptions('Kadın')" :series="femaleSeries">
          </apexchart>
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
            <th>Zaman Aralığı</th>
            <th>Yıl</th>
            <th>Kaza Günü Çalışır</th>
            <th>Kaza Günü İş Göremez</th>
            <th>2 Gün İş Göremez</th>
            <th>3 Gün İş Göremez</th>
            <th>4 Gün İş Göremez</th>
            <th>5+ Gün İş Göremez</th>
            <th>Toplam İş Göremezlik</th>
            <th>Erkek</th>
            <th>Kadın</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in tableData" :key="`${item.code}-${item.year}`">
            <td>{{ item.time_interval }}</td>
            <td>{{ item.year }}</td>
            <td>{{ item.works_on_accident_day || 0 }}</td>
            <td>{{ item.unfit_on_accident_day || 0 }}</td>
            <td>{{ item.two_days_unfit || 0 }}</td>
            <td>{{ item.three_days_unfit || 0 }}</td>
            <td>{{ item.four_days_unfit || 0 }}</td>
            <td>{{ item.five_or_more_days_unfit || 0 }}</td>
            <td>{{ calculateUnfit(item) }}</td>
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
    const analysisComment = ref([])
    const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
    const availableTimeIntervals = ref([])

    // Seçimler
    const selectedYear = ref('all')
    const selectedTimeInterval = ref('all')
    const selectedGender = ref('all')
    const selectedMetric = ref('total')

    // Grafik verileri
    const mainSeries = ref([{ name: 'Vaka Sayısı', data: [] }])
    const yearlyTrendSeries = ref([{ name: 'Vaka Sayısı', data: [] }])
    const maleSeries = ref([0])
    const femaleSeries = ref([0])

    const selectedTimeIntervalName = computed(() => {
      if (selectedTimeInterval.value === 'all') return ''
      const interval = availableTimeIntervals.value.find(t => t.code === selectedTimeInterval.value)
      return interval ? interval.time_interval : ''
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
          horizontal: false,
          borderRadius: 4,
          dataLabels: { position: 'top' }
        }
      },
      dataLabels: { enabled: true },
      xaxis: {
        type: 'category',
        title: { text: 'Zaman Aralıkları' },
        categories: []
      },
      yaxis: {
        title: { text: 'Vaka Sayısı' },
        labels: {
          formatter: function (val) {
            return Math.round(val).toLocaleString()
          }
        }
      },
      colors: ['#ef4444'],
      tooltip: {
        y: {
          formatter: function (val) {
            return val + ' vaka'
          }
        }
      }
    })

    const yearlyTrendChartOptions = ref({
      chart: { type: 'line', height: 350 },
      stroke: { curve: 'smooth', width: 3 },
      markers: { size: 5 },
      xaxis: { categories: availableYears.value },
      yaxis: { title: { text: 'Vaka Sayısı' } },
      colors: ['#ef4444'],
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

    // Veri çekme fonksiyonları
    const fetchData = async () => {
      loading.value = true
      try {
        const params = {
          year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
          time_interval: selectedTimeInterval.value !== 'all' ? selectedTimeInterval.value : undefined,
          gender: selectedGender.value !== 'all' ? selectedGender.value : undefined
        }

        const response = await axios.get('/api/work-accidents-by-hours-user', { params })
        tableData.value = response.data.data
        analysisComment.value = response.data.analysis
        updateCharts()
      } catch (error) {
        console.error('Veri alınırken hata:', error)
      } finally {
        loading.value = false
      }
    }

    // Zaman aralıklarını yükle
    const loadTimeIntervals = async () => {
      try {
        const response = await axios.get('/api/time-intervals-user')
        availableTimeIntervals.value = response.data.map(item => ({
          code: item.code,
          time_interval: item.time_interval
        }))
      } catch (error) {
        console.error('Zaman aralıkları yüklenirken hata:', error)
      }
    }

    // Grafik güncelleme fonksiyonları
    const updateCharts = () => {
      if (selectedTimeInterval.value === 'all') {
        updateMainChart()
      } else if (selectedYear.value === 'all') {
        updateYearlyTrendChart()
      }
      updateGenderChart()
    }

    const updateMainChart = () => {
      const intervalData = {}

      // Zaman aralıklarına göre verileri grupla
      tableData.value.forEach(item => {
        if (!intervalData[item.code]) {
          intervalData[item.code] = {
            name: item.time_interval,
            total: 0,
            unfit: 0,
            male_count: 0,
            female_count: 0
          }
        }

        const total = calculateTotal(item)
        intervalData[item.code].total += total
        intervalData[item.code].unfit += calculateUnfit(item)
        intervalData[item.code].male_count += item.male_count || 0
        intervalData[item.code].female_count += item.female_count || 0
      })

      // Grafik verilerini hazırla
      const categories = []
      const seriesData = []

      Object.keys(intervalData).forEach(code => {
        categories.push(intervalData[code].name)
        seriesData.push({
          x: intervalData[code].name,
          y: selectedMetric.value === 'total' ? intervalData[code].total : intervalData[code].unfit
        })
      })

      // Grafik verilerini güncelle
      mainSeries.value = [{
        name: getMetricTitle(),
        data: seriesData
      }]

      // Eksen etiketlerini güncelle
      mainChartOptions.value.xaxis.categories = categories
      mainChartOptions.value.yaxis.title.text = getMetricTitle()
    }

    const updateYearlyTrendChart = () => {
      const yearlyData = {}
      availableYears.value.forEach(year => {
        yearlyData[year] = 0
      })

      tableData.value.forEach(item => {
        if (selectedMetric.value === 'total') {
          yearlyData[item.year] += calculateTotal(item)
        } else {
          yearlyData[item.year] += calculateUnfit(item)
        }
      })

      yearlyTrendSeries.value = [{
        name: getMetricTitle(),
        data: availableYears.value.map(year => yearlyData[year])
      }]
    }

    const updateGenderChart = () => {
      let maleTotal = 0
      let femaleTotal = 0

      tableData.value.forEach(item => {
        maleTotal += item.male_count || 0
        femaleTotal += item.female_count || 0
      })

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
        case 'total': return 'Toplam Vaka Sayısı'
        case 'unfit': return 'İş Göremezlik Vakaları'
        default: return 'Vaka Sayısı'
      }
    }

    // Sayfa yüklendiğinde verileri çek
    onMounted(async () => {
      await loadTimeIntervals()
      await fetchData()
    })

    return {
      loading,
      tableData,
      availableYears,
      availableTimeIntervals,
      selectedYear,
      selectedTimeInterval,
      selectedTimeIntervalName,
      selectedGender,
      selectedMetric,
      mainChartOptions,
      mainSeries,
      yearlyTrendChartOptions,
      yearlyTrendSeries,
      genderChartOptions,
      maleSeries,
      femaleSeries,
      analysisComment,
      fetchData,
      calculateTotal,
      calculateUnfit,
      updateCharts,
      formatAnalysis
    }
  }
}
</script>

<style scoped>
.workstation-analysis-container {
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

.workstation-analysis-container {
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
}</style>
