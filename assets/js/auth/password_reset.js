// password_reset handler
$("#password_reset").submit(function() {
    // add CSRF token
    $('#submit').before('<input type="hidden" name="ci_csrf_token" value="'+$.cookie("ci_csrf_token")+'" />');
    $.post("/auth/password_reset", $('#password_reset').serialize(), function(data) {
            $("#password_error").html(data.password);
            $("#confirm_error").html(data.confirm);
            if (data.form_message) {
                $('#password_reset').html(data.form_message);
            }
            if (data.redirect_to) {
                window.location = data.redirect_to;
            }
        },'json'
    );
    return false;
});
