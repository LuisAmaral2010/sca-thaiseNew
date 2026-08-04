@extends('layouts.site')
     
@section('content')

<section id="perfil" class="clients section-bg">
<div class="container" data-aos="fade-up">

        <div class="section-title">
          <h3><span>Solicitante</span></h3>
        </div>
</div>
</section>    


<section id="op" class="featured-services">
      <div class="container" data-aos="fade-up">
        
        <div class="row">
          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxs-file-plus'></i></i></div>
              <!-- <div class="icon"><i class="bx bxl-dribbble"></i></div> -->
              <h4 class="title"><a href="/solicitacao/create">Criar solicitação</a></h4>
              <!-- <p class="description">Crie uma nova solicitação, informando material a ser analisado, laboratórios e análises</p> -->
            </div>
          </div>

          <!-- <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bx-file-find' ></i></div>
              <h4 class="title"><a href="/solicitacao/show">Acompanhar solicitação</a></h4>
              <p class="description">Acompanhe cada análise de suas solicitações</p>
            </div>
          </div> -->

          

        </div>

      </div>
    </section>


    <section id="solicitacoes" class="services">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Solicitações</h2>
          <p>Consulte o andamento de suas solicitações:</p>
        </div>

        <div class="row">
        @foreach ($solicitacoes_servicos as $solicitacao_servico)


            <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="icon-box">                
                <h4><a href="/solicitacao/{{ $solicitacao_servico->id }}/fracoesamostra">{{ $solicitacao_servico->desc_amostras }}</a></h4>
                <p>Solicitado em: {{ $solicitacao_servico->data_solicitacao }}</p>
                <p>Status: {{ $solicitacao_servico->status }}</p>
                </div>
            </div>
          @endforeach
        </div>

    </div>
    </section>

<!-- 

<section id="solicitacao">
<div class="container">
    <div>
        <a href="/solicitacao/create" class="btn btn-dark mb-2">Nova requisição</a>
    </div>
    <div class="row">
        <p>Suas requisições:</p>
        
        @foreach ($solicitacoes_servicos as $solicitacao_servico)

        <ul class="list-group">    
            <li class="list-group-item">{{ $solicitacao_servico->id }}</li>
            <li class="list-group-item">{{ $solicitacao_servico->desc_amostras }}</li>
            <li class="list-group-item">{{ $solicitacao_servico->data_solicitacao }}</li>
        </ul>            
        <p></p>
        @endforeach
        
    </div>
    
</div>
</section>    
     -->
   
@endsection