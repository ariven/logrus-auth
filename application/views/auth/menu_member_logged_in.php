
<ul class="nav pull-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $display_name; ?> <b
			class="caret"></b></a>
		<ul class="dropdown-menu">
			<?php
			if (!$login_authority)
			{
				//legacy accounts can change password
				?>
				<li><a href="/auth/change_password"><i class="icon-wrench"> </i> Change Password</a></li>
				<?php
			}
			else
			{
				?>
				<li><a>Logged in via <?php echo $login_authority; ?></a></li>

				<?php
			}
?>
			<li><hr /></li>
			<li><a href="/auth/logout"><i class="icon-off"> </i> Log Out</a></li>
		</ul>
	</li>
</ul>