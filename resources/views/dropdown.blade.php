<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta nome="csrf-token" content="content">
    <meta nome="csrf-token" content="{{ csrf_token() }}">
    <meta nome="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel AJAX Dependent Laboratorio Servico City Dropdown Example - ItSolutionStuff.com</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4" >
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-primary mb-4 text-center">
                   <h4 >Laravel AJAX Dependent Laboratorio Servico City Dropdown Example  ItSolutionStuff.com</h4>
                </div> 
                <form>
                    <div class="form-group mb-3">
                        <select  id="laboratorio-dropdown" class="form-control">
                            <option value="">-- Select Laboratorio --</option>
                            @foreach ($laboratorios as $data)
                            <option value="{{$data->id}}">
                                {{$data->nome}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <select id="servico-dropdown" class="form-control">
                        </select>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
  
            /*------------------------------------------
            --------------------------------------------
            Laboratorio Dropdown Change Event
            --------------------------------------------
            --------------------------------------------*/
            $('#laboratorio-dropdown').on('change', function () {
                var idLaboratorio = this.value;
                $("#servico-dropdown").html('');
                $.ajax({
                    url: "{{url('api/fetch-servicos')}}",
                    type: "POST",
                    data: {
                        laboratorio_id: idLaboratorio,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#servico-dropdown').html('<option value="">-- Select Servico --</option>');
                        $.each(result.servicos, function (key, value) {
                            $("#servico-dropdown").append('<option value="' + value
                                .id + '">' + value.nome + '</option>');
                        });
                    }
                });
            });
  
            
  
        });
    </script>
</body>
</html>