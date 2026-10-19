<?php 

namespace Inc\Base;

use Inc\Base\BaseController;

/**
* 
*/
class Enqueue extends BaseController
{
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}
	
	function enqueue() {
		// enqueue all our scripts
		wp_enqueue_style( 'mypluginstyle', $this->plugin_url . 'assets/mystyle.css' );
		wp_enqueue_script( 'mypluginscript', $this->plugin_url.'assets/myscript.js',array('jquery'),1,true);
		wp_localize_script('mypluginscript','postdata',
			array('ajax_url' => admin_url('admin-ajax.php'),
				'ajax_nonce' => wp_create_nonce('is_nonce_security_formrdvpress_admin'),
			)
		);
	}
}