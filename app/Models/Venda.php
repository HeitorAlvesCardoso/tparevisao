<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'produto_venda')
            ->withPivot('quantidade', 'preco_unitario')
            ->withTimestamps();
    }
}
