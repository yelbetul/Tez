<template>
    <div v-if="visible" class="province-codes-modal">
        <h2>İl Kodu {{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</h2>
        <form class="form" @submit.prevent="submitForm">
            <div v-if="error" class="form-error">
                <span><i class="fa-solid fa-xmark"></i> {{ errorMessage }}</span>
            </div>
            <div class="form-element">
                <label for="province_code">İl Kodu</label>
                <input type="text" id="province_code" v-model="formData.province_code" placeholder="İl kodu giriniz...">
            </div>
            <div class="form-element">
                <label for="province_name">İl Adı</label>
                <input type="text" id="province_name" v-model="formData.province_name" placeholder="İl adı giriniz...">
            </div>
            <div class="form-button">
                <button v-if="state !== 'new'" type="submit" @click.prevent="updateState">İl Kodu Ekle</button>
                <button type="submit">{{ state === 'new' ? 'Oluştur' : 'Güncelle' }}</button>
            </div>
        </form>
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
            formState : this.state,
            error: false,
            errorMessage: null
        };
    },
    methods: {
        updateState(){
            this.$emit('update');
        },
         submitForm() {
            this.closeModal();
        }
    }
}
</script>
<style scoped>
.province-codes-modal {
    border-radius: 16px;
    padding: 40px;
    width: 50%;
    max-width: 750px;
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
    width: 100%;
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
    display: flex;
    justify-content: space-around;
    align-items: center;
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
    width: 48%;
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
    .province-codes-modal {
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