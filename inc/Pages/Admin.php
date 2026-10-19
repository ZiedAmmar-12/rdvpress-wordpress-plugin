<?php

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;

class Admin extends BaseController
{
	public $settings;

	public $callbacks;

	public $pages = array();

	public $subpages = array();
	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->setPages();

		$this->setSubpages();

		$this->setSettings();
		$this->setSections();
		$this->setFields();

		$this->settings->addPages($this->pages)->withSubPage('Réglages')->addSubPages($this->subpages)->register();
	}



	public function setPages()
	{
		$this->pages = array(
			array(
				'page_title' => 'rdvpress Plugin',
				'menu_title' => 'RDV Press',
				'capability' => 'manage_options',
				'menu_slug' => 'rdvpress_plugin',
				'callback' => array($this->callbacks, 'adminReglages'),
				'icon_url' => 'dashicons-dashboard',
				'position' => 110
			)
		);
	}

	public function setSubpages()
	{
		$this->subpages = array(

			/*array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Categories',
				'menu_title' => 'Categories',
				'capability' => 'manage_options',
				'menu_slug' => 'rdvpress_categories',
				'callback' => array($this->callbacks, 'adminCategories')
			),*/

			
			
			array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Tous les services',
				'menu_title' => 'Tous les services',
				'capability' => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=rdvpress_services',
				'callback' => null
			),
			/*array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Ajouter un service',
				'menu_title' => 'Ajouter un service',
				'capability' => 'manage_options',
				'menu_slug'   => 'post-new.php?post_type=rdvpress_services',
				'callback' => null
			),*/
			array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Catégories service',
				'menu_title' => 'Catégories service',
				'capability' => 'manage_options',
				'menu_slug'   => 'edit-tags.php?taxonomy=Catégories&post_type=rdvpress_services',
				'callback' => null,
			),
			array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Tous les rendez-vous',
				'menu_title' => 'Tous les rendez-vous',
				'capability' => 'manage_options',
				'menu_slug'   => 'edit.php?post_type=rdvpress_rendez_vous',
				'callback' => null
			),
			/*array(
				'parent_slug' => 'rdvpress_plugin',
				'page_title' => 'Ajouter un rendez-vous',
				'menu_title' => 'Ajouter un rendez-vous',
				'capability' => 'manage_options',
				'menu_slug'   => 'post-new.php?post_type=rdvpress_rendez_vous',
				'callback' => null
			),*/
			
			
			

		);



	}

	public function setSettings()
	{
		$args = array(
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'type_rdv',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'couleur'
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'email_rdvinfo',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'telephone_rdvinfo',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'rdvnotif',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'email_rdvnotif',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			),
			array(
				'option_group' => 'rdvpress_options_group',
				'option_name' => 'email_rdvreply',
				'callback' => array($this->callbacks, 'rdvpressOptionsGroup')
			)
		);

		$this->settings->setSettings($args);
	}

	public function setSections()
	{
		$args = array(
			array(
				'id' => 'rdvpress_admin_index',
				'title' => 'Réglages',
				'callback' => array($this->callbacks, 'rdvpressAdminSection'),
				'page' => 'rdvpress_plugin'
			)
		);

		$this->settings->setSections($args);
	}

	public function setFields()
	{
		$args = array(
			array(
				'id' => 'type_rdv',
				'title' => 'Type de rendez-vous',
				'callback' => array($this->callbacks, 'rdvpressTypeRDV'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'type_rdv',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'couleur',
				'title' => 'Choix de couleur',
				'callback' => array($this->callbacks, 'rdvpressColor'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'couleur',
					'class' => 'example-class'
				)
				),
			array(
				'id' => 'email_rdvinfo',
				'title' => 'Email info',
				'callback' => array($this->callbacks, 'rdvpressEmailinfo'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'email',
					'class' => 'example-class'
				)
				),

			array(
				'id' => 'telephone_rdvinfo',
				'title' => 'Telephone info',
				'callback' => array($this->callbacks, 'rdvpressTelephoneinfo'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'telephone',
					'class' => 'example-class'
				)
			),array(
				'id' => 'rdvnotif',
				'title' => 'Notification',
				'callback' => array($this->callbacks, 'rdvpressnotif'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'rdvnotif',
					'class' => 'example-class'
				)
			),array(
				'id' => 'email_rdvnotif',
				'title' => 'Email notification',
				'callback' => array($this->callbacks, 'rdvpressEmailnotif'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'email',
					'class' => 'example-class'
				)
			),array(
				'id' => 'email_rdvreply',
				'title' => 'Email reply',
				'callback' => array($this->callbacks, 'rdvpressEmailreply'),
				'page' => 'rdvpress_plugin',
				'section' => 'rdvpress_admin_index',
				'args' => array(
					'label_for' => 'email',
					'class' => 'example-class'
				)
			),
			
		);

		$this->settings->setFields($args);
	}


	
}
