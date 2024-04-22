import {computed,ref} from 'vue'
import api  from '@/services/api';

import JwtService  from '@/services/JwtService';
import {AuthService} from '@/services/AuthService';
import type {AxiosError, AxiosResponse} from 'axios';
import type {LoginData} from '@/services/AuthService';




import { defineStore } from "pinia";

/**
 * Representa um usuario.
 */
type User = {
    email: string;    
    password: string;
    token: string;
};

/**
 * Store responsável por gerenciar a autenticação do usuário.
 */
export const useAuthStore = defineStore("auth",()=>{
    const user = ref<User>({} as User);
    const logado = ref<boolean>(false);

    /**
     * Define o token de autenticação no cabeçalho das requisições Axios.
     */
    function setTokenAxios():void{
        const token = JwtService.getToken();
        if (token) {
            api.defaults.withCredentials = true; // estava false antes
            api.defaults.headers.common.Authorization = `Bearer ${token}`;
        }
    }

    /**
     * Realiza o login do usuário.
     * @param email O nome de usuário ou email.
     * @param password A senha do usuário.
     * @returns Uma Promise contendo a resposta da requisição Axios.
     * @throws Um erro caso ocorra algum problema durante o login.
     */
    const login = (email:string, password: string): Promise<AxiosResponse> => {
        return AuthService.Login(email, password)
            .then((response: AxiosResponse<LoginData>) => {
                user.value = response.data;
                JwtService.saveToken(response.data.token);
                setTokenAxios();
                logado.value = true;
                return response;
            })
            .catch((error: AxiosError) => {
                purgeAuth();
                throw error;
            });
    }

    /**
     * Obtém os dados do usuário autenticado.
     * @returns Uma Promise vazia que é resolvida quando os dados do usuário são obtidos com sucesso.
     * @throws Um erro caso ocorra algum problema durante a obtenção dos dados do usuário.
     */
    const me = async (): Promise<void> => {
        return new Promise((resolve, reject) => {
            api.get('me')
            //axios.get(`${import.meta.env.VITE_APP_API_URL}/me`)
                .then((response: AxiosResponse<LoginData>) => {
                    user.value = response.data;
                    setTokenAxios();
                    logado.value = true;
                    resolve();
                })
                .catch((error: AxiosError) => {
                    reject(error);
                });
        })              
    }

    
    /**
     * Retorna a rota da página inicial.
     * @returns A rota da página inicial.
     */
    const rotaHome = computed(():string => {
        return "/inicio";
    })

    /**
     * Limpa os dados de autenticação do usuário.
     */
    const purgeAuth = () => {
        logado.value = false;
        user.value = {} as User;
        JwtService.destroyToken()
    }
    
    /**
     * Realiza o logout do usuário.
     * Limpa os dados de autenticação e recarrega a página.
     */
    const logout = () => {
        purgeAuth();
        location.reload();
    }; 

    return {
        user,
        logado,
        login,
        me,
        rotaHome,
        logout,
        purgeAuth,
    }
} )

// Interface que define a estrutura da store de autenticação
export interface IAuthStore {
    user: User; // Objeto que representa o usuário autenticado
    logado: boolean; // Indica se o usuário está logado ou não
    login: (email: string, password: string) => Promise<AxiosResponse>; // Função para realizar o login do usuário
    me: () => void; // Função para obter os dados do usuário autenticado
    logout: () => void; // Função para realizar o logout do usuário
    purgeAuth: () => void; // Função para limpar os dados de autenticação
    rotaHome: string; // Rota de redirecionamento após o login
}
   
