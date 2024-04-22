<script setup lang="ts">
import type { IAuthStore } from '@/stores/auth.store';
import { useAuthStore } from '@/stores/auth.store';
import DataTable from 'primevue/datatable';
import Paginator from 'primevue/paginator';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import ProgressSpinner from 'primevue/progressspinner';
import Message from 'primevue/message';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

import { onMounted, ref, computed } from 'vue';
import api from '@/services/api';
import Swal from 'sweetalert2';
import cloneDeep from 'lodash/cloneDeep';
import { type dadosPaginacaoInterface, type eventPaginator, extrairDadosResponse } from "@/scripts/utils/HelperPaginacao";


const auth = useAuthStore() as IAuthStore;
const carregando = ref(false); // preload da tabela
const alterando = ref(false);
const preloadForm = ref(false);
const salvo = ref(false);
const lista = ref([]);
const alterarSenha = ref(false);
const modalAberta = ref(false);

const dadosPaginacao = ref<dadosPaginacaoInterface & { campoBusca: string }>({
    page: 1, // para o laravel
    from: 0, // para o paginator
    per_page: +import.meta.env.VITE_APP_POR_PAGINA, // conversão para number. para o laravel
    total: 0,
    campoBusca: '',
})
const trocaPagina = (event: eventPaginator) => {
    dadosPaginacao.value.page = event.page + 1
    buscar();
}

const campoBusca = ref('');


const form = ref<{ id: null | number,nome: string }>({
    
    id: null,
    nome: '',
});
const formDefault = {
    id: null,
    nome: '',
   
};


const formReset = () => {
    form.value = cloneDeep(formDefault)
}

const podeCadastrarAlterar = computed((): boolean => {
    return form.value.nome.length >= 3;

})


const cadastrar = () => {
    modalAberta.value = true;
    preloadForm.value = true;
    salvo.value = false;
    let dados = {
        ...form.value,
    }

    api.post('categorias', dados).then((res) => {
        salvo.value = true;
    }).finally(() => {
        preloadForm.value = false;
        atualizar();
    });

};

const formAlterar = (id) => {
    modalAberta.value = true;
    preloadForm.value = true;
    alterando.value = true;
    form.value.id = 1;
    api.get(`categorias/${id}/edit`).then((res) => {
        form.value.nome = res.data.nome;
        form.value.id = res.data.id;
    }).finally(() => {
        preloadForm.value = false;
    });
};


const alterar = () => {
    preloadForm.value = true;

    let dados = {
        id: form.value.id,
        nome: form.value.nome,
    }
    api.put(`categorias/${form.value.id}`, dados).then((res) => {
        salvo.value = true;
        buscar();
    }).finally(() => {
        preloadForm.value = false;
    });

};


const apagar = (id) => {
    Swal.fire({
        title: 'Deseja realmente excluir?',
        text: "Você não poderá reverter esta ação!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {

            api.delete(`categorias/${id}`).then((res) => {
                Swal.fire(
                    'Excluído!',
                    'Categoria excluída com sucesso.',
                    'success'
                )
                atualizar();
            })
        }
    })
};

const fecharModal = () => {
    salvo.value = false;
    preloadForm.value = false;
    modalAberta.value = false;
    alterando.value = false;
    alterarSenha.value = false;
    formReset();
};

const buscar = () => {
    carregando.value = true;
    dadosPaginacao.value.campoBusca = campoBusca.value;
    api.get('categorias', {
        params: dadosPaginacao.value
    }).then((res) => {
        lista.value = res.data.data;
        dadosPaginacao.value = { ...dadosPaginacao.value, ...extrairDadosResponse(res.data) }
        carregando.value = false;
    })
};

//para caso não use o botao do pesquisar
const atualizar = () => {
    carregando.value = true;
    dadosPaginacao.value.page = 1;
    dadosPaginacao.value.campoBusca = campoBusca.value;
    api.get('categorias', {
        params: dadosPaginacao.value
    }).then((res) => {
        lista.value = res.data.data;
        dadosPaginacao.value.per_page = res.data.per_page;
        carregando.value = false;
    })
};




onMounted(() => {
    carregando.value = true;
    api.get('categorias', {
        params: dadosPaginacao.value
    }).then((res) => {
        dadosPaginacao.value = { ...dadosPaginacao.value, ...extrairDadosResponse(res.data) }
        lista.value = res.data.data;        
        carregando.value = false;
    })
})


