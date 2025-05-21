<template>
  <div class="workstation-analysis-container">
    <HeaderApp />
    <div class="page-header">
      <h1>Çalışma Süresine Göre İş Kazaları Analizi</h1>
      <p class="subtitle">Çalışanların çalışma sürelerine göre iş kazaları ve ölüm verileri</p>
    </div>

    <div class="filters">
      <!-- Çalışma Süresi Seçimi -->
      <div class="filter-group">
        <label for="employmentDuration">Çalışma Süresi:</label>
        <select id="employmentDuration" v-model="selectedEmploymentDuration" @change="fetchData">
          <option value="all">Tüm Süreler</option>
          <option v-for="duration in availableEmploymentDurations" :key="duration.code" :value="duration.code">
            {{ duration.employment_duration }}
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
          <option value="total_cases">Toplam Kazalar</option>
          <option value="total_unfit">Uygunsuzluk Günleri</option>
          <option value="fatalities">Ölümlü Kazalar</option>
        </select>
      </div>
    </div>

    <!-- Ana Grafik -->
    <div class="chart-container" v-if="selectedEmploymentDuration === 'all' && !loading">
      <h2>Çalışma Süresine Göre Dağılım</h2>
      <apexchart type="bar" height="500" :options="mainChartOptions" :series="mainSeries"></apexchart>
    </div>

    <!-- Yıllara Göre Grafik (Süre seçildiğinde ve tüm yıllar seçiliyse) -->
    <div class="chart-container" v-if="selectedEmploymentDuration !== 'all' && selectedYear === 'all' && !loading">
      <h2>{{ selectedEmploymentDurationName }} - Yıllara Göre Dağılım</h2>
      <apexchart type="line" height="350" :options="yearlyTrendChartOptions" :series="yearlyTrendSeries"></apexchart>
    </div>

    <!-- Cinsiyet Dağılımı -->
    <div class="chart-container" v-if="!loading">
      <h2>Cinsiyet Dağılımı</h2>
      <div class="gender-charts">
        <div class="gender-chart">
          <h3>Erkek ({{ summaryData.male_percentage }}%)</h3>
          <apexchart type="radialBar" height="300" :options="genderChartOptions('Erkek')"
            :series="[summaryData.male_percentage]">
          </apexchart>
        </div>
        <div class="gender-chart">
          <h3>Kadın ({{ summaryData.female_percentage }}%)</h3>
          <apexchart type="radialBar" height="300" :options="genderChartOptions('Kadın')"
            :series="[summaryData.female_percentage]">
          </apexchart>
        </div>
      </div>
    </div>

    <div class="analysis-container" v-if="analysis && !loading">
      <h2>Analiz ve Yorum</h2>
      <p><em>Bu analiz ISO 45001 standartlarına uygun olarak yapay zeka tarafından oluşturulmuştur.</em></p>
      <div class="analysis-comment">
        <div v-html="formatAnalysis(analysis)"></div>
      </div>
    </div>

    <!-- Veri Tablosu -->
    <div class="data-table" v-if="tableData.length > 0 && !loading">
      <h2>Detaylı Veri Tablosu</h2>
      <table>
        <thead>
          <tr>
            <th>Çalışma Süresi</th>
            <th>Yıl</th>
            <th>Çalışma Günü Kazaları</th>
            <th>Uygunsuzluk Günleri</th>
            <th>2 Gün Uygunsuz</th>
            <th>3 Gün Uygunsuz</th>
            <th>4 Gün Uygunsuz</th>
            <th>5+ Gün Uygunsuz</th>
            <th>Ölümlü Kazalar</th>
            <th>Erkek</th>
            <th>Kadın</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in tableData" :key="`${item.code}-${item.year}`">
            <td>{{ item.employment_duration }}</td>
            <td>{{ item.year }}</td>
            <td>{{ item.works_on_accident_day || 0 }}</td>
            <td>{{ item.unfit_on_accident_day || 0 }}</td>
            <td>{{ item.two_days_unfit || 0 }}</td>
            <td>{{ item.three_days_unfit || 0 }}</td>
            <td>{{ item.four_days_unfit || 0 }}</td>
            <td>{{ item.five_or_more_days_unfit || 0 }}</td>
            <td>{{ item.fatalities || 0 }}</td>
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
    const summaryData = ref({
      total_cases: 0,
      total_unfit: 0,
      total_fatalities: 0,
      male_count: 0,
      female_count: 0,
      male_percentage: 0,
      female_percentage: 0
    })
    const analysis = ref('')
    const availableYears = ref(['2019', '2020', '2021', '2022', '2023'])
    const availableEmploymentDurations = ref([])

    // Seçimler
    const selectedYear = ref('all')
    const selectedEmploymentDuration = ref('all')
    const selectedGender = ref('all')
    const selectedMetric = ref('total_cases')

    // Grafik verileri
    const mainSeries = ref([{ name: 'Vaka Sayısı', data: [] }])
    const yearlyTrendSeries = ref([{ name: 'Vaka Sayısı', data: [] }])

    const selectedEmploymentDurationName = computed(() => {
      if (selectedEmploymentDuration.value === 'all') return 'Tüm Süreler'
      const duration = availableEmploymentDurations.value.find(d => d.code === selectedEmploymentDuration.value)
      return duration ? duration.employment_duration : ''
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
        title: { text: 'Çalışma Süreleri' },
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

    const getEmploymentDurationName = (code) => {
      const duration = availableEmploymentDurations.value.find(d => d.code === code)
      return duration ? duration.employment_duration : code
    }

    // Veri çekme fonksiyonları
    const fetchData = async () => {
      loading.value = true
      try {
        const params = {
          year: selectedYear.value !== 'all' ? selectedYear.value : undefined,
          employment_duration: selectedEmploymentDuration.value !== 'all' ? selectedEmploymentDuration.value : undefined,
          gender: selectedGender.value !== 'all' ? selectedGender.value : undefined
        }

        const response = await axios.get('/api/accidents-and-fatalities-by-employer-durations-user', { params })
        tableData.value = response.data.data
        summaryData.value = response.data.summary
        analysis.value = response.data.analysis
        updateCharts()
      } catch (error) {
        console.error('Veri alınırken hata:', error)
      } finally {
        loading.value = false
      }
    }

    // Grafik güncelleme fonksiyonları
    const updateCharts = () => {
      if (selectedEmploymentDuration.value === 'all') {
        updateMainChart()
      } else if (selectedYear.value === 'all') {
        updateYearlyTrendChart()
      }
    }

    const updateMainChart = () => {
      const durationData = {}

      // Çalışma sürelerine göre verileri grupla
      tableData.value.forEach(item => {
        if (!durationData[item.code]) {
          durationData[item.code] = {
            name: item.employment_duration,
            total_cases: 0,
            total_unfit: 0,
            fatalities: 0,
            male_count: 0,
            female_count: 0
          }
        }

        durationData[item.code].total_cases += item.works_on_accident_day || 0
        durationData[item.code].total_unfit += (item.unfit_on_accident_day || 0) +
          (item.two_days_unfit || 0) +
          (item.three_days_unfit || 0) +
          (item.four_days_unfit || 0) +
          (item.five_or_more_days_unfit || 0)
        durationData[item.code].fatalities += item.fatalities || 0
        durationData[item.code].male_count += item.male_count || 0
        durationData[item.code].female_count += item.female_count || 0
      })

      // Grafik verilerini hazırla
      const categories = []
      const seriesData = []

      availableEmploymentDurations.value.forEach(duration => {
        if (durationData[duration.code]) {
          categories.push(duration.employment_duration)
          let value = 0

          switch (selectedMetric.value) {
            case 'total_cases':
              value = durationData[duration.code].total_cases;
              break
            case 'total_unfit':
              value = durationData[duration.code].total_unfit;
              break
            case 'fatalities':
              value = durationData[duration.code].fatalities;
              break
          }

          seriesData.push({
            x: duration.employment_duration,
            y: value
          })
        }
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
        let value = 0
        switch (selectedMetric.value) {
          case 'total_cases':
            value = item.works_on_accident_day || 0;
            break
          case 'total_unfit':
            value = (item.unfit_on_accident_day || 0) +
              (item.two_days_unfit || 0) +
              (item.three_days_unfit || 0) +
              (item.four_days_unfit || 0) +
              (item.five_or_more_days_unfit || 0);
            break
          case 'fatalities':
            value = item.fatalities || 0;
            break
        }
        yearlyData[item.year] += value
      })

      yearlyTrendSeries.value = [{
        name: getMetricTitle(),
        data: availableYears.value.map(year => yearlyData[year])
      }]
    }

    // Yardımcı fonksiyonlar
    const getMetricTitle = () => {
      switch (selectedMetric.value) {
        case 'total_cases': return 'Çalışma Günü Kazaları'
        case 'total_unfit': return 'Toplam Uygunsuzluk Günleri'
        case 'fatalities': return 'Ölümlü Kazalar'
        default: return 'Vaka Sayısı'
      }
    }

    // Sayfa yüklendiğinde verileri çek
    onMounted(async () => {
      try {
        const durationRes = await axios.get('/api/employee-employment-durations-user')
        availableEmploymentDurations.value = durationRes.data
      } catch (err) {
        console.error('Çalışma süreleri alınırken hata:', err)
      }
      await fetchData()
    })

    return {
      loading,
      tableData,
      summaryData,
      availableYears,
      availableEmploymentDurations,
      selectedYear,
      selectedEmploymentDuration,
      selectedEmploymentDurationName,
      selectedGender,
      selectedMetric,
      mainChartOptions,
      mainSeries,
      yearlyTrendChartOptions,
      yearlyTrendSeries,
      genderChartOptions,
      analysis,
      fetchData,
      updateCharts,
      formatAnalysis,
      getEmploymentDurationName
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
