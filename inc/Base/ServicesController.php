<?php

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\ServicesCallbacks;
use Inc\Api\Callbacks\AdminCallbacks;

/**
 * 
 */
class ServicesController extends BaseController
{
	public $settings;

	public $callbacks;

	public $services_callbacks;

	public $subpages = array();

	public $services = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->services_callbacks = new ServicesCallbacks();

		//$this->setSubpages();

		$this->setSettings();

		$this->setSections();

		$this->setFields();

		$this->settings->addSubPages($this->subpages)->register();
		add_action('init', array($this, 'registerServices'));
		add_action("add_meta_boxes", array($this, "add_meta_box_services"));
		add_action("save_post", array($this, "services_meta_box_save"));
	}

	public function setSubpages()
	{
		$this->subpages = array(
			array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'services',
				'menu_title' => 'services Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'rdvpress_services',
				'callback' => array($this->callbacks, 'adminServices')
			)
		);
	}



	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'rdvpress_plugin_services_settings',
				'option_name' => 'rdvpress_plugin_services',
				'callback' => array($this->services_callbacks, 'servicesSanitize')
			)
		);

		$this->settings->setSettings($args);
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'rdvpress_services_index',
				'title' => 'Service',
				'callback' => array($this->services_callbacks, 'servicesSectionManager'),
				'page' => 'rdvpress_services'
			)
		);

		$this->settings->setSections($args);
	}

	public function setFields()
	{
		$args = array(
			array(
				'id' => 'post_type',
				'title' => 'nom de services',
				'callback' => array($this->services_callbacks, 'textField'),
				'page' => 'rdvpress_services',
				'section' => 'rdvpress_services_index',
				'args' => array(
					'option_name' => 'rdvpress_plugin_services',
					'label_for' => 'post_type',
					'placeholder' => 'eg. service',
					'array' => 'post_type'
				),
			),

			array(
				'id' => 'description',
				'title' => 'description',
				'callback' => array($this->services_callbacks, 'textField'),
				'page' => 'rdvpress_services',
				'section' => 'rdvpress_services_index',
				'args' => array(
					'option_name' => 'rdvpress_plugin_services',
					'label_for' => 'post_type',
					'placeholder' => 'eg. Service...',
					'array' => 'post_type'
				),
			),
			array(
				'id' => 'durée',
				'title' => 'durée',
				'callback' => array($this->services_callbacks, 'textField'),
				'page' => 'rdvpress_services',
				'section' => 'rdvpress_services_index',
				'args' => array(
					'option_name' => 'rdvpress_plugin_services',
					'label_for' => 'post_type',
					'placeholder' => 'eg. date',
					'array' => 'post_type'
				),
			),

			array(
				'id' => 'public',
				'title' => 'Public',
				'callback' => array($this->services_callbacks, 'checkboxField'),
				'page' => 'rdvpress_services',
				'section' => 'rdvpress_services_index',
				'args' => array(
					'option_name' => 'rdvpress_plugin_services',
					'label_for' => 'public',
					'class' => 'ui-toggle',
					'array' => 'post_type'
				)
			),
		);
		$this->settings->setFields($args);
	}

	public function registerServices()
	{
		register_post_type(
			'rdvpress_services',
			array(
				'labels' => array(
					'name'                  => 'services',
					'singular_name'         => 'service',
					'all_items'             => 'Tous les services ',
					'add_new_item'          => 'Ajouter un nouveau service',
					'add_new'               => 'Ajouter un nouveau',
					'new_item'              => 'Nouveau ',
					'edit_item'             => 'Editer ',
					'update_item'           => 'Modifier ',
					'view_item'             => 'Voir ',
					'view_items'            => 'Voir les services ',
					'search_items'          => 'Rechercher ',

				),
				'supports'                  => array('title', 'Categories'),
				'taxonomies'                => array('Categories'),
				'public'                    => true,
				'has_archive'               => true,
				'show_in_menu' => 'edit.php?post_type=rdvpress_services',
				'menu_icon'					=> 'dashicons-store',
				'menu_position'             => 110,
				'show_in_admin_bar'         => true,
				'show_in_nav_menus'         => true,
				'capability_type'           => 'post'
			)
		);

	}

	public function add_meta_box_services()
	{
		$service = get_option('type_rdv');
		if ($service === 'garage') {
			add_meta_box('post_type',  'Service garage', array($this, "ajouter_service_metabox"), 'rdvpress_services');
		} elseif ($service === 'avocat') {
			add_meta_box('post_type',  'Service avocat', array($this, "ajouter_service_metabox"), 'rdvpress_services');
		} else {
			add_meta_box('post_type',  'Service medcin', array($this, "ajouter_service_metabox"), 'rdvpress_services');
		}
	}

	public function ajouter_service_metabox()
	{

		global $post;
		$value = get_post_meta($post->ID, 'nom_service', true);
		$valuedes = get_post_meta($post->ID, 'description_service', true);
		$valuedate = get_post_meta($post->ID, 'date', true);
		$valueduree = get_post_meta($post->ID, 'duree', true);
		$valueprix = get_post_meta($post->ID, 'prix', true);
		$valueetat = get_post_meta($post->ID, 'etat', true);


?>

		<table class="table ">
			<tr>
				<td><b>Nom de service :</b></td>
				<td><input type="text" name="nom_service" id="nom_service" value="<?= $value ?>" placeholder="nom de service..."></td>
			</tr>
			<tr>
				<td><b>Description :</b></td>
				<td><textarea name="description_service" id="description_service" rows="3" cols="23"><?= $valuedes ?></textarea></td>
			</tr>
			<tr>
				<td><b>Date :</b></td>
				<td><input type="date" name="date" id="date" value="<?= $valuedate ?>"></td>
			</tr>
			<tr>
				<td><b>Durée :</b></td>
				<td><input type="time" name="duree" id="duree" value="<?= $valueduree ?>"></td>
			</tr>
			<tr>
				<td><b>Prix :</b></td>
				<td><input type="number" name="prix" id="prix" value="<?= $valueprix ?>" placeholder="0.00"></td>
			</tr>
			<tr>
				<td><b>Etat :</b></td>
				<td><input type="checkbox" name="etat" id="etat" value="visible" <?php if ($valueetat) {
																						echo 'checked';
																					} else {
																						echo '';
																					} ?>><label for="visible"> Visible</label>
				</td>
			</tr>
		</table>
<?php
	}

	public function services_meta_box_save($post_id)
	{	


		if(isset($_POST['nom_service'])){
			$value = $_POST['nom_service'];
			update_post_meta($post_id, 'nom_service', $value);
		}else{
			add_post_meta($post_id, 'nom_service', "");
		}

		if(isset($_POST['description_service'])){
			$value = $_POST['description_service'];
			update_post_meta($post_id, 'description_service', $value);
		}else{
			add_post_meta($post_id, 'description_service', "");
		}


		if(isset($_POST['duree'])){
			$value = $_POST['duree'];
			update_post_meta($post_id, 'duree', $value);
		}else{
			add_post_meta($post_id, 'duree', "");
		}


		if(isset($_POST['prix'])){
			$value = $_POST['prix'];
			update_post_meta($post_id, 'prix', $value);
		}else{
			add_post_meta($post_id, 'prix', "");
		}


		if(isset($_POST['etat'])){
			$value = $_POST['etat'];
			update_post_meta($post_id, 'etat', $value);
		}else{
			add_post_meta($post_id, 'etat', "");
		}

	}
}
