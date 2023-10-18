(function ($) {

    $('#statistics-tabs').tabs(
        {
            active: 0,
        }
    );

    $('#gen_file_btn').click(function () {
        const genfilename = $('#gen_file_name').val();
        const genfiledatetime = $('#gen_file_datetime').val();
        const genfileext = $('#gen_file_ext').val();

        if (genfilename.val() === "") {
            alert("Insira o nome do arquivo");
            return;
        }
        if (genfiledatetime.val() === "") {
            alert("Insira a data e hora do arquivo");
            return;
        }
        if (genfileext.val() === "") {
            alert("Insira a extensão do arquivo");
            return;
        }

        let filename = genfilename.val() + "_DEL";
        filename += "_" + genfiledatetime.val();
        filename += "." + genfileext.val();
        $('#gen_file_result').text(filename);
    });

    $('#logFileBtn').click(function () {
        $('#loadingLog').show();
        let params = {
            action: ajaxobj.action_log,
            nonce: ajaxobj.nonce,
        };
        $.get(
            ajaxobj.ajaxurl,
            params,
            function (res) {
                console.log(res)
                $('#loadingLog').hide();
                $("#logFileContent").html(res);
            },
            "json"
        );
    });

})(jQuery);