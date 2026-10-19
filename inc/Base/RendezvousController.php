<?php

namespace Inc\Base;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\RendezvousCallbacks;
use Inc\Api\Callbacks\AdminCallbacks;

/**
 * 
 */
class RendezvousController extends BaseController
{
    public $settings;

    public $callbacks;

    public $Rendez_vous_callbacks;

    public $subpages = array();

    public $rendez_vous  = array();

    public function __construct() {
        add_action( 'wp_ajax_service_cat_option_admin', array($this,'get_listservice_cat_option_admin') );
    }
   
    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->Rendez_vous_callbacks = new RendezvousCallbacks();

        $this->setSubpages();

        $this->setSettings();

        $this->setSections();

        //$this->setFields();

        $this->settings->addSubPages($this->subpages)->register();
        add_action('init', array($this, 'registerRendezvous'));
        add_action("add_meta_boxes", array($this, "add_meta_box_rendezvous"));
        add_action("save_post", array($this, "rdv_meta_box_save"));

    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'rdvpress_plugin',
                'page_title' => 'rendez_vous ',
                'menu_title' => 'rendez_vous_Manager',
                'capability' => 'manage_options',
                'menu_slug' => 'rdvpress_rendez_vous ',
                //'callback' => array($this->callbacks, 'adminRDV ')
            )
        );
    }



    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'rdvpress_plugin_rendez_vous_settings',
                'option_name' => 'rdvpress_plugin_rendez_vous ',
                'callback' => array($this->Rendez_vous_callbacks, 'rendezvousSanitize')
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'rdvpress_rendez_vous_index',
                'title' => 'rendez vous',
                'callback' => array($this->Rendez_vous_callbacks, 'rendezvousSectionManager'),
                'page' => 'rdvpress_rendez_vous '
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array(
            array(
                'id' => 'post_type',
                'title' => 'nom de rendez vous ',
                'callback' => array($this->Rendez_vous_callbacks, 'textField'),
                'page' => 'rdvpress_rendez_vous ',
                'section' => 'rdvpress_rendez_vous_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_rendez_vous ',
                    'label_for' => 'public',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                ),
            ),

            array(
                'id' => 'description',
                'title' => 'description',
                'callback' => array($this->Rendez_vous_callbacks, 'textField'),
                'page' => 'rdvpress_rendez_vous ',
                'section' => 'rdvpress_rendez_vous_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_rendez_vous ',
                    'label_for' => 'public',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                ),
            ),
            array(
                'id' => 'durée',
                'title' => 'durée',
                'callback' => array($this->Rendez_vous_callbacks, 'textField'),
                'page' => 'rdvpress_rendez_vous ',
                'section' => 'rdvpress_rendez_vous_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_rendez_vous ',
                    'label_for' => 'public',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                ),
            ),

            array(
                'id' => 'public',
                'title' => 'Public',
                'callback' => array($this->Rendez_vous_callbacks, 'checkboxField'),
                'page' => 'rdvpress_rendez_vous ',
                'section' => 'rdvpress_rendez_vous_index',
                'args' => array(
                    'option_name' => 'rdvpress_plugin_rendez_vous ',
                    'label_for' => 'public',
                    'class' => 'ui-toggle',
                    'array' => 'post_type'
                ),
            ),
        );
        $this->settings->setFields($args);
    }


    public function registerRendezvous()
    {
        register_post_type(
            'rdvpress_rendez_vous',
            array(
                'labels' => array(
                    'name'                  => 'rendez_vous',
                    'singular_name'         => 'rendez_vous',
                    'all_items'             => 'Tous les rendez_vous ',
                    'add_new_item'          => 'Ajouter un nouveau rendez_vous',
                    'add_new'               => 'Ajouter un nouveau',
                    'new_item'              => 'Nouveau ',
                    'edit_item'             => 'Editer ',
                    'update_item'           => 'Modifier ',
                    'view_item'             => 'Voir ',
                    'view_items'            => 'Voir les rendez_vous ',
                    'search_items'          => 'Rechercher ',

                ),
                'supports'                  => array('title'),
                'public'                    => true,
                'has_archive'               => true,
                'show_in_menu'              =>'edit.php?post_type=rdvpress_rendez_vous',
                'menu_position'             => 80,
                'show_in_admin_bar'         => true,
                'show_in_nav_menus'         => true,
                'capability_type'           => 'post'
            )
        );
    }
    public function add_meta_box_rendezvous()
    {
        add_meta_box('post_type',  'Détails Rendez-vous', array($this, "ajouter_rendezvous_metabox"), 'rdvpress_rendez_vous');
                
    }

    



