function onSubmit(token) {
    $(".alert").alert();
    var inputURL = $.trim($("#URL").val());
    var strRegex = '^((https|http|ftp|rtsp|mms)?://)'
        + '?(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?'
        + '(([0-9]{1,3}.){3}[0-9]{1,3}'
        + '|'
        + '([0-9a-z_!~*\'()-]+.)*'
        + '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].'
        + '[a-z]{2,6})'
        + '(:[0-9]{1,4})?'
        + '((/?)|'
        + '(/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+/?)$';
    var reg = new RegExp(strRegex);
    if (reg.test(inputURL)) {
        $.post("/API/api.php", {
            URL: inputURL,
            token: token
        },
            function (data, status) {
                var txt1 = '<div class="alert alert-success fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + status + '</strong></div>';
                $("#info").after(txt1);
                console.log(data);
            });
    }
    else {
        var txt1 = '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>' + 'Failed:Invalid input!' + '</strong></div>';
        $("#info").after(txt1);
    }
}
function clearAll() {
    $(".alert").alert('close');
}