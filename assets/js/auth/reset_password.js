// reset_password handler
$("#reset_password").submit(function () {
    // add CSRF token
    $('#submit').before('<input type="hidden" name="ci_csrf_token" value="' + $.cookie("ci_csrf_token") + '" />');
    $.post("/auth/reset_password", $('#reset_password').serialize(), function (data) {
            $("#email_error").html(data.email);
            if (data.form_message) {
                $('#reset_password').html(data.form_message);
            }
            if (data.redirect_to) {
                window.location = data.redirect_to;
            }
        }, 'json'
    );
    return false;
});
