<template>
    <div class="page-container">
        <PageNavbar :navData="navbarData" />
        <div class="navbar-buttons">
            <span @click="newOccupationCode"><i class="fa-solid fa-plus"></i> Yeni Meslek Grubu</span>
            <span @click="downloadExcel"><i class="fa-solid fa-download"></i> Dışarı Aktar</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <td>Kod</td>
                    <td>Meslek Kodu</td>
                    <td>Meslek Adı</td>
                    <td>Grup Kodu</td>
                    <td>Grup Adı</td>
                    <td>Alt Grup Kodu</td>
                    <td>Alt Grup Adı</td>
                    <td>Asil Kod</td>
                    <td>Asil Ad</td>
                    <td style="width: 10%;"></td>
                </thead>
                <tbody>
                    <tr v-for="item in occupation_groups" :key="item.id">
                        <td>{{ item.code }}</td>
                        <td>{{ item.occupation_code }}</td>
                        <td>{{ item.occupation_name }}</td>
                        <td>{{ item.group_code }}</td>
                        <td>{{ item.group_name }}</td>
                        <td>{{ item.sub_group_code }}</td>
                        <td>{{ item.sub_group_name }}</td>
                        <td>{{ item.pure_code }}</td>
                        <td>{{ item.pure_name }}</td>
                        <td>
                            <i @click="updateOccupationCode(item)" class="fa-solid fa-pen-to-square"></i>
                            <i @click="deleteOccupationCode(item)" class="fa-solid fa-trash-can"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <OccupationCode v-if="modal_visible" :visible="modal_visible" :data="selected_group" :state="state"
        @close="closeModal" />
</template>

<script>
import OccupationCode from '@/components/panel/groups/OccupationCode.vue';
import PageNavbar from '@/components/panel/PageNavbar.vue';
import { useAuthStore } from '@/stores/AuthStore';
import axios from 'axios';
import Swal from 'sweetalert2';
import ExcelJS from 'exceljs';
export default {
    components: {
        PageNavbar,
        OccupationCode
    },
    setup() {
        const authStore = useAuthStore()
        return { authStore }
    },
    data() {
        return {
            navbarData: {
                title: 'Meslek Grupları',
                backRoute: '/admin/groups',
            },
            state: null,
            modal_visible: false,
            selected_group: null,
            occupation_groups: []
        }
    },
    methods: {
        newOccupationCode() {
            this.state = 'new'
            this.modal_visible = true
        },
        updateOccupationCode(item) {
            this.state = 'update'
            this.modal_visible = true
            this.selected_group = item
        },
        closeModal() {
            this.state = null
            this.modal_visible = false
            this.selected_group = null
            this.initializeAuth()
        },
        deleteOccupationCode(item) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#003049',
                cancelButtonColor: '#92140cff',
                confirmButtonText: 'Evet, sil!',
                cancelButtonText: 'Hayır, iptal et',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('https://iskazalarianaliz.com/api/occupation-groups/delete/' + item.id)
                        .then(res => {
                            if (res.data.success) {
                                this.initializeAuth()
                                Swal.fire({
                                    title: 'Silindi!',
                                    text: 'Meslek grubu başarıyla silindi.',
                                    icon: 'success',
                                });
                            }
                        })
                }
            });
        },
        downloadExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Meslek Grupları');

            worksheet.columns = [
                { header: 'Kod', key: 'code', width: 10 },
                { header: 'Meslek Kodu', key: 'occupation_code', width: 15 },
                { header: 'Meslek Adı', key: 'occupation_name', width: 30 },
                { header: 'Grup Kodu', key: 'group_code', width: 15 },
                { header: 'Grup Adı', key: 'group_name', width: 30 },
                { header: 'Alt Grup Kodu', key: 'sub_group_code', width: 15 },
                { header: 'Alt Grup Adı', key: 'sub_group_name', width: 30 },
                { header: 'Asil Kod', key: 'pure_code', width: 15 },
                { header: 'Asil Ad', key: 'pure_name', width: 30 },
            ];

            this.occupation_groups.forEach((item) => {
                worksheet.addRow({
                    code: item.code,
                    occupation_code: item.id,
                    occupation_name: item.occupation_name,
                    group_code: item.group_code,
                    group_name: item.group_name,
                    sub_group_code: item.sub_group_code,
                    sub_group_name: item.sub_group_name,
                    pure_code: item.pure_code,
                    pure_name: item.pure_name,
                });
            });

            // Başlık stil ayarları
            const headerRow = worksheet.getRow(1);
            headerRow.font = {
                bold: true,
                size: 14,
                color: { argb: 'FFFFFFFF' },
            };

            headerRow.eachCell((cell) => {
                cell.fill = {
                    type: 'pattern',
                    pattern: 'solid',
                    fgColor: { argb: 'FF003049' },
                };
                cell.alignment = {
                    horizontal: 'center',
                    vertical: 'middle',
                };
            });

            // Dosya olarak indir
            workbook.xlsx.writeBuffer().then((buffer) => {
                const blob = new Blob([buffer], { type: 'application/octet-stream' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Meslek_Grup_Kodlari.xlsx';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });
        },
        async initializeAuth() {
            await this.authStore.fetchAuthData();

            axios.get('https://iskazalarianaliz.com/api/occupation-groups')
                .then(res => {
                    this.occupation_groups = res.data.data
                })
        },
    },
    created() {
        const is_logged_in = localStorage.getItem('is_logged_in') === 'true'

        if (!is_logged_in) {
            this.$router.push('/admin/login')
            return
        }

        this.initializeAuth()
    }
}
</script>

<style scoped>
.page-container {
    background-color: var(--panel-bg);
    min-height: 100vh;
}

.navbar-buttons {
    width: 100%;
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin-top: 1%;
}

.navbar-buttons span {
    width: 20%;
    border: 1px solid var(--main-color);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px 0;
    border-radius: 10px;
    cursor: pointer;
    transition: all ease .3s;
}

.navbar-buttons span i {
    margin-right: 10px;
}

.navbar-buttons span:hover {
    background-color: var(--main-color);
    color: var(--second-color);
}

.table-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

table {
    margin-top: 2%;
    width: 90%;
    border-collapse: collapse;
    table-layout: fixed;
    background: var(--panel-bg);
}

thead {
    background: var(--main-color);
    color: var(--second-color);
}

th,
td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    height: 50px;
    vertical-align: middle;
}

tr:hover {
    background-color: #f5e7cd;
}

tbody tr td:last-child {
    text-align: center;
    vertical-align: middle;
}

tbody tr td:last-child i {
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--main-color);
}

tbody tr td:last-child i:hover {
    color: var(--penn-red);
}

.fa-pen-to-square {
    margin-right: 10px;
}
</style>