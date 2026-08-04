@extends('layouts.app')

@section('content')
<section id="perfil" class="clients section-bg">
<div class="container" data-aos="fade-up">

        <div class="section-title">
          <h3><span>Laboratório</span></h3>
        </div>
</div>
</section>    


<section id="op" class="featured-services">
      <div class="container" data-aos="fade-up">
        
        <div class="row">
          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bi bi-card-checklist"></i></div>
              <h4 class="title"><a href="/laboratorio">Aceitar amostra</a></h4>
              <!-- <p class="description"></p> -->
            </div>
          </div>

          <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class="bi bi-filetype-doc"></i></div>
              <h4 class="title"><a href="/laboratorio">Emitir laudo DOC</a></h4>
              <!-- <p class="description"></p> -->
            </div>
          </div>
          

        </div>

      </div>
    </section>
@endsection