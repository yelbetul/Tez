<template>
    <div v-if="visible" class="modal-overlay" @click.self="closeModal">
        <div class="sector-codes-modal">
            <div class="close" @click="closeModal">
                <i class="fa-solid fa-xmark"></i>
            </div>
            <h2>Sektör Kodu {{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</h2>
            <form class="form" @submit.prevent="submitForm">
                <div v-if="error" class="form-error">
                    <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
                </div>
                <div class="form-element">
                    <label for="sector_code">Sektör Kodu</label>
                    <input type="text" id="sector_code" v-model="formData.sector_code"
                        placeholder="Sektör kodu giriniz...">
                </div>
                <div class="form-element">
                    <label for="group_code">Grup Kodu</label>
                    <input type="text" id="group_code" v-model="formData.group_code" placeholder="Grup kodu giriniz...">
                </div>
                <div class="form-element">
                    <label for="group_name">Grup Adı</label>
                    <input type="text" id="group_name" v-model="formData.group_name" placeholder="Grup adı giriniz...">
                </div>
                <div class="form-element">
                    <label for="sub_group_code">Alt Grup Kodu</label>
                    <input type="text" id="sub_group_code" v-model="formData.sub_group_code"
                        placeholder="Alt grup kodu giriniz...">
                </div>
                <div class="form-element">
                    <label for="sub_group_name">Alt Grup Adı</label>
                    <input type="text" id="sub_group_name" v-model="formData.sub_group_name"
                        placeholder="Alt grup adı giriniz...">
                </div>
                <div class="form-element">
                    <label for="code">Asil Kod</label>
                    <input type="text" id="code" v-model="formData.code" placeholder="Kod giriniz...">
                </div>
                <div class="form-element">
                    <label for="name">Asil Ad</label>
                    <input type="text" id="name" v-model="formData.name" placeholder="Ad giriniz...">
                </div>
                <div class="form-button">
                    <button type="submit">{{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
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
                
            },
            error: false,
            errorMessage: null
        };
    },
    methods: {
        closeModal() {
            this.$emit('close');
        },
        submitForm() {
            this.closeModal();
        }
    }
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
    max-width: 750px;
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
    margin: 0 0 25px;
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
    margin-bottom: 20px;
    width: 48%;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
    width: 100%;
    padding: 12px;
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
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 1.2rem;
    width: 100%;
}

.form-error {
    width: 100%;
    padding: 10px 20px;
    background-color: var(--main-color);
    color: white;
    border-radius: 10px;
    margin-bottom: 4%;
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