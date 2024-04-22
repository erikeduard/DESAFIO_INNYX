import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/Login/LoginView.vue'
import { useAuthStore } from '../stores/auth.store';
import jwtService from '../services/JwtService';
import Swal from 'sweetalert2';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
   
    {
      path: '/',
      name: 'login',
      // route level code-splitting
      // this generates a separate chunk (Login.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: LoginView
    },

    {
      path: '/inicio',
      name: 'inicio',
      component: () => import('../views/Inicio/InicioView.vue'),
      meta: {titulo: 'Inicio', autenticado: true}
    },

    {
      path: '/usuarios',
      name: 'usuarios',
      component: () => import('../views/Cadastros/Usuarios/UsuariosView.vue'),
      meta: {titulo: 'Usuários', autenticado: true}
    },

    {
      path: '/categorias',
      name: 'categorias',
      component: () => import('../views/Cadastros/Categoria/CategoriasView.vue'),
      meta: {titulo: 'Categorias', autenticado: true}
    }

   
  ]
})

router.beforeEach(async (to, from, next) => {

  const authStore = useAuthStore();


  // se esta indo para login já com token.. vai para dashboard novamente
  if (to.fullPath == "/" && jwtService.getToken()) {
      next({name: 'inicio'} as any);
  }


  // current page view title
  document.title = `${import.meta.env.VITE_APP_NAME}: ${to.meta.titulo}`;


  // before page access check if page requires authentication
  if (to.meta.autenticado) {

      // se esta sem token..
      let temToken = jwtService.getToken();

      if (!temToken) {
          next({name: 'login'} as any);
          Swal.fire('Sem permissão', `Sem permissão para acessar esta página`, 'warning');
          return
      }
      // Se nao esta logado, vai no backend
      if (!authStore.logado) {
          // verificando no backend as permissoes
          await authStore.me().catch(() => {
              Swal.fire('Desculpe!', "Sua sessão expirou, faça o login novamente!", 'info')
              //next({name: "login"});
          })
      }
  }
  // Scroll page to top on every route change
  window.scrollTo({top: 0, left: 0, behavior: "smooth",});
  next();
});


export default router
