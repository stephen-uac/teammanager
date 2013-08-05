<?php

$links = array_slice($links, 1, count($links)-1, true);

foreach($links as $title => $link){
	?>
		<a href="<?php echo $link; ?>"><h2><?php echo $title; ?></h2></a><br />
	<?php
}

?>