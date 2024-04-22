<script setup lang="ts">

import { computed, onMounted, ref} from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { format } from 'date-fns';
import { type dadosPaginacaoInterface, type eventPaginator, extrairDadosResponse } from "@/scripts/utils/HelperPaginacao";


import InputText from 'primevue/inputtext';
import ProgressSpinner from 'primevue/progressspinner';
import DataView from 'primevue/dataview';
import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions'
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import Button from 'primevue/button';
import Swal from 'sweetalert2';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import Calendar from 'primevue/calendar';
import Message from 'primevue/message';
import Paginator from 'primevue/paginator';
import Dropdown from 'primevue/dropdown';
import Image from 'primevue/image';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { useToast } from 'primevue/usetoast';



const toast = useToast();
const router = useRouter();
const carregando = ref(false);
const produtos = ref([]);
const campoBusca = ref('');
const layout = ref("grid");
const modalAberta = ref(false);
const alterando = ref(false);
const preloadForm = ref(false);
const salvo = ref(false);
const dataValidada = ref(true);
const categoria = ref([]);
const apiUrl = import.meta.env.VITE_APP_API_URL;

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

const form = ref<{ id: null | number, nome: string, descricao: string,data_validade:null| string, preco: null | number, categoria_id: null | number,imagem:string  }>({
  id:null,
  nome: '',
  descricao: '',
  data_validade:null,
  preco: null,
  categoria_id: null,
  imagem: ''
});

const fecharModal = () => {
  modalAberta.value = false;
  form.value = {
    id: null,
    nome: '',
    descricao: '',
    data_validade:null,
    preco: null,
    categoria_id: null,
    imagem: ''
  };
  alterando.value = false;
  preloadForm.value = false;
  salvo.value = false;
  dataValidada.value = true;
};

const podeCadastrarAlterar = computed(() => {
  return form.value.nome.length>=3 && form.value.descricao.length>=3 && form.value.preco!=null && form.value.categoria_id!=null && dataValidada.value;
});

const dataFormatada = computed(() => {
 
  let saida = form.value.data_validade;
    if (saida) {
              // Expressão regular para verificar o formato 'dd/mm/yyyy'
        const formatoPTBR = /^\d{2}\/\d{2}\/\d{4}$/;

        // Verifica se a string da data corresponde ao formato 'dd/mm/yyyy'
        if (formatoPTBR.test(saida)) {
          return saida;
        } else {
          saida = new Intl.DateTimeFormat('pt-BR').format(new Date(saida));
        }
    }
    return saida;
});
  

const formatarData = () =>{
 
  form.value.data_validade = dataFormatada.value;
  api.post('verificarDataValidade', {
    data_validade: form.value.data_validade
  })
  .then(response => {
    dataValidada.value = response.data;
  })
};

const fileUploader = (event,id) => {
  
  const formData = new FormData();
  formData.append('id', id);
  formData.append('file', event.files[0]);
  api.post('upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
  .then(response => {
    form.value.imagem = response.data.file;
  })
  .catch(error => {
    console.error(error);
  });
};

const onAdvancedUpload = (event) => {
    toast.add({ severity: 'info', summary: 'Success', detail: 'File Uploaded', life: 3000 });
};


const apagar = (item) => {
  console.log(item);
  Swal.fire({
    title: 'Deseja realmente apagar?',
    text: "Você não poderá reverter isso!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sim, apagar!',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      api.delete(`/produtos/${item}`)
        .then(response => {
          Swal.fire(
            'Apagado!',
            'Seu registro foi apagado.',
            'success'
          )
          buscar();
        })
        .catch(error => {
          console.error(error);
        });
    }
  })
}

const imagemUrl= ref('');

const formAlterar = (item) => {
  
  alterando.value = true;
  api.get(`/produtos/${item.id}`)
    .then(response => {
      form.value = response.data;
      form.value.data_validade = format(new Date(response.data.data_validade + ' 12:00:00'), 'dd/MM/yyyy');
      if(form.value.imagem){
        api.get(`download/${form.value.imagem}`)
        .then(response => {
          const Url = URL.createObjectURL(new Blob([response.data]));
          imagemUrl.value = Url;
          
        })
      }
      modalAberta.value = true;
    })
    .catch(error => {
      console.error(error);
    });
}

const alterar = () => {
  preloadForm.value = true;
  api.put(`/produtos/${form.value.id}`, form.value)
    .then(response => {
      salvo.value = true;
      buscar();
    }).finally(() => {
      preloadForm.value = false;
    })
    .catch(error => {
      console.error(error);
    });
}

const cadastrar = () => {
  preloadForm.value = true;
  api.post('/produtos', form.value)
    .then(response => {
      
      salvo.value = true;
      produtos.value = response.data;
    }).finally(() => {
      preloadForm.value = false;
      buscar();
    })
    .catch(error => {
      console.error(error);
    });
}

