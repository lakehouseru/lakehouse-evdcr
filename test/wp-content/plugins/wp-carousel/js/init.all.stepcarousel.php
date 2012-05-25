<?php	
	/* DEPRECATED */
	require('../../../../wp-blog-header.php');
	$config = unserialize(get_option('wp_carousel_config'));
	$total = count($config);
	$n = 0;
	foreach ($config as $key => $value) {
		$n++;
?>
stepcarousel.setup({
	galleryid: 'carousel_<?php echo $key; ?>', //id of carousel DIV
	beltclass: 'belt', //class of inner "belt" DIV containing all the panel DIVs
	panelclass: 'panel', //class of panel DIVs each holding content
	autostep: {enable:<?php if ($value['AUTOSLIDE_TIME'] != '0' && $value['AUTOSLIDE_POSTS'] != '0') { echo 'true, moveby:'.$value['AUTOSLIDE_POSTS'].', pause:'.$value['AUTOSLIDE_TIME']; } else { echo 'false'; } ?>},
	panelbehavior: {speed:500, wraparound:<?php if (isset($value['LOOP_MODE'])) { if ($value['LOOP_MODE'] == '0') { echo 'false'; } else { echo 'true'; } } ?>, persist:true},
	defaultbuttons: {enable: false, moveby: 1, leftnav: ['http://i34.tinypic.com/317e0s5.gif', -5, 80], rightnav: ['http://i38.tinypic.com/33o7di8.gif', -20, 80]},
	statusvars: ['statusA', 'statusB', 'statusC'], //register 3 variables that contain current panel (start), current panel (last), and total panels
	contenttype: ['inline'] //content setting ['inline'] or ['ajax', 'path_to_external_file']
})<?php if ($n != $total) echo '; '; } ?>