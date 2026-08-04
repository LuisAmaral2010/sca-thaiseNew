@extends('layouts.site')
     
@section('content')
     <section id="home" class="featured-services">
      <div class="container" data-aos="fade-up">

        <div class="row">
            <p>Selecione o perfil desejado:</p>
        </div>
        
        <div class="row">
          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxs-institution'></i></div>
              <!-- <div class="icon"><i class="bx bxl-dribbble"></i></div> -->
              <h4 class="title"><a href="/cra">CRA</a></h4>
              <p class="description">Recebe amostra, gera laudo pdf, gerencia lista de laboratórios e gerencia permissões de acesso</p>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxs-flask'></i></div>
              <h4 class="title"><a href="/laboratorio">Laboratório</a></h4>
              <p class="description">Aceita amostra e emite laudo doc</p>
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class="bi bi-person-fill-gear"></i></div>
              <h4 class="title"><a href="/resptec">Resp Tec</a></h4>
              <p class="description">Aprova laudo, gerencia permissões de laboratório e gerencia cadastro de análises</p>
            </div>
          </div>


          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
              <div class="icon"><i class="bi bi-person-circle"></i></div>
              <h4 class="title"><a href="/solicitacao">Solicitante</a></h4>
              <p class="description">Gerencia suas requisições</p>
            </div>
          </div>
          

        </div>

      </div>
    </section>
@endsection