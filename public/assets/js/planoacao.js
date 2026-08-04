
    $(document).ready(function () {

        /*------------------------------------------
        --------------------------------------------
        Country Dropdown Change Event
        --------------------------------------------
        --------------------------------------------*/
        $('#select_planoAcao_in_id').on('change', function () {
            var idPa = this.value;
            alert('aqui');
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
                    
                    $('#select_usuarioResponsavelPa_in_id').html('<option value="">-- Select Responsavel --</option>');
                    $.each(result.responsaveis, function (key, value) {
                        $("#select_usuarioResponsavelPa_in_id").append('<option value="' + value.id + '">' 
                        + value.name + '</option>');
                    });
                }
            });
        });

        

    });