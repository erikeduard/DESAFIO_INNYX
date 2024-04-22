<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a category in the application.
 */
class Categoria extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nome'];

    /**
     * Get the products associated with the category.
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }
}
