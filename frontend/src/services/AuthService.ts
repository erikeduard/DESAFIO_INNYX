import api from './api';
import type {AxiosError, AxiosResponse} from 'axios';

export interface LoginData {
    email: string;
    password: string;
    token: string;
    }

export class AuthService {

    /**
        * Faz o login de um usuário com o login e senha fornecidos.
        * @param email - O login do usuário.
        * @param password - A senha do usuário.
        * @returns Uma promise que resolve para um AxiosResponse contendo os dados do login.
        * @throws Um AxiosError se ocorrer um erro durante o processo de login.
        */
    
     
    static Login = (email:string, password:string):Promise<AxiosResponse<LoginData>> => {
        return api
            .post('/login', {email, password})
            .then((response: AxiosResponse<LoginData>) => {
                return response;
            })
            .catch((error: AxiosError) => {
                throw error;
            });
    }
}