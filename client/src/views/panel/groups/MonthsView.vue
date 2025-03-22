<template>
    <div class="page-container">
        <PageNavbar :navData="navbarData" />
        <div class="table-container">
            <table>
                <thead>
                    <td>ID</td>
                    <td>Aylar</td>
                    <td style="width: 20%;"></td>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ocak</td>
                        <td>
                            <i @click="updateModal" class="fa-solid fa-pen-to-square"></i>
                            <i @click="deleteMonths" class="fa-solid fa-trash-can"></i>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Şubat</td>
                        <td>
                            <i @click="updateModal" class="fa-solid fa-pen-to-square"></i>
                            <i @click="deleteMonths" class="fa-solid fa-trash-can"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Months v-if="modal_visible" :visible="modal_visible" :state="state" @update="updateState"/>
        </div>
    </div>
</template>

<script>
import Months from '@/components/panel/groups/AppMonths.vue';
import PageNavbar from '@/components/panel/PageNavbar.vue';
import Swal from 'sweetalert2';
export default {
    components: {
        PageNavbar,
        Months
    },
    data() {
        return {
            navbarData: {
                title: 'Aylar',
                backRoute: '/admin/groups',
            },
            state: 'new',
            modal_visible: true
        }
    },
    methods: {
        updateModal(){
            this.state = 'update'
        },
        updateState(){
            this.state = 'new'
        },
        deleteMonths() {
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
                    window.location.reload();
                }
            });
        }
    }
}
</script>

<style scoped>
.page-container {
    background-color: var(--panel-bg);
    min-height: 100vh;
}

.table-container {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 0 5%;
}

table {
    margin-top: 2%;
    width: 50%;
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
}

tbody tr td:last-child i {
    font-size: 1.2rem;
    margin: 0 10px;
    cursor: pointer;
    color: var(--main-color);
}

tbody tr td:last-child i:hover {
    color: var(--penn-red);
}
</style>