<?php
/**
* Plugin Name: ConverteFácil: Pedidos
* Plugin URI: https://convertefacil.com.br/
* Text Domain: cf-pedidos
* Description: Transforma a Loja ConverteFácil em uma plataforma de pedidos online.
* Version: 1.0
* Author: Ingo Stramm
* Author URI: https://convertefacil.com.br/
* License: Parte integrante do sistema ConverteFácil, não pode ser comercializado separadamente.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'cf-pedidos-core.php';

// Executa as funções depois que o WP carregou mas antes dos Headers
// Garante que as funções sobreponham o Tema
add_action( 'init', 'cf_pedidos_override_theme' );

function cf_pedidos_override_theme() {

	// Remove os custom templates do Flatsome que não existem no Woocommerce
	remove_action('woocommerce_cart_actions', 'flatsome_continue_shopping', 10);

}

// Adiciona os novos custom templates do Flatsome que não existem no Woocommerce

add_action('woocommerce_cart_actions', 'cf_pedidos_continue_shopping', 10);

function cf_pedidos_continue_shopping() {
	include 'woocommerce/cart/continue-shopping.php';
}


// Executa as funções depois que os plugins são carregados
// Garante que as funções sobreponham o Woocommerce
add_action('plugins_loaded','wc_pedidos_override_plugins');

function wc_pedidos_override_plugins() {

	// Esconde os preços
	add_action('after_setup_theme','activate_filter');

	function activate_filter(){
		add_filter('woocommerce_get_price_html', 'hide_prices');
	}

	function hide_prices($price){
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}
	         
	// Troca o botão de comprar pelo botão customizado 
	add_filter( 'woocommerce_loop_add_to_cart_link', 'filter_woocommerce_loop_add_to_cart_link', 10, 2 ); 

	function filter_woocommerce_loop_add_to_cart_link( $array, $int ) {
		global $product;
		$post_id = $int->id;
		$url = get_permalink( $post_id );
		$btn = sprintf( '<div class="add-to-cart-button"><a href="%s" rel="nofollow" data-product_id="%s" class="%s %s product_type_%s button %s is-%s mb-0 is-%s">%s</a></div>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( $product->get_id() ),
            esc_attr( $product->is_type( 'variable' ) ? '' : 'ajax_add_to_cart'),
            $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
            esc_attr( $product->get_type() ),
            esc_attr( 'primary' ), // Button color
            esc_attr( get_theme_mod('add_to_cart_style', 'outline') ), // Button style
            esc_attr( 'small' ), // Button size
            esc_html( __( 'Solicitar orçamento', 'cf-pedidos' ) )
        );
	    return $btn; 
	};
	         
	// Troca o texto do botão Comprar do single page product
	add_filter( 'woocommerce_product_single_add_to_cart_text', 'filter_woocommerce_product_single_add_to_cart_text', 10, 2 ); 

	function filter_woocommerce_product_single_add_to_cart_text( $var, $instance ) { 
	    $var = __( 'Solicitar orçamento', 'cf-pedidos' );
	    return $var; 
	}; 

	// Esconde o preço na Minha Conta
	add_filter( 'woocommerce_account_orders_columns', 'filter_woocommerce_account_orders_columns', 10, 1 ); 

	function filter_woocommerce_account_orders_columns( $array ) { 
	    unset($array["order-total"]);
	    unset($array["order-status"]);
	    return $array; 
	}; 

	// Remove o filtro e ordenação na listagem de produtos
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

	// Remove Subtotal do Carrinho no topo
	add_filter( 'woocommerce_cart_subtotal', 'filter_woocommerce_cart_item_subtotal', 10, 3 ); 

	function filter_woocommerce_cart_item_subtotal( $wc, $cart_item, $cart_item_key ) { 
	    // make filter magic happen here... 
	    // return '<span class="shop-page-header-cart-hide--"></span>';
	    return ''; 
	}

	// Remove preços do mini cart

	add_filter( 'woocommerce_widget_cart_item_quantity', 'edita_mini_cart', 10, 3 );

	function edita_mini_cart( $html_output, $cart_item, $cart_item_key ) {
		$html_output = '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>';
		return $html_output; 
	}

	// Remove preços do cart-sidebar

	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
	remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );

	add_action( 'woocommerce_cart_collaterals', 'btn_checkout' );

	function btn_checkout() {
		do_action( 'woocommerce_proceed_to_checkout' );
	}
	         
	// Remove preços do checkout-sidebar
	add_filter( 'woocommerce_cart_item_subtotal', 'edita_checkout_sidebar', 10, 3 ); 

	function edita_checkout_sidebar( $wc, $cart_item, $cart_item_key ) { 
	    // make filter magic happen here... 
	    return null; 
	};

	// Tentativa de trocar o "Finalizar compra" do mini-cart, não funcionou
	remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );
	add_action( 'woocommerce_widget_shopping_cart_buttons', 'cf_pedidos_widget_shopping_cart_proceed_to_checkout', 20 );

	function cf_pedidos_widget_shopping_cart_proceed_to_checkout() {
		echo '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button checkout wc-forward">' . esc_html__( 'Finalizar orçamento', 'cf-pedidos' ) . '</a>';
	}

	// Troca o "Finalizar compra" da página de checkout
	add_filter( 'woocommerce_order_button_html', 'cf_pedidos_btn_checkout' );

	function cf_pedidos_btn_checkout() {
		$order_button_text = __( 'Finalizar orçamento', 'cf-pedidos' );
		return '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" />';
	}

	add_filter('woocommerce_email_subject_new_order', 'altera_assunto_email_novo_pedido', 1, 2);

	function altera_assunto_email_novo_pedido( $subject, $order ) {
		global $woocommerce;

		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$subject = sprintf( '[%s] Nova Solicitação de Orçamento (# %s) de %s %s', $blogname, $order->id, $order->billing_first_name, $order->billing_last_name );

		return $subject;
	}

}

// CSS
// Escondendo os preços que não possuem action/filters
add_action( 'wp_head', 'cf_pedidos_style' );

function cf_pedidos_style() {
	?>
	<style>
		.woocommerce-mini-cart__total.total,
		.product-price,
		.product-subtotal,
		.shop_table.woocommerce-checkout-review-order-table .product-total,
		.shop_table.woocommerce-checkout-review-order-table tfoot,
		.shop_table .product-total,
		.shop_table tfoot,
		.wc_payment_method 
		{
			display: none !important;
		}
	</style>
	<?php
}

// JS
// Alterando via script as strings que são manipuladas pelos scripts do tema
add_action( 'wp_footer', 'cf_pedidos_script' );

function cf_pedidos_script() {
	?>
	<script>
		(function($) {
			$(window).load(function(){
				$( '.widget_shopping_cart .button.checkout.wc-forward' ).text( '<?php echo __( 'Finalizar orçamento!', 'cf-pedidos' ); ?>' );
			}); // $(window).load
			$(document).ready(function(){
				$( '#order_review_heading' ).text( '<?php echo __( 'Seu orçamento', 'cf-pedidos' ); ?>' );				
			}); // $(document).ready
		})( jQuery );
	</script>
	<?php
}