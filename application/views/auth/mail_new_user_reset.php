<p>Hi <?php echo $name; ?>,</p>

<p>You, or someone claiming to be you, has requested an account at <?php echo $base_url; ?>.</p>

<p>If this was you, simply click on the following link (or copy it to your browser URL box) and confirm your account.</p>

<p>Reset Link:  <a href="<?php echo $base_url; ?>/auth/confirm_email_code/<?php echo $reset_code; ?>"><?php echo $base_url; ?>/auth/confirm_email_code/<?php echo $reset_code; ?></a></p>

<p>If this wasn't you, then you can simply just ignore this email.</p>