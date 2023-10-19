(function ($) {

    $(document).ready(function () {
        $('#gen_file_datetime').flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            defaultDate: new Date(),
        });
    });

    $('#statistics-tabs').tabs(
        {
            active: 0,
        }
    );

    $('#gen_file_btn').click(function () {
        const genfilename = $('#gen_file_name');
        const genfiledatetime = $('#gen_file_datetime');
        const genfileext = $('#gen_file_ext');

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
        filename += "_" + genfiledatetime.val().replace(/[- ]/g, "_");
        filename += "." + genfileext.val();
        $('#gen_file_result').show().val(filename);
        $('#gen_file_copyname').show();
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

    $('#gen_file_copyname').click(function () {
        alert("Copiado para a área de transferência");
        let copyText = document.getElementById("gen_file_result");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
    });

})(jQuery);