<template>
    <div v-if="visible" class="modal-overlay" @click.self="closeModal">
        <div class="sector-codes-modal">
            <div class="close" @click="closeModal">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h2>Yaşlara Göre Ölümlü İş Kazası Verisi {{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</h2>
            <form class="form" @submit.prevent="submitForm">
                <div v-if="error" class="form-error">
                    <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
                </div>
                <div class="form-element">
                    <label for="year">Yıl</label>
                    <select id="year" v-model="formData.year">
                        <option value="0">Seçiniz...</option>
                        <option value="2019">2019</option>
                        <option value="2020">2020</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                    </select>
                </div>

                <div class="form-element">
                    <label for="age_code">Yaş</label>
                    <select id="age_code" v-model="formData.age_id">
                        <option value="0">Seçiniz...</option>
                        <option v-for="item in age_codes" :key="item.id" :value="item.id">
                            {{ item.age }}
                        </option>
                    </select>
                </div>

                <div class="form-element">
                    <label for="gender">Cinsiyet</label>
                    <select id="gender" v-model="formData.gender">
                        <option value="0">Seçiniz...</option>
                        <option :value="true">Kadın</option>
                        <option :value="false">Erkek</option>
                    </select>
                </div>

                <!-- Tabloya ait ek alanlar -->
                <div class="form-element">
                    <label for="work_accident_fatalities">İş Kazası Sonucu (Ölenler)</label>
                    <input type="number" id="work_accident_fatalities" v-model="formData.work_accident_fatalities"
                        min="0" />
                </div>

                <div class="form-element">
                    <label for="occupational_disease_fatalities">Meslek Hastalığı Sonucu (Ölenler)</label>
                    <input type="number" id="occupational_disease_fatalities" v-model="formData.occupational_disease_fatalities"
                        min="0" />
                </div>

                <div class="form-button">
                    <button type="submit">{{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    props: {
        visible: {
            type: Boolean,
            required: true
        },
        state: {
            type: String,
            required: true
        },
        data: {
            type: Object,
            required: false
        }
    },
    data() {
        return {
            formData: {
                year: null,
                group_id: null,
                gender: null,
                work_accident_fatalities: null,
                occupational_disease_fatalities: null,
            },
            age_codes: [],
            error: false,
            errorMessage: null
        };
    },
    watch: {
        data: {
            handler(newVal) {
                if (newVal) {
                    this.formData = { ...newVal };
                    this.formData.gender = this.formData.gender === 1;
                }
            },
            deep: true,
            immediate: true
        }
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        submitForm() {
            if (this.state == 'new') {
                axios.post('https://iskazalarianaliz.com/api/fatal-work-accidents-by-age/store', this.formData)
                    .then(res => {
                        if (!res.data.success) {
                            this.error = true;
                            this.errorMessage = res.data.message;
                        } else {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: 'Veri başarıyla eklendi.',
                                icon: 'success',
                            }).then(() => {
                                this.$emit('close');
                            });
                        }
                    });
            } else {
                axios.put('https://iskazalarianaliz.com/api/fatal-work-accidents-by-age/update/' + this.formData.id, this.formData)
                    .then(res => {
                        if (!res.data.success) {
                            this.error = true;
                            this.errorMessage = res.data.message;
                        } else {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: 'Veri başarıyla güncellendi.',
                                icon: 'success',
                            }).then(() => {
                                this.$emit('close');
                            });
                        }
                    });
            }
        }
    },
    created() {
        axios.get('https://iskazalarianaliz.com/api/age-codes')
            .then(res => {
                this.age_codes = res.data.data
            })
    },
}
</script>
<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(10px);
}

.sector-codes-modal {
    background-color: var(--panel-bg);
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    padding: 40px;
    width: 100%;
    max-width: 600px;
    z-index: 4500;
    position: relative;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.close {
    position: absolute;
    top: 15px;
    right: 20px;
    cursor: pointer;
    color: var(--penn-red);
    font-size: 1.8rem;
    transition: color 0.3s;
}

.close:hover {
    color: #c0392b;
}

h2 {
    margin: 0 0 10px;
    color: var(--main-color);
    font-size: 1.4rem;
    text-align: center;
}

.form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.form-element {
    margin-bottom: 10px;
    width: 48%;
}

label {
    display: block;
    margin-bottom: 2px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    width: 100%;
    padding: 8px;
    border: 1px solid #dcdcdc;
    border-radius: 8px;
    transition: border-color 0.3s;
    appearance: none;
    -webkit-appearance: none;
    font-family: "Poppins", sans-serif;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus {
    border-color: var(--main-color);
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding-right: 30px;
}

input[type="text"]:focus,
input[type="number"]:focus,
input[type="date"]:focus,
select:focus {
    outline: none;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
    font-family: "Poppins", sans-serif;
    -webkit-user-select: none;
    user-select: none;
}

select::-webkit-scrollbar {
    display: none;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    padding: 12px 15px;
}

.form-button {
    width: 100%;
    text-align: center;
}

button {
    background-color: var(--main-color);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 1.2rem;
    width: 50%;
    margin-top: 10px;
}

.form-error {
    width: 100%;
    padding: 10px 20px;
    background-color: var(--main-color);
    color: white;
    border-radius: 10px;
    margin-bottom: 15px;
}

@media (max-width: 480px) {
    .sector-codes-modal {
        max-width: 90%;
        height: 92vh;
    }

    h2 {
        font-size: 1.4rem;
    }

    .form-element {
        margin-bottom: 30px;
    }
}
</style>