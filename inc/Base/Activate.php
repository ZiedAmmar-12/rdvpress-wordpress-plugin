<?php

namespace Inc\Base;

class Activate
{
	public static function activate()
	{
		flush_rewrite_rules();

		if (get_option('rdvpress_plugin')) {
			return;
		}

		$default = array();

		update_option('rdvpress_plugin', $default);
	}
}
