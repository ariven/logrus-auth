<?php 
	//$item_display = '<li><a href="%s">%s</a></li>';
	//$empty_display = '<li>%s</li>';
$item_display = '<a href="%s">%s</a>';
$empty_display = '%s';
	$holder = '';
	foreach ($items as $item) {
		if (strlen($item['link']) != 0) {
			$holder .= sprintf($item_display, $item['link'], $item['text']);
		} else {
			$holder .= sprintf($empty_display, $item['text']);
		}
	}
	if (!empty($holder)) { // warning on empty, not truly empty!
		echo $holder;
	}
?>