<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $produtos = [
            ['nome'=>'Retrovisor Gvs Xt660', 'descricao'=>'RETROVISOR GVS XT660 GIRO 360° YAMAHA LENTE CONVEXA.', 'preco'=>'113.95','data_validade'=>'2024/04/30','imagem'=>'1_D_NQ_NP_825617-MLB74014605117_012024-O.webp','categoria_id'=>'9'],
            ['nome'=>'Retrovisor Gs 650 F800', 'descricao'=>'Retrovisor Gs 650 F800 Bmw Fixo Convexa Rosca Honda Par', 'preco'=>'44.95','data_validade'=>'2024/04/30','imagem'=>'2_D_NQ_NP_709572-MLB71450169813_092023-O.webp','categoria_id'=>'9'],
            ['nome'=>'Samsung Galaxy A15 4G ', 'descricao'=>'Samsung Galaxy A15 4G Dual SIM 128 GB Azul escuro 4 GB RAM', 'preco'=>'899.38','data_validade'=>'2024/04/30','imagem'=>'3_D_NQ_NP_668543-MLA75547177771_042024-O.webp','categoria_id'=>'3'],
            ['nome'=>'Smartphone Moto G54 5g ', 'descricao'=>'Smartphone Moto G54 5g 128gb 4gb De Ram Grafite Motorola', 'preco'=>'969.40','data_validade'=>'2024/04/30','imagem'=>'4_D_NQ_NP_646196-MLU74979395045_032024-O.webp','categoria_id'=>'3'],

        ];
        foreach ($produtos as $produto) {
            \App\Models\Produto::create($produto);
        }
    }
}
