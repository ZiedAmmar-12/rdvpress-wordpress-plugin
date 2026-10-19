<?php
namespace Inc\Base;
class Uninstall {
    public static function uninstall() {
    // Manually register the taxonomy 'Catégories'
        register_taxonomy('Catégories', 'rdvpress_services', array(
            'label' => 'Catégories',
            'rewrite' => array('slug' => 'categories'),
            'hierarchical' => true,
        ));
        // Remove custom taxonomies related
        $terms = get_terms(array(
            'taxonomy' => 'Catégories',
            'hide_empty' => false,
        ));
        if($terms != NULL){
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, 'Catégories');
            }
        }
        // Remove custom post types rdvpress_rendez_vous
        $args = array(
            'post_type' => 'rdvpress_rendez_vous',
            'numberposts' => -1,
            'post_status' => 'any'
        );
        $posts = get_posts($args);
        if($posts != NULL){
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }
        // Remove custom post types rdvpress_services
        $args = array(
            'post_type' => 'rdvpress_services',
            'numberposts' => -1,
            'post_status' => 'any'
        );
        $posts = get_posts($args);
        if($posts != NULL){
            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }
        // Remove custom options if needed
        delete_option('couleur');
        delete_option('type_rdv');
        delete_option('email_rdvinfo');
        delete_option('telephone_rdvinfo');
        delete_option('email_rdvnotif');
        delete_option('email_rdvreply');
    }
}