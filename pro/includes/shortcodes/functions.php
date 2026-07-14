<?php
/**
 * This file contains all functions related to shortcodes that couldn't fit anywhere else.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes
 */

defined( 'ABSPATH' ) or exit;

function support_smpro_menu() {
    global $submenu;
    $permalink = 'https://wpforchurch.com/my/clientarea.php';
    $submenu['edit.php?post_type=wpfc_sermon'][] = array( '<div id="sm-support-db">Support</div>', 'manage_options', $permalink );
}
add_action( 'admin_menu',  'support_smpro_menu' , 150 );

add_action( 'admin_footer', 'make_smpro_support_blank' );    
function make_smpro_support_blank()
{
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
       jQuery('#sm-support-db').parent().attr('target','_blank');
    });
    </script>
    <?php
}