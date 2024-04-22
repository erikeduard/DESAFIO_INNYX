<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $categorias = [
            ['nome' => 'Alimentos'],
            ['nome' => 'Bebidas'],
            ['nome' => 'Eletrônicos'],
            ['nome' => 'Informática'],
            ['nome' => 'Móveis'],
            ['nome' => 'Roupas'],
            ['nome' => 'Saúde'],
            ['nome' => 'Serviços'],
            ['nome' => 'Veículos'],
        ];

        foreach ($categorias as $categoria) {
            \App\Models\Categoria::create($categoria);
        }
    }
}
