// confirm_email handler
$("#confirm_email").submit(function() {
    // add CSRF token
    $('#submit').before('<input type="hidden" name="ci_csrf_token" value="'+$.cookie("ci_csrf_token")+'" />');
    $.post("/auth/confirm_email", $('#confirm_email').serialize(), function(data) {
            $("#confirm_error").html(data.confirm);
            if (data.form_message) {
                $('#confirm_email').html(data.form_message);
            }
            if (data.redirect_to) {
                window.location = data.redirect_to;
            }
        },'json'
    );
    return false;
});
