import axios from 'axios';
import router from '@/router';
import JwtService from "@/services/JwtService";
import Swal from 'sweetalert2';


const instance = axios.create({
  baseURL: import.meta.env.VITE_APP_API_URL,
  timeout: 20000,
  //timeout: 60000,
})
// apply interceptor on response error
instance.defaults.headers.common.Accept = 'application/json';

// verificando token
const token = JwtService.getToken();
if (token) {
  instance.defaults.withCredentials = true; // estava false antes
  instance.defaults.headers.common.Authorization = `Bearer ${token}`;
}

function errorHandle(error:AxiosError) {

  if (!error.response) {
    Swal.fire('Oops...', "Houve um erro ao se comunicar com o servidor", 'error')
    return Promise.reject(error);
  }
  if (error.response) {
    if (error.response.status === 400 || error.response.status === 422) {
      Swal.fire('Atenção', `${error.response.data.message}`, 'warning')
      return Promise.reject(error);
    }
    if (error.response.status === 401) {
      localStorage.clear();
      Swal.fire('Desculpe!', "Sua sessão expirou, faça o login novamente!", 'info')
      router.replace({path: '/'});
    }
    if (error.response.status === 419) {
      Swal.fire('', "Recarregue a página novamente!", 'info')
      return Promise.reject(error);
    }
    if (error.response.status === 403) {
      Swal.fire('Sem permissão', 'Acesso negado', 'warning')
      return Promise.reject(error);
    }
    if (error.response.status === 500) {
      Swal.fire('Erro interno de servidor', `${error.response.data.message}`, 'error')
      return Promise.reject(error);
    }
  }
  // outros erros
  //Swal.fire(`Ocorreu um erro`, `${error.response.data.mensagem}`, 'error');
  return Promise.reject(error);
}

instance.interceptors.response.use((response) => response, errorHandle);

export default instance
