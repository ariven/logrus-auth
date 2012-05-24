
function init()
{
    var email = $('#email').val();
    check_email(email);
}
init();

function check_email(email_address)
{
    var token = $.cookie('ci_csrf_token');
    $.post('/auth/ajax_account_exists',{email: email_address, ci_csrf_token: token}, function(data) {
        if (data == 'true') {
            $('#account_exists').html('	<span class="label label-important">Warning</span> That account exists already.');
        }
    });
}

/**
 * check to see if user with this email exists
 */
$("#email").change(function() {
    var email = $('#email').val();
    check_email(email);
});
$("#email").keyup(function() {
    var email = $('#email').val();
    check_email(email);
});


// signup handler
$("#signup").submit(function() {
    // add CSRF token
    $('#submit').before('<input type="hidden" name="ci_csrf_token" value="'+$.cookie("ci_csrf_token")+'" />');
    $.post("/auth/signup", $('#signup').serialize(), function(data) {
            $("#email_error").html(data.email);
            $("#display_name_error").html(data.display_name);
            if (data.form_message) {
                $('#signup').html(data.form_message);
            }
            if (data.redirect_to) {
                window.location = data.redirect_to;
            }
        },'json'
    );
    return false;
});
