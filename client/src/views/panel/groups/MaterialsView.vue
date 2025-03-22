<template>
    <div class="page-container">
        <PageNavbar :navData="navbarData" />
        <div class="navbar-buttons">
            <span @click="newMaterialCode"><i class="fa-solid fa-plus"></i> Yeni Materyal Kodu</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <td>Materyal Kodu</td>
                    <td>Grup Kodu</td>
                    <td>Grup Adı</td>
                    <td>Alt Grup Kodu</td>
                    <td>Alt Grup Adı</td>
                    <td style="width: 10%;"></td>
                </thead>
                <tbody>
                    <tr>
                        <td>Lorem.</td>
                        <td>Lorem.</td>
                        <td>Lorem.</td>
                        <td>Lorem.</td>
                        <td>Lorem.</td>
                        <td>
                            <i @click="updateMaterialCode" class="fa-solid fa-pen-to-square"></i>
                            <i @click="deleteMaterialCode" class="fa-solid fa-trash-can"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <MaterialCode v-if="modal_visible" :visible="modal_visible" :state="state" @close="closeModal" />
</template>

<script>
import MaterialCode from '@/components/panel/groups/AppMaterials.vue';
import PageNavbar from '@/components/panel/PageNavbar.vue';
import Swal from 'sweetalert2';
    export default {
        components: {
            PageNavbar,
            MaterialCode
        },
        data() {
            return {
                navbarData: {
                    title: 'Materyaller',
                    backRoute: '/admin/groups',
                },
                state: null,
                modal_visible: false
            }
        },
        methods:{
            newMaterialCode(){
                this.state = 'new'
                this.modal_visible = true
            },
            updateMaterialCode(){
                this.state = 'update'
                this.modal_visible = true
            },
            closeModal(){
                this.state = null
                this.modal_visible = false
            },
            deleteMaterialCode(){
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
.page-container{
    background-color: var(--panel-bg);
    min-height: 100vh;
}
.navbar-buttons{
    width: 100%; 
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 1%;
}
.navbar-buttons span{
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
.navbar-buttons span i{
    margin-right: 10px;
}
.navbar-buttons span:hover{
    background-color: var(--main-color);
    color: var(--second-color);
}
.table-container{
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
tr:hover{
    background-color: #f5e7cd;
}
tbody tr td:last-child {
    display: flex;
    justify-content: space-around;
    align-items: center;
}
tbody tr td:last-child i{
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--main-color);
}
tbody tr td:last-child i:hover{
    color: var(--penn-red);
}
</style>