<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Coordenador;
use App\User;
use App\Agente;
use App\Inspetor;
use App\Empresa;
use App\Docempresa;
use App\Checklistemp;
use App\Endereco;
use App\Telefone;
use App\CnaeEmpresa;
use App\Requerimento;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class CoordenadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function home()
    {
        return view('coordenador.home_coordenador');
    }

    /* Função para listar em tela todas empresas que se cadastraram
    e que o acesso não foi liberado.
    */
    public function listarPendente()
    {
        $empresas = Empresa::where("status_cadastro","pendente")->get();
        return view('coordenador.cadastro_pendente', ["empresa" => $empresas]);
    }

    /* Função para selecionar e exibir na página a empresa que será
    Avaliada
    */
    public function paginaDetalhes(Request $request)
    {
        $empresa = Empresa::find($request->empresa);
        $user = User::where('id', $empresa->user_id)->first();

        // $empresa = Empresa::find("1");
        // $user = User::where('id', "2")->first();
        $endereco = Endereco::where('empresa_id', $empresa->id)->first();
        $telefone = Telefone::where('empresa_id', $empresa->id)->first();
        $cnaeEmpresa = CnaeEmpresa::where('empresa_id', $empresa->id)->get();

        return view("coordenador/avaliar_cadastro")->with([
            "empresa" => $empresa,
            "user"    => $user,
            "endereco" => $endereco,
            "telefone" => $telefone,
            "cnae" => $cnaeEmpresa,
        ]);
    }

    public function licenca(Request $request)
    {
        $empresa = Empresa::find($request->empresa);

        $docsempresa = Docempresa::where('empresa_id', $empresa->id)->get();
        $checklist = Checklistemp::where('empresa_id', $empresa->id)
        ->where('areas_id', $request->area)
        ->orderBy('id','ASC')
        ->get();


        return view("coordenador/avaliar_requerimento")->with([
            "docsempresa"  => $docsempresa,
            "checklist"    => $checklist,
            "empresa"      => $empresa,
            "requerimento" => $request->requerimento,
        ]);
    }

    public function julgarRequerimento(Request $request)
    {

        if ($request->decisao == "true") {
            
            $requerimento = Requerimento::find($request->requerimento);
            $requerimento->status = "aprovado";
            $requerimento->save();

            $inspetores = Inspetor::get();
            $agentes = Agente::get();
            return view('coordenador/requerimento_coordenador',["inspetores" => $inspetores,"agentes" => $agentes])->with('aprovado', 'O requerimento foi aprovado!');

        } else {

            $requerimento = Requerimento::find($request->requerimento);
            $requerimento->status = "reprovado";
            $requerimento->aviso = $request->avisos;
            $requerimento->save();

            $inspetores = Inspetor::get();
            $agentes = Agente::get();
            return view('coordenador/requerimento_coordenador',["inspetores" => $inspetores,"agentes" => $agentes])->with('reprovado', 'O requerimento foi reprovado!');

        }
        
    }

    public function julgar(Request $request)
    {
        // Encontrar email do perfil da empresa
        //*******************************************************
        $useremail = User::find($request->user_id);
        // ******************************************************
        $empresa = Empresa::find($request->empresa_id);

        if($useremail->status_cadastro == "pendente" && $empresa->status_cadastro == "pendente"){

            if($request->decisao == 'true'){

                // Enviar e-mai de comprovação de cadastro de usuário e empresa
                //************************************** */
                $user = new \stdClass();
                $user->name = $useremail->name;
                $user->email = $useremail->email;
                $emp = new \stdClass();
                $emp->nome = $empresa->nome;
                $decisao = new \stdClass();
                $decisao = $request->decisao;

                \Illuminate\Support\Facades\Mail::send(new \App\Mail\ConfirmaCadastroUser($user,$emp,$decisao));
                // *************************************

                $empresa->status_cadastro = "aprovado";
                $useremail->status_cadastro = "aprovado";
                $empresa->save();
                $useremail->save();

                session()->flash('success', 'Cadastros aprovados com sucesso');
                return redirect()->route('/');
            }
            else{

                // Enviar e-mai de reprovação de cadastro de usuário e empresa
                //************************************** */
                $user = new \stdClass();
                $user->name = $useremail->name;
                $user->email = $useremail->email;
                $emp = new \stdClass();
                $emp->nome = $empresa->nome;
                $decisao = new \stdClass();
                $decisao = $request->decisao;

                \Illuminate\Support\Facades\Mail::send(new \App\Mail\ConfirmaCadastroUser($user,$emp,$decisao));
                // *************************************

                $empresa->status_cadastro = "reprovado";
                $useremail->status_cadastro = "reprovado";
                $empresa->save();
                $useremail->save();

              session()->flash('success', 'Cadastros reprovados com sucesso');
              return redirect()->route('/');
            }

        }
        elseif($useremail->status_cadastro == "aprovado" && $empresa->status_cadastro == "pendente"){

            if($request->decisao == 'true'){

                // Enviar e-mai de comprovação de cadastro
                //************************************** */

                $user = new \stdClass();
                $user->name = $useremail->name;
                $user->email = $useremail->email;
                $emp = new \stdClass();
                $emp->nome = $empresa->nome;
                $decisao = new \stdClass();
                $decisao = $request->decisao;

                \Illuminate\Support\Facades\Mail::send(new \App\Mail\ConfirmaCadastroEmpresa($user,$empresa,$decisao));
                // *************************************

                $empresa->status_cadastro = "aprovado";
                $empresa->save();

                session()->flash('success', 'Cadastro aprovado com sucesso');
                return redirect()->route('/');
            }
            else{

                // Enviar e-mai de comprovação de cadastro
                //************************************** */

                $user = new \stdClass();
                $user->name = $useremail->name;
                $user->email = $useremail->email;
                $emp = new \stdClass();
                $emp->nome = $empresa->nome;
                $decisao = new \stdClass();
                $decisao = $request->decisao;

                \Illuminate\Support\Facades\Mail::send(new \App\Mail\ConfirmaCadastroEmpresa($user,$empresa,$decisao));
                // *************************************
                $empresa->status_cadastro = "reprovado";
                $empresa->save();

                session()->flash('success', 'Cadastro reprovado com sucesso');
                return redirect()->route('/');
            }

        }

        // Trecho para o caso de coordenador precisar reavaliar cadastro de empresa
        // elseif ($estabelecimento->status == "Aprovado" || $estabelecimento->status == "Reprovado") {

        //     if($request->decisao == 'true'){

        //         // Enviar e-mai de comprovação de cadastro
        //         //************************************** */

        //         $user = new \stdClass();
        //         $user->name = $userfound[0]->name;
        //         $user->email = $userfound[0]->email;

        //         \Illuminate\Support\Facades\Mail::send(new \App\Mail\SendMailUser($user));
        //         // *************************************

        //         $estabelecimento->status = "Aprovado";
        //         $estabelecimento->save();

        //         session()->flash('success', 'Estabelecimento aprovado com sucesso');
        //         return redirect()->route('estabelecimentoAdmin.revisar');
        //     }
        //     else{
        //       $estabelecimento->status = "Reprovado";
        //       $estabelecimento->save();

        //       session()->flash('success', 'Estabelecimento reprovado com sucesso');
        //       return redirect()->route('estabelecimentoAdmin.revisar');
        //     }
        // }
    }

    public function convidarEmail(Request $request)
    {
        $validationData = $this->validate($request,[
            'email'=>'required|email',
        ]);

        if ($request->tipo == "inspetor") {

            $user = User::where('email',$request->input('email'))->first();
            $empresa = Empresa::where('id', $request->empresa)->first();

            if($user == null){

              $passwordTemporario = Str::random(8);
              Mail::to($request->email)->send(new \App\Mail\CadastroUsuarioPorEmail($passwordTemporario, $request->tipo));
              $user = User::create([
                'name'            => "Inspetor",
                'email'           => $request->email,
                'password'        => bcrypt($passwordTemporario),
                'tipo'            => "inspetor",
                'status_cadastro' => "pendente",
              ]);
              session()->flash('success', 'Um e-mail com o convite foi enviado para o endereço especificado.');
              return back();
            }
            else {
                session()->flash('error', 'O e-mail já está cadastrado no sistema!');
                return back();
            }
        }

        elseif ($request->tipo == "agente") {

            $user = User::where('email',$request->input('email'))->first();
            $empresa = Empresa::where('id', $request->empresa)->first();

            if($user == null){

              $passwordTemporario = Str::random(8);
              Mail::to($request->email)->send(new \App\Mail\CadastroUsuarioPorEmail($passwordTemporario, $request->tipo));
              $user = User::create([
                'name'            => "Agente",
                'email'           => $request->email,
                'password'        => bcrypt($passwordTemporario),
                'tipo'            => "agente",
                'status_cadastro' => "pendente",
              ]);
              session()->flash('success', 'Um e-mail com o convite foi enviado para o endereço especificado.');
              return back();
            }
            else {
                session()->flash('error', 'O e-mail já está cadastrado no sistema!');
                return back();
            }
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'tipo' => "supervisor",
        ]);

        $supervisor = Supervisor::create([
            'userId' => $user->id,
        ]);

        return redirect()->route('home');
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

    /**
     * Funcao: abre a tela de requerimento
     * Tela: requerimento_coordenador.blade.php
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function listarRequerimentoInspetorEAgente()
    {
        $inspetores = Inspetor::get();
        $agentes = Agente::get();
        return view('coordenador/requerimento_coordenador',["inspetores" => $inspetores,"agentes" => $agentes]);
    }
    /**
     * Funcao: listar todos os requerimentos
     * Tela: requerimento_coordenador.blade.php
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function ajaxListarRequerimento(Request $request)
    {
        $this->listarRequerimentos($request->filtro);
    }
    public function listarRequerimentos($filtro){
        $requerimentos = Requerimento::orderBy('created_at', 'ASC')->get();
        $empresas = Empresa::orderBy('created_at', 'ASC')->get();
        $output = '';
        // avaliar cadastro da empresa
        foreach($empresas as $item){
            if($item->status_cadastro == "pendente" && ($filtro == "pendente" || $filtro == "all")){
                $output .='
                    <div class="container cardListagem" id="primeiralicenca">
                    <div class="d-flex">
                        <div class="mr-auto p-2">
                            <div class="btn-group" style="margin-bottom:-15px;">
                                <div class="form-group" style="font-size:15px;">
                                    <div class="textoCampo">'.$item->nome.'</div>
                                    <span>Cadastro pendente</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <div class="form-group" style="font-size:15px;">
                                <div>'.$item->created_at->format('d/m/Y').'</div>
                            </div>
                        </div>
                        <div class="p-2">
                            <div class="dropdown">
                                <button class="btn btn-info  btn-sm" type="button" id="dropdownMenuButton'.$item->id.'" onclick="abrir_fechar_card_requerimento(\''."$item->created_at".'\'+\''."$filtro".'\'+'.$item->id.')">
                                +
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="'.$item->created_at.''.$filtro.''.$item->id.'" style="display:none;">
                        <hr style="margin-bottom:-0.1rem; margin-top:-0.2rem;">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <div class="form-group" style="font-size:15px;">
                                    <div>CNPJ: <span class="textoCampo">'.$item->cnpjcpf.'</span></div>
                                    <div>Tipo: <span class="textoCampo">'.$item->tipo.'</span></div>
                                    <div>Proprietário: <span class="textoCampo">'.$item->user->name.'</span></div>
                                    <div style="margin-top:10px; margin-bottom:-10px;"><button type="button" onclick="empresaId('.$item->id.')" class="btn btn-success">Avaliar</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ';

            }
        }
        // 1º licenca, renovação
        foreach($requerimentos as $item){
                if($item->tipo == "primeira_licenca" && ($filtro == "primeira_licenca" || $filtro == "all") && ($item->status == "pendente")){
                    $output .='
                        <div class="container cardListagem" id="primeiralicenca">
                            <div class="d-flex">
                                <div class="mr-auto p-2">
                                    <div class="btn-group" style="margin-bottom:-15px;">
                                        <div class="form-group" style="font-size:15px;">
                                            <div class="textoCampo">'.$item->empresa->nome.'</div>
                                            <span>Primeira Licença</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <div class="form-group" style="font-size:15px;">
                                        <div>'.$item->created_at->format('d/m/Y').'</div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <div class="dropdown">
                                    <button class="btn btn-info  btn-sm" type="button" id="dropdownMenuButton'.$item->id.'" onclick="abrir_fechar_card_requerimento(\''."$item->created_at".'\'+\''."$filtro".'\'+'.$item->id.')">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="'.$item->created_at.''.$filtro.''.$item->id.'" style="display:none;">
                                <hr style="margin-bottom:-0.1rem; margin-top:-0.2rem;">
                                <div class="d-flex">
                                    <div class="mr-auto p-2">
                                        <div class="btn-group" style="margin-bottom:-15px;">
                                            <div class="form-group" style="font-size:15px;">
                                                <div>CNAE: <span class="textoCampo">'.$item->cnae->descricao.'</span></div>
                                                <div>Responsável Técnico:<span class="textoCampo"> '.$item->resptecnico->user->name.'</span></div>
                                                <div>Status:<span class="textoCampo"> '.$item->status.'</span></div>
                                                <div style="margin-top:10px; margin-bottom:-10px;"><button type="button" onclick="licencaAvaliacao('.$item->empresa->id.','.$item->cnae->areas_id.','.$item->id.')" class="btn btn-success">Avaliar</button></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';

                }elseif($item->tipo == "primeira_licenca" && ($filtro == "primeira_licenca" || $filtro == "all") && ($item->status == "aprovado")){
                    $output .='
                        <div class="container cardListagem" id="primeiralicenca">
                            <div class="d-flex">
                                <div class="mr-auto p-2">
                                    <div class="btn-group" style="margin-bottom:-15px;">
                                        <div class="form-group" style="font-size:15px;">
                                            <div class="textoCampo">'.$item->empresa->nome.'</div>
                                            <span>Primeira Licença</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <div class="form-group" style="font-size:15px;">
                                        <div>'.$item->created_at->format('d/m/Y').'</div>
                                    </div>
                                </div>
                                <div class="p-2">
                                    <div class="dropdown">
                                    <button class="btn btn-info  btn-sm" type="button" id="dropdownMenuButton'.$item->id.'" onclick="abrir_fechar_card_requerimento(\''."$item->created_at".'\'+\''."$filtro".'\'+'.$item->id.')">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="'.$item->created_at.''.$filtro.''.$item->id.'" style="display:none;">
                                <hr style="margin-bottom:-0.1rem; margin-top:-0.2rem;">
                                <div class="d-flex">
                                    <div class="mr-auto p-2">
                                        <div class="btn-group" style="margin-bottom:-15px;">
                                            <div class="form-group" style="font-size:15px;">
                                                <div>CNAE: <span class="textoCampo">'.$item->cnae->descricao.'</span></div>
                                                <div>Responsável Técnico:<span class="textoCampo"> '.$item->resptecnico->user->name.'</span></div>
                                                <div>Status:<span class="textoCampo"> '.$item->status.'</span></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ';

                }elseif($item->tipo == "renovacao"  && ($filtro == "renovacao_de_licenca" || $filtro == "all") && ($item->status == "pendente")){
                    $output .='
                    <div class="container cardListagem">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <div class="btn-group" style="margin-bottom:-15px;">
                                    <div class="form-group" style="font-size:15px;">
                                        <div class="textoCampo">'.$item->empresa->nome.'</div>
                                        <span>Renovacao de Licenca</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="form-group" style="font-size:15px;">
                                    <div>'.$item->created_at->format('d/m/Y').'</div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="dropdown">
                                    <button class="btn btn-info  btn-sm" type="button" id="dropdownMenuButton'.$item->id.'" onclick="abrir_fechar_card_requerimento(\''."$item->created_at".'\'+\''."$filtro".'\'+'.$item->id.')">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="'.$item->created_at.''.$filtro.''.$item->id.'" style="display:none;">
                            <hr style="margin-bottom:-0.1rem; margin-top:-0.2rem;">
                            <div class="d-flex">
                                <div class="mr-auto p-2">
                                    <div class="btn-group" style="margin-bottom:-15px;">
                                        <div class="form-group" style="font-size:15px;">
                                            <div>CNAE: <span class="textoCampo">'.$item->cnae->descricao.'</span></div>
                                            <div>Responsável Técnico:<span class="textoCampo"> '.$item->resptecnico->user->name.'</span></div>
                                            <div>Status:<span class="textoCampo"> '.$item->status.'</span></div>
                                            <div style="margin-top:10px; margin-bottom:-10px;"><button type="button" onclick="licencaAvaliacao('.$item->empresa->id.','.$item->cnae->areas_id.','.$item->id.')" class="btn btn-success">Avaliar</button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                }elseif($item->tipo == "renovacao"  && ($filtro == "renovacao_de_licenca" || $filtro == "all") && ($item->status == "aprovado")){
                    $output .='
                    <div class="container cardListagem">
                        <div class="d-flex">
                            <div class="mr-auto p-2">
                                <div class="btn-group" style="margin-bottom:-15px;">
                                    <div class="form-group" style="font-size:15px;">
                                        <div class="textoCampo">'.$item->empresa->nome.'</div>
                                        <span>Renovacao de Licenca</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="form-group" style="font-size:15px;">
                                    <div>'.$item->created_at->format('d/m/Y').'</div>
                                </div>
                            </div>
                            <div class="p-2">
                                <div class="dropdown">
                                    <button class="btn btn-info  btn-sm" type="button" id="dropdownMenuButton'.$item->id.'" onclick="abrir_fechar_card_requerimento(\''."$item->created_at".'\'+\''."$filtro".'\'+'.$item->id.')">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="'.$item->created_at.''.$filtro.''.$item->id.'" style="display:none;">
                            <hr style="margin-bottom:-0.1rem; margin-top:-0.2rem;">
                            <div class="d-flex">
                                <div class="mr-auto p-2">
                                    <div class="btn-group" style="margin-bottom:-15px;">
                                        <div class="form-group" style="font-size:15px;">
                                            <div>CNAE: <span class="textoCampo">'.$item->cnae->descricao.'</span></div>
                                            <div>Responsável Técnico:<span class="textoCampo"> '.$item->resptecnico->user->name.'</span></div>
                                            <div>Status:<span class="textoCampo"> '.$item->status.'</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                }
        }




        $data = array(
            'success'   => true,
            'table_data' => $output,
        );
        echo json_encode($data);
    }

    public function localizar(Request $request){

        $resultado = Empresa::where('nome','ilike','%'.$request->localizar.'%')->get();

        $output = '';
            if($resultado->count() > 0){
                    $output .= '<div class="container" style="font-weight:bold;">Estabelecimento</div>';
                foreach($resultado as $item){
                    $output .= '<div id="idEstabelecimentoLocalizar'.$item->id.'"  class="container" onmouseenter="mostrarSelecaoLocalizar('.$item->id.')"><a href='.route('mostrar.empresas','value='.Crypt::encrypt($item->id)).' style="font-weight:bold; color:black;text-decoration:none; font-family: Quicksand;"><div>'.$item->nome.'</div></a></div>';
                }
            }else{
                $output .= '<div class="container">Nenhum resultado encontrado para <span style="font-weight:bold">'.$request->localizar.'</span></div>';
            }
        $data = array(
            'success'   => true,
            'table_data' => $output,
        );


        echo json_encode($data);
    }
}


// href="{{ route('mostrar.empresas',["value" => Crypt::encrypt($item->id)]) }}"
