<script setup lang="ts">
import type { IAuthStore } from '@/stores/auth.store';
import { useAuthStore } from '@/stores/auth.store';
import { useRouter } from 'vue-router';
import Menubar from 'primevue/menubar';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';
import Menu from 'primevue/menu';
import {computed, ref} from 'vue';

const menuUserComponent = ref();
const toggle = (event:any) => {
  menuUserComponent.value.toggle(event);
};

const router = useRouter();

const auth = useAuthStore() as IAuthStore;

const items = ref ([
    {
        label: 'Inicio',
        icon: 'pi pi-home',
        command: () => {
        if (auth.logado) {
          router.push('/inicio');
        }
      }
    },   

    {
      label: 'Cadastros',
      icon: 'pi pi-fw pi-pencil',
      items: [
        {
          label: 'Usuários',
          icon: 'pi pi-fw pi-user',
          command: () => {
            router.push('/usuarios');
          }
        },
        {
          label: 'Categoria',
          icon: 'pi pi-fw pi-folder',
          command: () => {
            router.push('/categorias');
          }
        }
      ]
    }
   
])

const menuUser = computed<any[]>(()=>{
  return [
    {
      label: 'Sair',
      icon: 'pi pi-fw pi-sign-out',
      command: () => {
        logout();
      }
    }
  ]
})

const logout = (): void => {
  auth.purgeAuth();
  router.push('/');
}


</script>

<template>
<Menubar :model="items" v-if="auth.logado">
    <template #item="{ item, props, hasSubmenu, root }">
        <a v-ripple class="flex align-items-center" v-bind="props.action">
            <span :class="item.icon" />
            <span class="ml-2">{{ item.label }}</span>
            <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-2': root }" :value="item.badge" />
            <span v-if="item.shortcut" class="ml-auto border-1 surface-border border-round surface-100 text-xs p-1">{{ item.shortcut }}</span>
            <i v-if="hasSubmenu" :class="['pi pi-angle-down', { 'pi-angle-down ml-2': root, 'pi-angle-right ml-auto': !root }]"></i>
        </a>
    </template>
    <template #end>
        <button class="relative overflow-hidden w-full p-link flex align-items-center p-2 pl-3 text-color hover:surface-200 border-noround" aria-haspopup="true" aria-controls="overlay_menu" @click="toggle">
        <Avatar icon="pi pi-user" class="mr-2" shape="circle" />
        <span class="inline-flex flex-column">
                <span class="font-bold">{{auth.user.name}}</span>
            </span>
      </button>
        <Menu ref="menuUserComponent" id="overlay_menu" :model="menuUser" :popup="true" />
    </template>
</Menubar>
</template>

<style scoped></style>
