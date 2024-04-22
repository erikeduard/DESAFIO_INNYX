export const extrairDadosResponse = (response:dadosPaginacaoInterface):dadosPaginacaoInterface => {
    const {current_page, from, last_page, per_page, to, total} = response;
    return {current_page, from, last_page, per_page, to, total,page:current_page};
};
export interface dadosPaginacaoInterface {
    per_page: number,
    page: number,
    total: number,
    current_page?:number,
    from?:number,
    last_page?:number,
    to?:number,

}
export interface eventPaginator {
    page: number,
    first: number,
    rows: number,
    pageCount: number
}