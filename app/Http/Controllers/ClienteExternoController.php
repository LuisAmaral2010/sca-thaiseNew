<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteExternoRequest;
use App\Models\ClienteExterno;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

// CRUD de clientes externos (pessoas físicas e jurídicas)
class ClienteExternoController extends Controller
{
    // Listar, com busca por nome / nome fantasia / CPF-CNPJ e filtro por tipo
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'busca' => ['nullable', 'string', 'max:255'],
            'tipo_pessoa' => ['nullable', 'in:F,J'],
        ]);

        $busca = trim($filtros['busca'] ?? '');
        $buscaDocumento = preg_replace('/\D/', '', $busca);

        $clientes = ClienteExterno::query()
            ->when($busca !== '', function ($query) use ($busca, $buscaDocumento) {
                $query->where(function ($q) use ($busca, $buscaDocumento) {
                    $q->where('nome', 'like', "%{$busca}%")
                        ->orWhere('nome_fantasia', 'like', "%{$busca}%");

                    if ($buscaDocumento !== '') {
                        $q->orWhere('cpf_cnpj', 'like', "%{$buscaDocumento}%");
                    }
                });
            })
            ->when($filtros['tipo_pessoa'] ?? null, fn ($query, $tipo) => $query->where('tipo_pessoa', $tipo))
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ClienteExterno $cliente) => [
                'cliente_externo_id' => $cliente->cliente_externo_id,
                'tipo_pessoa' => $cliente->tipo_pessoa,
                'nome' => $cliente->nome,
                'nome_fantasia' => $cliente->nome_fantasia,
                'cpf_cnpj_formatado' => $cliente->cpf_cnpj_formatado,
                'email' => $cliente->email,
                'telefone' => $cliente->telefone,
                'cidade_uf' => collect([$cliente->cidade, $cliente->uf])->filter()->implode('/') ?: null,
                'is_ativo' => $cliente->is_ativo,
            ]);

        return Inertia::render('ClientesExternos/Index', [
            'clientes' => $clientes,
            'filtros' => [
                'busca' => $busca,
                'tipo_pessoa' => $filtros['tipo_pessoa'] ?? null,
            ],
        ]);
    }

    // Busca (JSON) de clientes ativos por nome, nome fantasia ou CPF/CNPJ —
    // usada no formulário de requisição externa
    public function buscar(Request $request)
    {
        $termo = trim((string) $request->query('q', ''));
        $termoDocumento = preg_replace('/\D/', '', $termo);

        if (mb_strlen($termo) < 2) {
            return response()->json([]);
        }

        $clientes = ClienteExterno::query()
            ->where('is_ativo', true)
            ->where(function ($q) use ($termo, $termoDocumento) {
                $q->where('nome', 'like', "%{$termo}%")
                    ->orWhere('nome_fantasia', 'like', "%{$termo}%");

                if (strlen($termoDocumento) >= 3) {
                    $q->orWhere('cpf_cnpj', 'like', "%{$termoDocumento}%");
                }
            })
            ->orderBy('nome')
            ->limit(10)
            ->get()
            ->map(fn (ClienteExterno $cliente) => [
                'cliente_externo_id' => $cliente->cliente_externo_id,
                'tipo_pessoa' => $cliente->tipo_pessoa,
                'nome' => $cliente->nome,
                'nome_fantasia' => $cliente->nome_fantasia,
                'cpf_cnpj_formatado' => $cliente->cpf_cnpj_formatado,
                'cidade_uf' => collect([$cliente->cidade, $cliente->uf])->filter()->implode('/') ?: null,
            ]);

        return response()->json($clientes);
    }

    public function show(ClienteExterno $cliente_externo)
    {
        return Inertia::render('ClientesExternos/Show', [
            'cliente' => [
                ...$cliente_externo->toArray(),
                'cpf_cnpj_formatado' => $cliente_externo->cpf_cnpj_formatado,
                'data_nascimento_formatada' => $cliente_externo->data_nascimento?->format('d/m/Y'),
                'created_at_formatted' => $cliente_externo->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $cliente_externo->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('ClientesExternos/Create');
    }

    public function store(ClienteExternoRequest $request)
    {
        try {
            $cliente = ClienteExterno::create($request->validated());

            return redirect()
                ->route('clientes_externos.show', $cliente)
                ->with('success', 'Cliente externo cadastrado com sucesso!');
        } catch (Exception $e) {
            Log::error('Cliente externo não cadastrado.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Cliente externo não cadastrado!');
        }
    }

    public function edit(ClienteExterno $cliente_externo)
    {
        return Inertia::render('ClientesExternos/Edit', [
            'cliente' => $cliente_externo,
        ]);
    }

    public function update(ClienteExternoRequest $request, ClienteExterno $cliente_externo)
    {
        try {
            $cliente_externo->update($request->validated());

            return redirect()
                ->route('clientes_externos.show', $cliente_externo)
                ->with('success', 'Cliente externo editado com sucesso!');
        } catch (Exception $e) {
            Log::error('Cliente externo não editado.', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Cliente externo não editado!');
        }
    }

    public function destroy(ClienteExterno $cliente_externo)
    {
        // Cliente com requisições não pode ser excluído (mantém o histórico)
        if ($cliente_externo->solicitacoesServicos()->exists()) {
            return back()->with('error', 'Este cliente externo tem requisições cadastradas e não pode ser excluído. Desmarque "Cliente ativo" para desativá-lo.');
        }

        try {
            $cliente_externo->delete();

            return redirect()
                ->route('clientes_externos.index')
                ->with('success', 'Cliente externo excluído com sucesso!');
        } catch (Exception $e) {
            Log::error('Cliente externo não excluído.', ['error' => $e->getMessage()]);

            return back()->with('error', 'Cliente externo não excluído!');
        }
    }
}
