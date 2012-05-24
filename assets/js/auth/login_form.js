// login handler
$("#login").submit(function() {
    // add CSRF token
    $('#submit').before('<input type="hidden" name="ci_csrf_token" value="'+$.cookie("ci_csrf_token")+'" />');
    $.post("/auth/login", $('#login').serialize(), function(data) {
            $("#email_error").html(data.email);
            $("#password_error").html(data.password);
            if (data.form_message) {
                $('#login').html(data.form_message);
            }
            if (data.redirect_to) {
                window.location = data.redirect_to;
            }
        },'json'
    );
    return false;
});
