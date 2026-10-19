<div class="wrap">

	<?php settings_errors(); ?>
	<div id="tab-2" class="tab-pane">
		<form method="post" action="options.php">
			<?php
			settings_fields('rdvpress_plugin_services_settings');
			do_settings_sections('rdvpress_services');
			submit_button();
			?>
		</form>
	</div>
	<!--div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<h3>liste de categorie</h3>
			<!?php
			$options = get_option('rdvpress_plugin_services') ?: array();
			echo '<table class="services-table"><tr><th>Categorie</th><td>   </td><td>   </td><td>   </td><th class="text-center">Etat</th><td>   </td><td>   </td><td>   </td><th class="text-center">Actions</th></tr>';
			$public = isset($options['public']) ? "visible" : "invisible";
			echo "<tr><td>{$options['post_type']}</td><td>   </td><td>   </td><td>   </td><td class=\"text-center\">{$public}</td>    <td></td>    <td></td>    <td></td><td class=\"text-center\"><a href=\"#\">Editer</a></td></tr>";
			echo '</table>';
			?>
		</div>
	</!--div>
</div>