<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inspetor;
use App\User;
use App\Inspecao;
use App\Endereco;
use App\Telefone;
use Auth;

class InspetorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listarInspetores()
    {
        $inspetores = User::where("tipo", "inspetor")->where("status_cadastro", "aprovado")->get();
        return view('coordenador/inspetores_coordenador', [ 'inspetores'  => $inspetores ]);
    }

    public function home()
    {
        return view('inspetor.home_inspetor');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::find(Auth::user()->id);
        // Tela de conclusão de cadastro de agente
        return view('inspetor.cadastrar_inspetor')->with(["user" => $user->email]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $validator = $request->validate([
            'nome'     => 'required|string',
            'formacao' => 'required|string',
            'especializacao' => 'nullable|string',
            'cpf'            => 'required|string',
            'telefone'       => 'required|string',
            'password'       => 'required',
        ]);

        // Atualiza dados de user para inspetor
        $user->name = $request->nome;
        $user->password = bcrypt($request->password);
        $user->status_cadastro = "aprovado";
        $user->save();

        $inspetor = Inspetor::create([
            'formacao'       => $request->formacao,
            'especializacao' => $request->especializacao,
            'cpf'            => $request->cpf,
            'telefone'       => $request->telefone,
            'user_id'        => $user->id,
        ]);


        return redirect()->route('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function inspecoes(Request $request)
    {
        $inspecoes = Inspecao::where('inspetor_id', 1)
        ->where('status', 'pendente')->get();
        $temp = [];

        foreach ($inspecoes as $indice) {
            $endereco = Endereco::where('empresa_id', $indice->requerimento->empresa->id)
            ->first();
            $telefone = Telefone::where('empresa_id', $indice->requerimento->empresa->id)
            ->first();

            $obj = (object) array(
                'empresa_nome'  => $indice->requerimento->empresa->nome,
                'rua'           => $endereco->rua,
                'numero'        => $endereco->numero,
                'bairro'        => $endereco->bairro,
                'cep'           => $endereco->cep,
                'cnpjcpf'          => $indice->requerimento->empresa->cnpjcpf,
                'representante_legal' => $indice->requerimento->empresa->user->name,
                'telefone1'     => $telefone->telefone1,
                'telefone2'     => $telefone->telefone2,
                'data'          => $indice->data,
                'status'        => $indice->status,
            );
            array_push($temp, $obj);
        }
    }
    /*
    * FUNCAO: Mostrar a pagina de programacao
    * ENTRADA:
    * SAIDA: Listar inspecoes programadas para o inspetor
    */
    public function showProgramacao(){

        $inspetor = Inspetor::where('user_id','=',Auth::user()->id)->first();
        $inspecoes = Inspecao::where('inspetor_id',$inspetor->id)->where('status', 'pendente')->orderBy('data', 'ASC')->get();

        return view('inspetor/programacao_inspetor', ['inspecoes' => $inspecoes]);
    }
}