const buscar = () => {
  carregando.value = true;
  dadosPaginacao.value.campoBusca = campoBusca.value;
  api.get('/produtos', {
    params: dadosPaginacao.value    
  })
    .then(response => {
      produtos.value = response.data.data.data;
      produtos.value.forEach((item) => {
        item.data_validade = format(new Date(item.data_validade + ' 12:00:00'), 'dd/MM/yyyy');
      });
      dadosPaginacao.value = { ...dadosPaginacao.value, ...extrairDadosResponse(response.data.data) }
    }).finally(() => {
      carregando.value = false;
    })
    .catch(error => {
      console.error(error);
    });
}

onMounted(() => {
  carregando.value = true;
  api.get('/produtos',{
    params: dadosPaginacao.value
  })
    .then(response => {
      dadosPaginacao.value = { ...dadosPaginacao.value, ...extrairDadosResponse(response.data.data) }
      produtos.value = response.data.data.data;
      produtos.value.forEach((item) => {
        item.data_validade = format(new Date(item.data_validade + ' 12:00:00'), 'dd/MM/yyyy');
      });
      categoria.value = response.data.categorias;
      carregando.value = false;
    })
    .catch(error => {
      console.error(error);
    });
});
</script>

<template>
  <Dialog v-model:visible="modalAberta" modal :header='alterando ? "Alterando Produto" : "Cadastrando Produto"'
        :closable="true" :closeOnEscape="false" :style="{ width: '60rem' }"
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
                            aria-describedby="nome-help" placeholder="Nome do produto" :maxlength="50"  />
                    </span>

                    <label for="descricao">Descrição</label>
                    <span>
                        <InputText class="w-full" size="large" id="descricao" v-model="form.descricao"
                            aria-describedby="descricao-help" placeholder="Descrição do produto" :maxlength="200"/>
                    </span>

                    <label for="preco">Preço</label>
                    <span>
                      <InputNumber v-model="form.preco" inputId="preco" :minFractionDigits="2" :maxFractionDigits="5" :min="0" :max="99999"/>
                    </span>

                    <label for="data_validade">Data de Validade</label>
                    <span>
                      <Calendar v-model="form.data_validade" inputId="data_validade" @hide="formatarData" dateFormat="dd/mm/yy"/>
                      
                    </span>
                    <small v-if="!dataValidada" class="p-error" id="data_validade-help">A data precisa ser maior que hoje!</small>
                    
                    
                    <label for="categoria">Categoria</label>
                    <span>
                        <Dropdown class="w-full" inputId="categoria" v-model="form.categoria_id" :options="categoria" :option-value="categoria=>categoria.id" :optionLabel="categoria=>categoria.nome" placeholder="Selecione uma categoria" />                           
                    </span>

                    <span v-if="alterando">
                      <label >Imagem</label>
                      <Image :src="`${apiUrl}download/${form.imagem}`" alt="Image" width="250"/>
                      <div class="card">
                          <Toast />
                          <FileUpload name="file" uploadedFiles customUpload @uploader="fileUploader($event,form.id)" @upload="onAdvancedUpload($event)" :multiple="false" accept="image/*" :maxFileSize="1000000" :fileLimit="1">
                              <template #empty>
                                  <p>Arraste e solte o arquivo para upload.</p>
                              </template>
                          </FileUpload>
                      </div>
                    </span>
                </form>
            </div>
        </div>

        <div class="flex flex-column">
            <Message v-if="salvo && !form.id" severity="success">Produto Cadatrado com sucesso!</Message>
            <Message v-if="salvo && form.id" severity="success">Produto  Alterado com sucesso!</Message>
        </div>

        <template #footer>
            
            <Button v-if="!preloadForm" @click="modalAberta = false" class="w-full" severity="secondary"
                label="Fechar" />
            <Button :disabled="!podeCadastrarAlterar" class="w-full"
                v-if="!salvo && !form.id && !preloadForm && !alterando" icon="pi pi-database" label="Cadastrar"
                @click="cadastrar()" />
            <Button :disabled="!podeCadastrarAlterar" class="w-full" v-if="!salvo && form.id && !preloadForm"
                icon="pi pi-pencil" label="Alterar" @click="alterar()" />
            
        </template>
        
    </Dialog>


    <div class="container-fluid">
      <div class="card">
        <h2>PRODUTOS</h2>
      </div>
      <div class="card flex flex-wrap justify-content-right gap-3">
        <div class="card flex justify-content-center">
            <form @submit.prevent="buscar" class="flex flex-column gap-2">
                <label for="produtoname">Buscar</label>
                <span>
                  <IconField iconPosition="right">
                      <InputIcon class="pi pi-search" @click="buscar()"/> 
                      <InputText v-model="campoBusca" autocomplete="false" aria-describedby="produtoname-help" placeholder="Nome do produto ou descrição" />
                  </IconField>
                </span>
            </form>
        </div>
      </div>
      <div class="flex flex-wrap justify-content-right gap-3 mt-2">
            <Button icon="pi pi-database"  label="Cadastrar" severity="info" @click="modalAberta = true" />
        </div>

      
      <div class="flex align-items-center justify-content-center gap-2" v-if="carregando">
          <ProgressSpinner />
      </div>
      <div class="card mt-4"  v-if="produtos.length > 0 && !carregando">
        <DataView :value="produtos" :layout="layout">
            <template #header>
                <div class="flex justify-content-end">
                    <DataViewLayoutOptions v-model="layout" />
                </div>
            </template>

            <template #list="slotProps">
                <div class="grid grid-nogutter">
                    <div v-for="(item, index) in slotProps.items" :key="index" class="col-12">
                        <div class="flex flex-column sm:flex-row sm:align-items-center p-4 gap-3" :class="{ 'border-top-1 surface-border': index !== 0 }">
                            <div class="md:w-10rem relative" v-if="item.imagem!=''">
                                <img class="block xl:block mx-auto border-round w-full" :src="`${apiUrl}download/${item.imagem}`" :alt="item.imagem" />
                                
                            </div>
                            <div class="flex flex-column md:flex-row justify-content-between md:align-items-center flex-1 gap-4">
                                <div class="flex flex-row md:flex-column justify-content-between align-items-start gap-2">
                                    <div>
                                        <span class="font-medium text-secondary text-sm">{{ item.categoria.nome }}</span>
                                        <div class="text-lg font-medium text-900 mt-2">{{ item.nome }}</div>
                                        <div class="text-xs font-medium text-500 mt-3">Descrição: {{ item.descricao }}</div>
                                        <div class="text-xs font-medium text-500 mt-5">Validade: {{ item.data_validade }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-column md:align-items-end gap-5">
                                    <span class="text-xl font-semibold text-900">R$ {{ item.preco }}</span>
                                    <div class="flex flex-row-reverse md:flex-row gap-2">
                                        <Button icon="pi pi-trash" outlined @click="apagar(item)"></Button>
                                        <Button icon="pi pi-pencil" label="Alterar" class="flex-auto md:flex-initial white-space-nowrap" @click="formAlterar(item)"></Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #grid="slotProps">
                <div class="grid grid-nogutter">
                    <div v-for="(item, index) in slotProps.items" :key="index" class="col-12 sm:col-6 md:col-4 xl:col-6 p-2">
                        <div class="p-4 border-1 surface-border surface-card border-round flex flex-column">
                            <div class="surface-50 flex justify-content-center border-round p-3">
                                <div class="relative mx-auto" v-if="item.imagem!=''">
                                    <img class="border-round w-full" :src="`${apiUrl}download/${item.imagem}`" :alt="item.imagem" style="max-width: 300px"/>
                                    
                                </div>
                            </div>
                            <div class="pt-4">
                                <div class="flex flex-row justify-content-between align-items-start gap-2">
                                    <div>
                                        <span class="font-medium text-secondary text-sm">{{ item.categoria.nome }}</span>
                                        <div class="text-lg font-medium text-900 mt-1">{{ item.nome }}</div>
                                        <div class="text-xs font-medium text-500 mt-2">Descrição: {{ item.descricao }}</div>
                                        <div class="text-xs font-medium text-500 mt-5">Validade: {{ item.data_validade }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-column gap-4 mt-4">
                                    <span class="text-2xl font-semibold text-900">R$ {{ item.preco }}</span>
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-pencil" label="Alterar"  class="flex-auto white-space-nowrap"  @click="formAlterar(item)"></Button>
                                        <Button icon="pi pi-trash" outlined @click="apagar(item.id)"></Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </DataView>
        <Paginator class="flex align-items-center justify-content-center" @page="trocaPagina"
            :rows="dadosPaginacao.per_page" :totalRecords="dadosPaginacao.total" :first="dadosPaginacao.from - 1"
            template="CurrentPageReport PrevPageLink NextPageLink"
            currentPageReportTemplate="Total encontrado: {totalRecords} | Página {currentPage} de {totalPages}">
            <template #end class="flex">
                <Button severity="secondary" text rounded type="button" icon="pi pi-refresh" @click="buscar" />
            </template>
        </Paginator>
    </div>
    <h2 class="text-center text-default" v-if="produtos.length == 0 && !carregando"> Nenhum registro encontrado</h2>
    </div>
</template>

<style>
</style>