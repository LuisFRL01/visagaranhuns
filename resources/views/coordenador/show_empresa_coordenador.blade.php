@extends('layouts.app')

@section('content')
<div class="container">
    <div class="barraMenu">
        <div class="d-flex justify-content-center">
            <div class="mr-auto p-2 styleBarraPrincipalMOBILE">
                <a href="javascript: history.go(-1)" style="text-decoration:none;cursor:pointer;color:black;">
                    <div class="btn-group">
                        <div style="margin-top:1px;margin-left:5px;"><img src="{{ asset('/imagens/logo_voltar.png') }}" alt="Logo" style="width:13px;"/></div>
                        <div style="margin-top:2.4px;margin-left:10px;font-size:15px;">Voltar</div>
                    </div>
                </a>
            </div>
            <div class="mr-auto p-2 styleBarraPrincipalPC">
                <div class="form-group">
                    <div class="tituloBarraPrincipal">Perfil do estabelecimento</div>
                    <div>
                        <div style="margin-left:10px; font-size:13px;margin-top:2px; margin-bottom:-15px;color:gray;">Início > Áreas > CNAE > Estabelecimentos > <label class="limiteDeTexto" style="margin-bottom:-0.3rem;">{{$empresa->nome}}</label></div>
                    </div>
                </div>
            </div>
            <div class="p-2">
                <div class="dropdown" style="margin-top:10px; width:20%;">
                    {{-- <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ações
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">Criar cnae</a>
                        <a class="dropdown-item" href="#">Editar área</a> --}}
                        {{-- <a class="dropdown-item" href="#">Editar área</a>
                        <a class="dropdown-item" href="#">Deletar área</a> --}}
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="barraMenu" style="margin-top:2rem; margin-bottom:4rem;padding:15px;">
        <div class="container" style="margin-top:1rem;">
            <div class="form-row">
                <div class="form-group col-md-12" >
                    <div>
                        <label class="limiteDeTexto" style="color:black; font-size:35px;  margin-bottom:-10px; font-weight:400; font-family: 'Libre Baskerville', serif;;
                        ;">{{$empresa->nome}}</label>
                    </div>
                    <hr size = 7 style="margin-bottom:-2px;">
                </div>

                <div class="form-group col-md-7">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label style="font-size:19px;margin-top:10px; margin-bottom:-5px; font-family: 'Roboto', sans-serif;">INFORMAÇÕES DO ESTABELECIMENTO</label>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Nome: </label>
                            <span class="limiteDeTexto" style="color:#707070;">{{$empresa->nome}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">CNPJ: </label>
                            <span style="color:#707070">{{$empresa->cnpjcpf}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Tipo: </label>
                            <span style="color:#707070">{{$empresa->tipo}}</span>
                        </div>

                        <div class="form-group col-md-12">
                            <label style="margin-top:10px;margin-bottom:-5px; font-family: 'Roboto', sans-serif;">Endereço</label>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="margin-bottom:-15px; font-weight:normal;font-family: 'Roboto', sans-serif;">Rua: </label>
                            <span style="margin:0px;color:#707070">{{$endereco->rua}},</span>
                            <span style="margin:0px;color:#707070"> nº{{$endereco->numero}},</span>
                            <span style="margin:0px;color:#707070"> {{$endereco->bairro}},</span>
                            <span style="margin:0px;color:#707070"> {{$endereco->cidade}}/{{$endereco->uf}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:1px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">CEP: </label>
                            <span style="color:#707070">{{$endereco->cep}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Complemento: </label>
                            <span style="color:#707070">{{$endereco->complemento}}</span>
                        </div>


                        <div class="form-group col-md-12">
                            <label style="margin-top:10px;margin-bottom:-5px;font-family: 'Roboto', sans-serif;">Contato</label>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">E-mail: </label>
                            <span style="color:#707070">{{$empresa->email}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Telefone 1: </label>
                            <span style="color:#707070">{{$telefone->telefone1}}</span>
                        </div>
                        <div class="form col-md-12" style="margin-top:-10px;margin-bottom:1rem;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Telefone 2: </label>
                            <span style="color:#707070">{{$telefone->telefone2}}</span>
                        </div>
                    </div>
                </div>
                <div class="form col-md-5" style="margin-top:10px;">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                                <label style="font-size:19px;margin-bottom:-5px; font-family: 'Roboto', sans-serif;">INFORMAÇÕES DO GERENTE</label>
                            </div>
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Nome: </label>
                            <span class="limiteDeTexto" style="color:#707070">{{$empresa->user->name}}</span>
                        </div>
                        {{-- <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">CPF: </label>
                            <span style="color:#707070">000.000.000-00</span>
                        </div> --}}
                        <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">E-mail: </label>
                            <span style="color:#707070">{{$empresa->user->email}}</span>
                        </div>
                        {{-- <div class="form col-md-12" style="margin-top:-10px;">
                            <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Telefone: </label>
                            <span style="color:#707070">(00) 0000-0000</span>
                        </div> --}}
                    </div>
                    <div class="form-group col-md-12">
                        <label style="font-size:19px;margin-top:10px;margin-bottom:-5px; font-family: 'Roboto', sans-serif; margin-left: -15px;">INFORMAÇÕES DO RESPONSÁVEL TÉCNICO</label>
                    </div>
                    <div id="idTabela" class="form-row overflow-auto" style="height: 205px;">
                        @if(count($rt)>0)
                            @foreach ($rt as $rt) 
                                <div class="cardRT" style="margin-bottom:8px;">
                                    <div class="row">
                                        <div class="col-9">
                                            <div class="form col-md-12" style="margin-top:5px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Nome: </label>
                                                <span style="color:#707070">{{$rt->user->name}}</span>
                                            </div>
                                            <div class="form col-md-12" style="margin-top:-10px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">CPF: </label>
                                                <span style="color:#707070">{{$rt->cpf}}</span>
                                            </div>
                                            <div class="form col-md-12" style="margin-top:-10px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Formação: </label>
                                                <span style="color:#707070">{{$rt->formacao}}</span>
                                            </div>
                                            <div class="form col-md-12" style="margin-top:-10px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Especialização: </label>
                                                <span style="color:#707070">{{$rt->especializacao}}</span>
                                            </div>
                                            <div class="form col-md-12" style="margin-top:-10px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Telefone: </label>
                                                <span style="color:#707070">{{$rt->telefone}}</span>
                                            </div>
                                            <div class="form col-md-12" style="margin-top:-10px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">E-mail: </label>
                                                <span style="color:#707070">{{$rt->user->email}}</span>
                                            </div>
                                            {{-- <div class="form col-md-12" style="margin-top:-5px;">
                                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;"><a type="button" class="btn btn-primary" href="{{ route() }}">Visualizar Documentos</a>
                                            </div>  --}}
                                        </div>
                                        <div class="col-2">
                                            <div class="p-2" style="margin-right:20px">
                                                <div class="dropdown" style="margin-top:-3px;">
                                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Ações
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="{{ route('documentos.rt', ['rt_id' => Crypt::encrypt($rt->id)]) }}">Visualizar Documentos</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="form col-md-12" style="margin-top:10px;">
                                <label style="font-weight:normal;font-family: 'Roboto', sans-serif;">Nenhum responsável técnico cadastrado</label>
                            </div>
                        @endif
                    </div>
                </div>


                <div class="form-row">
                    <div class="col-12" style="margin-bottom:10px;">
                            <label style="font-size:19px;margin-top:0px;margin-bottom:-5px; font-family: 'Roboto', sans-serif;">CNAE</label>
                    </div>
                    <div class="col-12">
                        @foreach($cnae as $item)
                            <div class="form col-md-12" style="margin-top:-10px;margin-bottom:5px;margin-left:-10px;">
                                <img src="{{ asset('/imagens/logo_ponto.png') }}" alt="Logo" style="margin-top:-3px; margin-right:5px;"/>
                                <label >{{$item->cnae->codigo}} </label> |
                                <span style="color:#707070">{{$item->cnae->descricao}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:2rem; margin-bottom:1rem">
                <div class="col-auto mr-auto"></div>
                <div class="col-auto">

                </div>
            </div>
        </div>
        </div>
    </div>
</div>


@endsection


