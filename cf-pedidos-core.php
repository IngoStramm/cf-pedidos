<?php

// Debug
function debug( $a ) {
	echo '<pre>';
	var_dump( $a );
	echo '</pre>';
}

// Pega o caminho absoluto do diretório do plugin
function cf_pedidos_plugin_path() {
	return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

// Prioriza os templates do plugin sobre o tema e o Woocommerce
// Ref: @link: https://www.skyverge.com/blog/override-woocommerce-template-file-within-a-plugin/
add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 10, 3 );

function myplugin_woocommerce_locate_template( $template, $template_name, $template_path ) {

	global $woocommerce;
	$_template = $template;
	$template_path = $woocommerce->template_url;
	$plugin_path  = cf_pedidos_plugin_path() . '/woocommerce/';

// Modification: Get the template from this plugin, if it exists
	if ( file_exists( $plugin_path . $template_name ) ) :
		$template = $plugin_path . $template_name;
	else :
// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
				)
			);
	endif;

// Use default template
	if ( ! $template )
		$template = $_template;

// Return what we found
	return $template;

}