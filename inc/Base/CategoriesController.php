<?php

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\CategoriesCallbacks;
use Inc\Api\Callbacks\AdminCallbacks;

/**
 * 
 */
class CategoriesController extends BaseController
{
    public $settings;

    public $callbacks;

    public $categories_callbacks;

    public $subpages = array();

    public $categories = array();

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->categories_callbacks = new CategoriesCallbacks();

        $this->setSubpages();

        $this->setSettings();

        $this->setSections();

        $this->setFields();

        $this->settings->addSubPages($this->subpages)->register();
        add_action('init', array($this, 'build_taxonomies'), 0);
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'rdvpress_plugin',
                'page_title' => 'categories',
                'menu_title' => 'categories Manager',
                'capability' => 'manage_options',
                'menu_slug' => 'rdvpress_categories',
                'callback' => array($this->callbacks, 'adminCategories')
            )
        );
    }



    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'rdvpress_plugin_categories_settings',
                'option_name' => 'rdvpress_plugin_categories',
                'callback' => array($this->categories_callbacks, 'categoriesSanitize')
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'rdvpress_categories_index',
                'title' => 'Categorie',
                'callback' => array($this->categories_callbacks, 'categoriesSectionManager'),
                'page' => 'rdvpress_categories'
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'post_type',
                'title' => 'nom de categorie',
                'callback' => array($this->categories_callbacks, 'textField'),
                'page' => 'rdvpress_categories',
                'section' => 'rdvpress_categories_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_categories',
                    'label_for' => 'post_type',
                    'placeholder' => 'eg. categorie',
                    'array' => 'taxonomy'
                ),
            ),

            array(
                'id' => 'public',
                'title' => 'Public',
                'callback' => array($this->categories_callbacks, 'checkboxField'),
                'page' => 'rdvpress_categories',
                'section' => 'rdvpress_categories_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_categories',
                    'label_for' => 'hierarchical',
                    'class' => 'ui-toggle',
                    'array' => 'taxonomy'
                )
            ),


        );
        $this->settings->setFields($args);
    }

    public function build_taxonomies()
    {
        $labels = array(
            'name'              => 'Catégories',
            'singular_name'     => 'Catégorie',
            'search_items'      => 'Search Catégories',
            'all_items'         => 'All Catégories',
            'parent_item'       => 'Parent Catégorie',
            'parent_item_colon' => 'Parent Catégorie:',
            'edit_item'         => 'Edit Catégorie',
            'update_item'       => 'Update Catégorie',
            'add_new_item'      => 'Add New Catégorie',
            'new_item_name'     => 'New Catégorie Name',
            'menu_name'         => 'Catégorie',
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'catégorie'),
        );



        register_taxonomy('Catégories', array('rdvpress_services'), $args);
    }
}
