<x-layout title="Nova Solicitação">
<section id="novasolicitacao"    >

    <h3>Solicitando uma nova requisição: </h3>
    <form action="/solicitacao/salvar" method="post">
        @csrf
        <div class="mv-3">
            <label for="planoAcao_in_id" class="form-label">Plano de Ação:</label>
            <select id="select_planoAcao_in_id" name="select_planoAcao_in_id" class="form-control">
                <option value="">-- Selecione um plano de ação --</option>
                @foreach($planoacoes as $planoacao)
                    <option value="{{ $planoacao->id }}">{{ $planoacao->tx_codigo .' - '. $planoacao->tx_titulo }}</option>
                @endforeach
            </select>

            <label for="usuarioResponsavelPa_in_id" class="form-label">Responsavel pelo PA:</label>            
            <p><label class="form-label"></label></p>
            <input type="text" id="usuarioResponsavelPa_in_id" name="usuarioResponsavelPa_in_id" class="form-control">
            <select id="select_usuarioResponsavelPa_in_id" class="form-control">
            </select>

            <label for="desc_pa" class="form-label">Descrição do PA:</label>
            <p><label class="form-label"></label></p>

            <label for="desc_amostras" class="form-label">Nome do material:</label>
            <input type="text" id="desc_amostras" name="desc_amostras" class="form-control">

            <label for="observacao" class="form-label">Observação:</label>
            <input type="text" id="observacao" name="observacao" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>
    </section>   
    
    


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
  
            /*------------------------------------------
            --------------------------------------------
            Laboratorio Dropdown Change Event
            --------------------------------------------
            --------------------------------------------*/
            $('#select_planoAcao_in_id').on('change', function () {
                var idPa = this.value;
                $("#select_usuarioResponsavelPa_in_id").html('');
                $.ajax({
                    url: "{{url('api/fetch-responsavel')}}",
                    type: "POST",
                    data: {
                        pa_id: idPa,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#select_usuarioResponsavelPa_in_id').html('<option value="">-- Select resp --</option>');
                        $.each(result.responsaveis, function (key, value) {
                            $("#select_usuarioResponsavelPa_in_id").append('<option value="' + value
                                .id + '">' + value.nome + '</option>');
                        });
                    }
                });
            });
  
            
  
        });
    </script>      
</x-layout>