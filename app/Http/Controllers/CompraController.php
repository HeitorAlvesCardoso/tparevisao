<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(): View
    {
        $vendas = Venda::with('produtos')
            ->where('cliente_id', Auth::id())
            ->latest()
            ->get();

        return view('compras.index', compact('vendas'));
    }

    public function create(): View
    {
        $produtos = Produto::orderBy('nome')->get();

        return view('compras.create', compact('produtos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'itens' => ['nullable', 'array'],
            'itens.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $itens = collect($dados['itens'] ?? [])
            ->map(fn ($quantidade) => (int) $quantidade)
            ->filter(fn ($quantidade) => $quantidade > 0);

        if ($itens->isEmpty()) {
            return back()
                ->withErrors(['itens' => 'Selecione ao menos um item para comprar.'])
                ->withInput();
        }

        $produtos = Produto::whereIn('id', $itens->keys())->get()->keyBy('id');

        foreach ($itens as $produtoId => $quantidade) {
            $produto = $produtos->get($produtoId);

            if (! $produto) {
                return back()
                    ->withErrors(['itens' => 'Produto inválido na compra.'])
                    ->withInput();
            }

            if ($quantidade > $produto->estoque) {
                return back()
                    ->withErrors(['itens' => "Estoque insuficiente para {$produto->nome} (disponível: {$produto->estoque})."])
                    ->withInput();
            }
        }

        $venda = Venda::create(['cliente_id' => Auth::id()]);

        foreach ($itens as $produtoId => $quantidade) {
            $produto = $produtos->get($produtoId);

            $venda->produtos()->attach($produtoId, [
                'quantidade' => $quantidade,
                'preco_unitario' => $produto->preco,
            ]);

            $produto->decrement('estoque', $quantidade);
        }

        return redirect()->route('compras.index')->with('sucesso', 'Compra realizada!');
    }
}
