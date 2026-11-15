<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProdutoController extends Controller
{
    public function index(): View
    {
        $produtos = Produto::with('vendedor')->orderBy('nome')->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create(): View
    {
        return view('produtos.create');
    }

    public function store(StoreProdutoRequest $request): RedirectResponse
    {
        $produto = new Produto($request->validated());
        $produto->vendedor_id = Auth::id();
        $produto->save();

        return redirect()->route('produtos.index')->with('sucesso', 'Produto cadastrado!');
    }
}
