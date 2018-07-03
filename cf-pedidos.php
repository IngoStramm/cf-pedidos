<?php
/**
* Plugin Name: ConverteFácil: Pedidos
* Plugin URI: https://convertefacil.com.br/
* Text Domain: cf-pedidos
* Description: Transforma a Loja ConverteFácil em uma plataforma de pedidos online.
* Version: 1.2
* Author: Ingo Stramm
* Author URI: https://convertefacil.com.br/
* License: Parte integrante do sistema ConverteFácil, não pode ser comercializado separadamente.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once 'required_plugins/required_plugins.php';
require_once 'cf-pedidos-core.php';
require_once 'cf-pedidos-settings.php';
require_once 'cf-pedidos-product.php';
require_once 'cf-pedidos-functions.php';