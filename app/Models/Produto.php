<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'categoria',
        'garantia_meses',
        'preco',
        'estoque',
    ];

    protected function casts(): array
    {
        return [
            'garantia_meses' => 'integer',
            'preco' => 'decimal:2',
            'estoque' => 'integer',
        ];
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function vendas(): BelongsToMany
    {
        return $this->belongsToMany(Venda::class, 'produto_venda')
            ->withPivot('quantidade', 'preco_unitario')
            ->withTimestamps();
    }
}
