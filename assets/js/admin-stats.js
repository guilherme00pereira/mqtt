(function ($) {

    $('#statistics-tabs').tabs(
        {
            active: 0,
        }
    );

    $('#gen_file_btn').click(function () {
        if ($('#gen_file_name').val() == "") {
            alert("Insira o nome do arquivo");
            return;
        }
        if ($('#gen_file_datetime').val() == "") {
            alert("Insira a data e hora do arquivo");
            return;
        }
        if ($('#gen_file_ext').val() == "") {
            alert("Insira a extensão do arquivo");
            return;
        }
        
        $filename = $('#gen_file_name').val() + "_DEL";
        $filename += "_" + $('#gen_file_datetime').val();
        $filename += "." + $('#gen_file_ext').val();
        $('#gen_file_result').text($filename);
    });

})(jQuery);