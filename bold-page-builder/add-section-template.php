<?php

global $wp_query;
$wp_query->is_singular = true;

if ( ! function_exists( 'bt_bb_noindex_nofollow' ) ) {
	function bt_bb_noindex_nofollow( $array ) {
		$array['noindex'] = true;
		$array['nofollow'] = true;
		return $array;
	}
}
add_filter( 'wp_robots', 'bt_bb_noindex_nofollow' );
?><head><?php
wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open();

$sections = @file_get_contents( get_template_directory() . '/basic-sections.txt' );

if ( ! $sections ) {
	$sections = @file_get_contents( get_stylesheet_directory() . '/basic-sections.txt' );
}

if ( ! $sections ) {
	$sections = file_get_contents( plugin_dir_path( __FILE__ ) . 'basic-sections.txt' );
}

$sections = preg_replace( '/\[\/bt_bb_section\]\s*\[bt_bb_section/', '[/bt_bb_section][bt_bb_section', $sections );

$sections_arr = explode( '[/bt_bb_section][bt_bb_section', $sections );

$i = 0;
$c = count( $sections_arr );
$sections_arr1 = array();
foreach( $sections_arr as $section ) {
	if ( $i > 0 ) {
		$section = '[bt_bb_section' . $section;
	}
	if ( $i < $c - 1 ) {
		$section = $section . '[/bt_bb_section]';
	}
	$sections_arr1[] = $section;
	$i++;
}
foreach( $sections_arr1 as $section ) {
	if ( $i > 0 ) {
		$section = '[bt_bb_section' . $section;
	}
	if ( $i < $c - 1 ) {
		$section = $section . '[/bt_bb_section]';
	}
	$section = trim( preg_replace( '/\s+/', ' ', $section ) );
	BT_BB_FE::$sections_arr_search[] = str_replace( array( '"', '\'', '=' ), '', $section );
	$i++;
}

BT_BB_FE::$content = implode( '', $sections_arr1 );

add_filter( 'the_content', function( $existing_content ) {
	return BT_BB_FE::$content;
}, 0 );

add_action( 'wp_footer', function() {
	echo '<script>';
		echo 'window.bt_bb_fe_sections_search = ["';
		echo implode( '","', BT_BB_FE::$sections_arr_search );
		echo '"]';
	echo '</script>';
});

the_post();

the_content();

wp_footer();