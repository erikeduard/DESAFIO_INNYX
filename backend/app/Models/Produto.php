<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Produto
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $nome
 * @property string $descricao
 * @property float $preco
 * @property \Illuminate\Support\Carbon $data_validade
 * @property string $imagem
 * @property int $categoria_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property \App\Models\Categoria $categoria
 */
class Produto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'data_validade',
        'imagem',
        'categoria_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $dates = ['data_validade'];

    /**
     * Get the category that the product belongs to.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