</script>

<template>
    <Dialog v-model:visible="modalAberta" modal :header='alterando ? "Alterando Categoria" : "Cadastrando Categoria"'
        :closable="true" :closeOnEscape="false" :style="{ width: '50rem' }"
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" @hide="fecharModal">

        <div class="inline-flex align-items-center justify-content-center gap-2" v-if="preloadForm">
            <ProgressSpinner />
        </div>

        <div v-if="!preloadForm && !salvo" class="flex flex-column gap-2">
            <div class="flex flex-column gap-2">
                <form @submit.prevent action="" class="flex flex-column gap-2 ">

                    <label for="nome">Nome</label>
                    <span>
                        <InputText class="w-full" size="large" id="nome" v-model="form.nome"
                            aria-describedby="nome-help" placeholder="Nome da Categoria" :maxlength="100" />
                    </span>

                    
                </form>
            </div>
        </div>

        <div class="flex flex-column">
            <Message v-if="salvo && !form.id" severity="success">Categoria Cadatrada com sucesso!</Message>
            <Message v-if="salvo && form.id" severity="success">Categoria Alterada com sucesso!</Message>
        </div>

        <template #footer>
                <Button v-if="!preloadForm" @click="modalAberta = false" class="w-full" severity="secondary"
                    label="Fechar" />
                <Button :disabled="!podeCadastrarAlterar " class="w-full"
                    v-if="!salvo && !form.id && !preloadForm && !alterando" icon="pi pi-database" label="Cadastrar"
                    @click="cadastrar()" />
                <Button :disabled=" !alterando" class="w-full" v-if="!salvo && form.id && !preloadForm"
                    icon="pi pi-pencil" label="Alterar" @click="alterar()" />

        </template>
    </Dialog>

    <div class="container-fluid">
        <div class="card">
            <h2 class="titulo">CATEGORIAS</h2>
        </div>
        <div class="card flex flex-wrap justify-content-right gap-3">
            <div class="card flex justify-content-center">
                <form @submit.prevent="buscar" class="flex flex-column gap-2">
                    <label for="username">Buscar</label>
                    <span>
                        <IconField iconPosition="right">
                            <InputIcon class="pi pi-search" @click="buscar()"></InputIcon>
                            <InputText v-model="campoBusca" autocomplete="false" aria-describedby="username-help" placeholder="Nome da Categoria" />
                        </IconField>
                    </span>
                </form>
            </div>
        </div>
        <div class="flex flex-wrap justify-content-right gap-3 mt-2">
            <Button :disabled="carregando" icon="pi pi-refresh" label="Atualizar" @click="atualizar()" />
            <Button icon="pi pi-database"  label="Cadastrar" severity="info" @click="modalAberta = true" />
        </div>


        <div class="flex align-items-center justify-content-center gap-2" v-if="carregando">
            <ProgressSpinner />
        </div>
        <div class="cards mt-4" v-if="lista.length > 0 && !carregando">
            <DataTable :value="lista" stripedRows tableStyle="min-width: 50rem">
                <Column  field="id" header="ID" />
                <Column  field="nome" header="Nome" /> 
                <Column>
                    <template #body="{ data }">
                        <div class="flex flex-wrap  justify-content-center gap-2 mt-2 ">
                            <Button  icon="pi pi-pencil" severity="secondary"
                                label="Alterar" @click="formAlterar(data.id)" />
                            <Button  icon="pi pi-trash" severity="danger"
                                label="Excluir" @click="apagar(data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
            <Paginator class="flex align-items-center justify-content-center" @page="trocaPagina"
                :rows="dadosPaginacao.per_page" :totalRecords="dadosPaginacao.total" :first="dadosPaginacao.from - 1"
                template="CurrentPageReport PrevPageLink NextPageLink"
                currentPageReportTemplate="Total encontrado: {totalRecords} | Página {currentPage} de {totalPages}">
                <template #end class="flex">
                    <Button severity="secondary" text rounded type="button" icon="pi pi-refresh" @click="buscar" />
                </template>
            </Paginator>
        </div>
        <h2 class="text-center text-default" v-if="lista.length == 0 && !carregando"> Nenhum registro encontrado</h2>
    </div>
</template>

<style></style>