function get_listservice_cat_option_admin() {

	// If nonce verification fails die.
	if ( ! wp_verify_nonce( $_POST['security'], 'is_nonce_security_formrdvpress_admin' ) ) {
		wp_die();
	}

	$rdv_categorieid = $_POST['rdv_categorieid'];

   
    $args = array(
            'post_type' => 'rdvpress_services',              
            'orderby' => 'post_name',               
            'order' => 'ASC',
            'post_status' => 'publish',
		    'tax_query' => array(
		            array(
		                'taxonomy' => 'Catégories',
		                'field' => 'term_id',
		                'terms'    => $rdv_categorieid
		            ),
		       ),
   );          

    $bruteposts = new \WP_Query($args);
    $postservices = $bruteposts->get_posts();
    
    foreach( $postservices as $post ) { 
       	$service_title = $post->post_title;
		$service_id = $post->ID;
		$content .= '<option value="' . $service_id . '">
					' . $service_title .
					'</option>';
	}

	wp_send_json_success( array(
		'my_data' => $content,  // Always pass your data here that you want to access in js file.
		'data_recieved_from_js' => $_POST,  // $_POST will contain the array of data object passed in js file second parameter. like action,name etc
	) );
}
    


public function ajouter_rendezvous_metabox()
    {

        global $post;
        $categories = get_categories(array('taxonomy' => 'Catégories','hide_empty' => false,'parent' => '0'));

            $rdv_categorievalue = get_post_meta($post->ID, 'rdv_categorie',true);
            $rdv_servicevalue = get_post_meta($post->ID, 'rdv_service',true);
            $rdv_datevalue = get_post_meta($post->ID, 'rdv_date',true);
            $rdv_timevalue = get_post_meta($post->ID, 'rdv_time',true);
            $rdv_immatriculationvalue = get_post_meta($post->ID, 'rdv_immatriculation',true);
            $rdv_genreclientvalue = get_post_meta($post->ID, 'rdv_genreclient',true);
            $rdv_nomclientvalue = get_post_meta($post->ID, 'rdv_nomclient',true);
            $rdv_emailclientvalue = get_post_meta($post->ID, 'rdv_emailclient',true);
            $rdv_telclientvalue = get_post_meta($post->ID, 'rdv_telclient',true);
            $rdv_commentaireclientvalue = get_post_meta($post->ID, 'rdv_commentaireclient',true);
            $rdv_etatvalue = get_post_meta($post->ID, 'rdv_etat',true);

            if($rdv_categorievalue != null){
                    $args = array(
                            'post_type' => 'rdvpress_services',              
                            'orderby' => 'post_name',               
                            'order' => 'ASC',
                            'post_status' => 'publish',
                            'tax_query' => array(
                                    array(
                                        'taxonomy' => 'Catégories',
                                        'field' => 'term_id',
                                        'terms'    => $rdv_categorievalue
                                    ),
                               ),
                    );          

                    $bruteposts = new \WP_Query($args);
                    $postservices = $bruteposts->get_posts();
                
            }
?>

<fieldset class="fieldsetadmin-rdv">
<legend>Service</legend>
        <table class="formadmin-tablerdv">
            <tr>
                <td><label for="rdv_categorie">Catégorie de service <span>*</span>:</label></td>
                <td><select id="rdv_categorie" name="rdv_categorie" required>
                    <option value="">--choisir--</option>
                    <?php 
                    if($categories != null){
                        foreach ($categories as $category) { ?>
                            <option value="<?php echo $category->term_id; ?>" <?php if($category->term_id == $rdv_categorievalue){ echo "selected";} ?>> <?php echo $category->name; ?></option>
                    <?php 
                        } 
                    } 
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="rdv_service">Service demandé <span>*</span>:</label></td>
                <td><select id="rdv_service" name="rdv_service" required>
                    <option value="" <?php if($rdv_servicevalue == ''){ echo "selected";} ?>>--choisir--</option>
                    <?php if($rdv_categorievalue != null){
                        foreach( $postservices as $post ) { 
                        $service_title = $post->post_title;
                        $service_id = $post->ID;
                    ?>
                        <option value="<?php echo $service_id; ?>" <?php if($service_id == $rdv_servicevalue){ echo "selected" ;} ?> > <?php echo $service_title; ?></option>
                    <?php
                         }
                    } ?>
                    </select>
                </td>
            </tr>
        </table>
</fieldset>


<fieldset class="fieldsetadmin-rdv">
<legend>Date et Heure</legend>
        <table class="formadmin-tablerdv">
            <tr>
                <td><label for="rdv_date">Date <span>*</span>:</label></td>
                <td>
                    <input type="date" id="rdv_date" name="rdv_date" value="<?php echo $rdv_datevalue;?>" placeholder="E-mail" required>
                </td>
            </tr>
            <tr>
                <td><label for="rdv_time">Heure <span>*</span>:</label></td>
                <td>
                <select name="rdv_time" id="rdv_time">
    <option <?php if($rdv_timevalue == '09:00 - 10:00') echo 'selected';?> value="09:00 - 10:00">09:00 - 10:00</option>
    <option <?php if($rdv_timevalue == '09:30 - 10:30') echo 'selected';?> value="09:30 - 10:30">09:30 - 10:30</option>
    <option <?php if($rdv_timevalue == '10:00 - 11:00') echo 'selected';?> value="10:00 - 11:00">10:00 - 11:00</option>
    <option <?php if($rdv_timevalue == '10:30 - 11:30') echo 'selected';?> value="10:30 - 11:30">10:30 - 11:30</option>
    <option <?php if($rdv_timevalue == '11:00 - 12:00') echo 'selected';?> value="11:00 - 12:00">11:00 - 12:00</option>
    <option <?php if($rdv_timevalue == '11:30 - 12:30') echo 'selected';?> value="11:30 - 12:30">11:30 - 12:30</option>
    <option <?php if($rdv_timevalue == '12:00 - 13:00') echo 'selected';?> value="12:00 - 13:00">12:00 - 13:00</option>
    <option <?php if($rdv_timevalue == '12:30 - 13:30') echo 'selected';?> value="12:30 - 13:30">12:30 - 13:30</option>
    <option <?php if($rdv_timevalue == '13:00 - 14:00') echo 'selected';?> value="13:00 - 14:00">13:00 - 14:00</option>
    <option <?php if($rdv_timevalue == '13:30 - 14:30') echo 'selected';?> value="13:30 - 14:30">13:30 - 14:30</option>
    <option <?php if($rdv_timevalue == '14:00 - 15:00') echo 'selected';?> value="14:00 - 15:00">14:00 - 15:00</option>
    <option <?php if($rdv_timevalue == '14:30 - 15:30') echo 'selected';?> value="14:30 - 15:30">14:30 - 15:30</option>
    <option <?php if($rdv_timevalue == '15:00 - 16:00') echo 'selected';?> value="15:00 - 16:00">15:00 - 16:00</option>
    <option <?php if($rdv_timevalue == '15:30 - 16:30') echo 'selected';?> value="15:30 - 16:30">15:30 - 16:30</option>
    <option <?php if($rdv_timevalue == '16:00 - 17:00') echo 'selected';?> value="16:00 - 17:00">16:00 - 17:00</option>
    <option <?php if($rdv_timevalue == '16:30 - 17:30') echo 'selected';?> value="16:30 - 17:30">16:30 - 17:30</option>
    <option <?php if($rdv_timevalue == '17:00 - 18:00') echo 'selected';?> value="17:00 - 18:00">17:00 - 18:00</option>
</select>


                </td>
            </tr>
        </table>
</fieldset>
<fieldset class="fieldsetadmin-rdv">
<legend>Plaque d'immatriculation</legend>
        <table class="formadmin-tablerdv">
            <tr>
                <td><label for="rdv_immatriculation">Plaque d'immatriculation <span>*</span>:</label></td>
                <td>
                    <input type="text" id="rdv_immatriculation" name="rdv_immatriculation" value="<?php echo $rdv_immatriculationvalue;?>" placeholder="AA-123-BB" required>
                </td>
            </tr>
        </table>
</fieldset>
<fieldset class="fieldsetadmin-rdv">
<legend> Client</legend>
        <table class="formadmin-tablerdv">
            <tr>
                <td><label for="rdv_genreclient">Genre :</label></td>
                <td><select id="rdv_genreclient" name="rdv_genreclient">
                    <option <?php if($rdv_genreclientvalue == 'Mr') echo 'selected';?> value="Mr">Mr</option>
                    <option <?php if($rdv_genreclientvalue == 'Mdme') echo 'selected';?> value="Mdme">Mdme</option>
                    <option <?php if($rdv_genreclientvalue == 'Mlle') echo 'selected';?> value="Mlle">Mlle</option>
                    <option <?php if($rdv_genreclientvalue == 'Société') echo 'selected';?> value="Société">Société</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="rdv_nomclient">Nom et prénom <span>*</span>:</label></td>
                <td><input type="text" id="rdv_nomclient" name="rdv_nomclient" value="<?php echo $rdv_nomclientvalue;?>" placeholder="Nom et prénom" required></td>
            </tr>
            <tr>
                <td><label for="rdv_emailclient">E-mail <span>*</span>:</label></td>
                <td><input type="email" id="rdv_emailclient" name="rdv_emailclient" value="<?php echo $rdv_emailclientvalue;?>" placeholder="E-mail" required></td>
            </tr>
            <tr>
                <td><label for="rdv_telclient">Téléphone <span>*</span>:</label></td>
                <td><input type="tel" id="rdv_telclient" name="rdv_telclient" value="<?php echo $rdv_telclientvalue;?>" placeholder="Téléphone" required></td>
            </tr>
            
            <tr>
                <td><label for="rdv_commentaireclient">Commentaire :</label></td>
                <td><textarea id="rdv_commentaireclient" name="rdv_commentaireclient" rows="3" cols="23" placeholder="Commentaire"><?php echo $rdv_commentaireclientvalue;?></textarea></td>

            </tr>
        </table>
</fieldset>
<fieldset class="fieldsetadmin-rdv">
<legend>Etat</legend>
        <table class="formadmin-tablerdv">
            <tr>
                <td><label for="rdv_etat">Etat <span>*</span>:</label></td>
                <td>
                    <label for="rdv_etat"><input type="radio" name="rdv_etat" value="enattente" <?php if($rdv_etatvalue == 'enattente'){ echo "checked";} ?>>En attente</label>
                    <label for="rdv_etat"><input type="radio" name="rdv_etat" value="valider" <?php if($rdv_etatvalue == 'valider'){ echo "checked";} ?>>Valider</label>
                    <label for="rdv_etat"><input type="radio" name="rdv_etat" value="annuler" <?php if($rdv_etatvalue == 'annuler'){ echo "checked";} ?>>Annuler</label>

                </td>
            </tr>
        </table>
</fieldset>

<?php
    }

    

    public function rdv_meta_box_save($post_id)
	{

        if(isset($_POST['rdv_categorie'])){
            $rdv_categorievalue = $_POST['rdv_categorie'];
            update_post_meta($post_id, 'rdv_categorie', $rdv_categorievalue);
        }else{
            add_post_meta($post_id, 'rdv_categorie', "");
        }
		
        if(isset($_POST['rdv_service'])){
            $rdv_servicevalue = $_POST['rdv_service'];
		    update_post_meta($post_id, 'rdv_service', $rdv_servicevalue);
        }else{
            add_post_meta($post_id, 'rdv_service', "");
        }

        if(isset($_POST['rdv_date'])){
            $rdv_datevalue = $_POST['rdv_date'];
		    update_post_meta($post_id, 'rdv_date', $rdv_datevalue);
        }else{
            add_post_meta($post_id, 'rdv_date', "");
        }

        if(isset($_POST['rdv_time'])){
            $rdv_timevalue = $_POST['rdv_time'];
		    update_post_meta($post_id, 'rdv_time', $rdv_timevalue);
        }else{
            add_post_meta($post_id, 'rdv_time', "");
        }

        if(isset($_POST['rdv_immatriculation'])){
            $rdv_immatriculationvalue = $_POST['rdv_immatriculation'];
		    update_post_meta($post_id, 'rdv_immatriculation', $rdv_immatriculationvalue);
        }else{
            add_post_meta($post_id, 'rdv_immatriculation', "");
        }

        if(isset($_POST['rdv_genreclient'])){
            $rdv_genreclientvalue = $_POST['rdv_genreclient'];
		    update_post_meta($post_id, 'rdv_genreclient', $rdv_genreclientvalue);
        }else{
            add_post_meta($post_id, 'rdv_genreclient', "");
        }

        if(isset($_POST['rdv_nomclient'])){
		    $rdv_nomclientvalue = $_POST['rdv_nomclient'];
		    update_post_meta($post_id, 'rdv_nomclient', $rdv_nomclientvalue);
        }else{
            add_post_meta($post_id, 'rdv_nomclient', "");
        }

        if(isset($_POST['rdv_emailclient'])){
		    $rdv_emailclientvalue = $_POST['rdv_emailclient'];
		    update_post_meta($post_id, 'rdv_emailclient', $rdv_emailclientvalue);
        }else{
            add_post_meta($post_id, 'rdv_emailclient', "");
        }

        if(isset($_POST['rdv_telclient'])){
		    $rdv_telclientvalue = $_POST['rdv_telclient'];
		    update_post_meta($post_id, 'rdv_telclient', $rdv_telclientvalue);
        }else{
            add_post_meta($post_id, 'rdv_telclient', "");
        }

        if(isset($_POST['rdv_commentaireclient'])){
		    $rdv_commentaireclientvalue = $_POST['rdv_commentaireclient'];
		    update_post_meta($post_id, 'rdv_commentaireclient', $rdv_commentaireclientvalue);
        }else{
            add_post_meta($post_id, 'rdv_commentaireclient', "");
        }

        if(isset($_POST['rdv_etat'])){
		    $rdv_etatvalue = $_POST['rdv_etat'];
		    update_post_meta($post_id, 'rdv_etat', $rdv_etatvalue);
        }else{
            add_post_meta($post_id, 'rdv_etat', "");
        }

	}
}