function defaultSubmit() {
    $(".alert").alert();
    var inputURL = encodeURI($.trim($("#URL").val()));
    var strRegex = '^((https|http)://)'
        + '+(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?'
        + '(([0-9]{1,3}.){3}[0-9]{1,3}'
        + '|'
        + '([0-9a-z_!~*\'()-]+.)*'
        + '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].'
        + '[a-z]{2,6})'
        + '(:[0-9]{1,4})?'
        + '((/?)|'
        + '(/[0-9a-zA-Z_!~*\'().;?:@&=+$,%#-]+)+/?)$';
    var reg = new RegExp(strRegex);
    if (reg.test(inputURL)) {
        $("#default-btn").attr("disabled", true);
        grecaptcha.ready(function () {
            grecaptcha.execute('6LclgSoaAAAAAEoDmmNxBurh9hdLoEKqni_iwO3k', {action: 'submit'}).then(function (token) {
                // Add your logic to submit to your backend server here.
                $.post("/API/api.php", {
                        URL: inputURL,
                        token: token
                    },
                    function (data, status) {
                        var txt1;
                        if (data.status == 200) {
                            txt1 = '<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>Success!</strong>  Got it: <strong class="url-return">https://ur1l.com/' + data.code + '</strong></div>';
                        } else {
                            txt1 = '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + data.code + '</strong></div>';
                        }
                        $("#info").after(txt1);
                    });
            });
        });
        setTimeout(function () {
            $("#default-btn").removeAttr("disabled")
        }, 3000);
    } else {
        var txt1 = '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + 'Failed:Invalid input!' + '</strong></div>';
        $("#info").after(txt1);
    }
}

function clearAll() {
    $(".alert").alert('close');
}
