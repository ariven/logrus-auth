<p>Hi <?php echo $name; ?>,</p>

<p>You, or someone claiming to be you, has requested a password reset request at <?php echo $base_url; ?>.</p>

<p>If this was you, simply click on the following link (or copy it to your browser URL box) and reset your password.</p>

<p>Reset Link:  <a href="<?php echo $base_url; ?>auth/password_reset/<?php echo $reset_code; ?>"><?php echo $base_url; ?>/auth/password_reset/<?php echo $reset_code; ?></a></p>

<p>If this wasn't you, then you can simply just ignore this email, and the password reset link will expire soon.</p>