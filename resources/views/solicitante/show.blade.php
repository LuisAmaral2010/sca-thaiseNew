<x-layout title="Solicitação">

<section id="perfil" class="clients section-bg">
<div class="container" data-aos="fade-up">

        <div class="section-title">
          <h3><span>Solicitante</span></h3>
        </div>
</div>
</section>    


<section id="op" class="featured-services">
      <div class="container" data-aos="fade-up">
        
        
    <section id="solicitacoes" class="services">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2>Solicitação {{ $solicitacao->id }}</h2>
          
        </div>

        <div class="row">



            <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                <div class="icon-box">                
                <h4><a href="">{{ $solicitacao->desc_amostras }}</a></h4>
                <p>Solicitado em: {{ $solicitacao->data_solicitacao }}</p>
                <p>Status: {{ $solicitacao->status }}</p>
                </div>
            </div>

        </div>

    </div>
    </section>


   
</x-layout>