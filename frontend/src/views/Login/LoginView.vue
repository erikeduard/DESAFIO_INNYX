<script setup lang="ts">
import { computed, ref } from 'vue'
import {useAuthStore} from '@/stores/auth.store'
import type {IAuthStore} from '@/stores/auth.store'
import { useRouter } from 'vue-router';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Password from 'primevue/password';
import Card from 'primevue/card';
import Swal from 'sweetalert2';

const router = useRouter();
const auth = useAuthStore();
const form = ref({
  email: '',
  password: '',
});

const podeLogar = computed((): boolean => {
  return  form.value.email.length >=3 &&
          validaEmail() &&
          form.value.password.length >=3;
});

const validaEmail  = (): boolean => {
    let email = form.value.email;
    if (email === "" || email === null || email === undefined) {
        return true;
        
    }
    let re = /\S+@\S+\.\S+/;
    return re.test(email);
}

const login = async () => {
  console.log('login', form.value);
  auth.login(form.value.email, form.value.password)
    .then(response => {
      Swal.fire({
        title: 'Login efetuado com sucesso',
        html: '',
        timer: 2000,
        timerProgressBar: true,
        icon: 'success',
        willClose: () => {
          router.push(auth.rotaHome)

        }
      })
    })
    .catch(error => {
      Swal.fire('Login ou senha inválidos', `${error}`, 'error')
    });
};


</script>

<template>
    <div class="flex flex-column md:flex-row justify-content-center mt-5">
        <Card :pt="{root:{class:'p-4 shadow-2 border-round w-full lg:w-6'}}">
          
            <template #title> Desafio </template>
            
            <template #content>
            
            <form @submit.prevent  class="flex flex-column gap-2 text-black-alpha-90">
                    
                    <label >Usuário</label>
                    <InputText v-model="form.email"  />
                    <small v-if="!validaEmail()" class="p-error" id="email-help">E-mail inválido</small>
                        
                    <label>Senha</label>
                    
                    <Password inputClass="w-full" v-model="form.password" toggleMask :feedback="false"/>              
                    
                    
                    <Button :disabled="!podeLogar" type="submit" label="Login" @click="login()"/>
                </form>
            </template>
            
        </Card>
</div>
</template>



<style scoped>

</style>