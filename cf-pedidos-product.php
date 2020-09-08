<?php

add_action( 'cmb2_admin_init', 'yourprefix_register_demo_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function yourprefix_register_demo_metabox() {
	$prefix = 'product_settings_';

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$cmb = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Exibir preço?', 'cmb2' ),
		'object_types'  => array( 'product' ), // Post type
		// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
		'context'    => 'side',
		// 'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
		// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
	) );

	$cmb->add_field( array(
		'name' => esc_html__( 'Inverter a lógica de exibição de preço do produto.', 'cmb2' ),
		'desc' => esc_html__('Se ativada, esta opção irá inverter a lógica de exibição de preço especificamente deste produto. Por ex: se nas "Opções de Pedidos" estiver selecionado para "Exibir preços", ao ativar esta opção, os preços deste produto não serão exibidos.', 'cmb2' ),
		'id'   => $prefix . 'reverse_logic',
		'type' => 'checkbox',
	) );
}