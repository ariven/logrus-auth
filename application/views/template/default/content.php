<div id="content">
<?php
	if (isset($content)) {
		if (is_array($content)) {
			foreach ($content as $item) {
				echo $item;
			}
		} else {
			echo $content;
		}
	}
?>
</div>