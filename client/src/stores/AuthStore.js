import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        id: localStorage.getItem('id'),
        username: localStorage.getItem('username'),
        name_surname: localStorage.getItem('name_surname'),
        api_key: null,
        secret_key: null,
        default_api_key: 'A7X9LQ2M5T8V3R1Y6W0K',
        default_secret_key: 'Z4P2N8J6D1F0B7X3Y5QK'
    }),
    actions: {
        async fetchAuthData() {
            if (this.api_key === null || this.secret_key === null) {
                try {
                    axios.defaults.headers.common['X-API-KEY'] = this.default_api_key;
                    axios.defaults.headers.common['X-SECRET-KEY'] = this.default_secret_key;
                    const response = await axios.get(`https://iskazalarianaliz.com/api/admin/search/${this.id}`)
                    if(response.data.success){
                        this.api_key = response.data.admin.api_key
                        this.secret_key = response.data.admin.secret_key

                        axios.defaults.headers.common['X-ADMIN-ID'] = this.id;
                        axios.defaults.headers.common['X-API-KEY'] = this.api_key;
                        axios.defaults.headers.common['X-SECRET-KEY'] = this.secret_key;
                    }else{
                        this.$router.push('/admin/login')
                    }
                } catch (error) {
                    console.error('Kullanıcı verileri alınırken hata oluştu:', error)
                    this.authError = 'Kullanıcı verileri alınırken bir hata oluştu.'
                }
            }
        },
    },
})
