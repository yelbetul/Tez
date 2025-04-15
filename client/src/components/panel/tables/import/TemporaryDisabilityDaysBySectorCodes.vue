<template>
    <div v-if="visible" class="modal-overlay" @click.self="closeModal">
        <div class="sector-codes-modal">
            <div class="close" @click="closeModal">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h2>Sektörlere Göre Geçici İş Göremezlik Verisi Aktar</h2>

            <form class="form" @submit.prevent="submitForm">
                <div v-if="error" class="form-error">
                    <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
                </div>

                <div class="drop-zone" :class="{ 'is-dragover': isDragging }" @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop">
                    <input type="file" id="file" hidden ref="fileInput" @change="handleFileChange" />
                    <p @click="$refs.fileInput.click()">
                        <i class="fa-solid fa-cloud-arrow-up"></i><br />
                        Dosyanızı buraya bırakın veya tıklayın
                    </p>
                    <span v-if="fileName">Yüklenen: {{ fileName }}</span>
                </div>

                <div class="form-button">
                    <button type="submit">Aktar</button>
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
        }
    },
    data() {
        return {
            file: null,
            fileName: null,
            error: false,
            errorMessage: null,
            isDragging: false,
        };
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        handleFileChange(e) {
            const files = e.target.files;
            if (files.length) {
                this.file = files[0];
                this.fileName = files[0].name;
            }
        },
        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files.length) {
                this.file = files[0];
                this.fileName = files[0].name;
            }
        },
        submitForm() {
            if (!this.file) {
                this.error = true;
                this.errorMessage = "Lütfen bir dosya seçiniz.";
                return;
            }

            Swal.fire({
                title: 'Lütfen Bekleyin...',
                text: 'Veriler yükleniyor...',
                icon: 'info',
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('file', this.file);

            axios.post('https://iskazalarianaliz.com/api/temporary-disability-day-by-sector/import', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    Swal.close();

                    if (!res.data.success) {
                        this.error = true;
                        this.errorMessage = res.data.message;
                    } else {
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'Veriler başarıyla eklendi.',
                            icon: 'success',
                        }).then(() => {
                            this.$emit('close');
                        });
                    }
                })
                .catch(err => {
                    console.log(err)
                    Swal.close();
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Bir şeyler ters gitti, lütfen tekrar deneyin.',
                        icon: 'error',
                    });
                });
        }
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
    max-width: 50%;
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
    font-size: 1.6rem;
    text-align: center;
}

.form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.form-element {
    margin-bottom: 10px;
    width: 30%;
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
    padding: 10px;
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

.drop-zone {
    width: 100%;
    padding: 30px;
    border: 2px dashed var(--main-color);
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    background-color: #f9f9f9;
    color: #555;
    transition: 0.3s ease;
    margin-bottom: 20px;
}

.drop-zone:hover {
    background-color: #f1f1f1;
}

.drop-zone.is-dragover {
    background-color: #eaf5ff;
    border-color: #007bff;
}

.drop-zone i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: var(--main-color);
}

.drop-zone span {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    color: #333;
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