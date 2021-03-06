<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fornecedor;

class FornecedorController extends Controller
{
    public function index()
    {
        return view('app.fornecedor.index');
    }

    public function listar(Request $request)
    {
        $fornecedores = Fornecedor::where('nome', 'like', '%' . $request->input('nome') . '%')
                                    ->where('site', 'like', '%' . $request->input('site') . '%')
                                    ->where('uf', 'like', '%' . $request->input('uf') . '%')
                                    ->where('email', 'like', '%' . $request->input('email') . '%')
                                    ->paginate(3);

        return view('app.fornecedor.listar', ['fornecedores' => $fornecedores, 'request' => $request->all()]);
    }

    public function adicionar(Request $request)
    {
        $msg = '';

        if ($request->input('_token') != '' && $request->input('id') == '') {  // Inserção de um registro
            // Validação:

            $regras = [
                'nome' => 'required|min:3|max:40',
                'site' => 'required',
                'uf' => 'required|min:2|max:2',
                'email' => 'email',
            ];

            $feedback = [
                'required' => 'O campo :attribute deve ser preenchido',
                'nome.min' => 'O campo nome deve ter no mínimo 3 caracteres',
                'nome.max' => 'O campo nome deve ter no máximo 40 caracteres',
                'uf.min' => 'O campo UF deve ter no mínimo 2 caracteres',
                'uf.max' => 'O campo UF deve ter no máximo 2 caracteres',
                'email.email' => 'O campo email deve ser válido',
            ];

            $request->validate($regras, $feedback);
            Fornecedor::create($request->all());
            $msg = 'Cadastro realizado com sucesso';
        }

        if ($request->input('_token') != '' && $request->input('id') != '') {  // Edição de um registro
            // Fornecedor::find($request->input('id'))->update($request->all());

            $fornecedor = Fornecedor::find($request->input('id'));
            $update = $fornecedor->update($request->all());

            if ($update) {
                $msg = 'Atualização realizada com sucesso';
            } else {
                $msg = 'Erro ao tentar atualizar o registro';
            }

            return redirect()->route('app.fornecedor.editar', ['id' => $request->input('id'), 'mensagem' => $msg]);
        }

        return view('app.fornecedor.adicionar', ['mensagem' => $msg]);
    }

    public function editar(int $id, string $mensagem = '')
    {
        /* Aproveitando a view de adicionar fornecedor para também editar fornecedor */
        return view('app.fornecedor.adicionar', ['fornecedor' => Fornecedor::find($id), 'mensagem' => $mensagem]);
    }

    public function excluir(int $id)
    {
        // Fornecedor::find($id)->delete();
        Fornecedor::find($id)->forceDelete();  // Forçando a remoção (a tabela fornecedores tem soft delete)
        return redirect()->route('app.fornecedor');
    }
}
