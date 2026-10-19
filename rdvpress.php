<?php
/*
@package AZitPlugin
Plugin Name: Rdv Press
Plugin URI: https://azit.fr
Description: Appointment booking wordpress plugin
Author: AZ IT  
Version: 1.0.0
*/


defined('ABSPATH') or die('you cannot access');

// Require once the Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
function activate_rdvpress()
{

	Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_rdvpress');

/**
 * The code that runs during plugin deactivation
 */
function deactivate_rdvpress()
{

	Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_rdvpress');

/**
 * The code that runs during plugin uninstall
 */

function uninstall_rdvpress() {
    Inc\Base\Uninstall::uninstall();
}
register_uninstall_hook(__FILE__, 'uninstall_rdvpress');

/**
 * Initialize all the core classes of the plugin
 */
if (class_exists('Inc\\Init')) {
	Inc\Init::register_services();
}

function load_assets_script(){
    wp_enqueue_style('rdvpress_formstyle', plugin_dir_url(__FILE__) . 'assets/style-rdvpress.css', array(), '1.0', 'all');
    wp_enqueue_script('rdvpress_formjs', plugin_dir_url(__FILE__) . 'assets/src-main-rdvpress.js', array('jquery'), '1.0', true);

    $couleur = get_option('couleur', '#ffffff');
    $custom_css = "
        .sidebar {
            background: {$couleur};
        }
        button {
            background: {$couleur};
            color: white;
        }
        .calendar div.selected, .time-slots div.selected {
            background: {$couleur};
            color: white;
        }
        .day-header {
            background-color: {$couleur};
        }
    ";
    wp_add_inline_style('rdvpress_formstyle', $custom_css);

    wp_localize_script('rdvpress_formjs', 'postdata', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('is_nonce_security_formrdvpress'),
    ));
}

add_action('wp_enqueue_scripts', 'load_assets_script');

function load_scripts(){
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

    <?php
}
add_action('wp_head','load_scripts');

function shortcode_form_rdvpress()
{
	global $post;
 
	// We're outputing a lot of html and the easiest way 
	// to do it is with output buffering from php.
	// 
	
	ob_start();

    $type_rdv = get_option('type_rdv');

    switch ($type_rdv) {
        
        case 'garage':
        default:
            rdpress_handle_form_garage();
            break;
    }


	// Output the content.
	$output = ob_get_contents();
	ob_end_clean();
    
	// Return only if we're inside a page. This won't list anything on a post or archive page. 
	if (is_page()) return  $output;
}
 
// Add a shortcode
add_shortcode('sc-formrdvpress', 'shortcode_form_rdvpress');


function rdpress_handle_form_garage(){

    wp_nonce_field('rendezvous_insert');

    $categories = get_categories(array('taxonomy' => 'Catégories', 'hide_empty' => false,
    'parent' => '0'));
    $couleur = get_option('couleur', '#ffffff');
    $type_rdv = get_option('type_rdv');
    $email_rdvinfo = get_option('email_rdvinfo');
    $telephone_rdvinfo = get_option('telephone_rdvinfo');
    ?>

    
        <div class="container">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

        <style>

        .sidebar {
            background: <?php echo $couleur; ?>;
            color: white;
            padding: 20px;
            width: 30%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: width 0.3s ease;
        }

        button {
            background: <?php echo $couleur; ?>;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }

        .calendar div.selected {
            background: <?php echo $couleur; ?>;
            color: white;
        }

        .time-slots div.selected {
            background: <?php echo $couleur; ?>;
            color: white;
            border: none;
        }

        .day-header {
            flex: 1 0 14.28%; /* Distribution égale de la largeur */
            text-align: center;
            padding: 8px 0; /* Ajustement du padding pour mieux centrer le texte */
            margin: 0; /* Pas de marge pour éviter les décalages */
            background-color: <?php echo $couleur; ?>;
            color: #fff;
            font-weight: bold;
            border: 1px solid #ddd;
            box-sizing: border-box; /* Assure que le padding et les bordures sont inclus dans les dimensions de l'élément */
            line-height: 1.5; /* Hauteur de ligne pour un meilleur alignement vertical */
            font-size: 14px; /* Taille de police ajustée pour une meilleure lisibilité */
            height: 50px; /* Hauteur fixe pour garantir que tous les en-têtes aient la même taille */
            display: flex;
            align-items: center; /* Centre le texte verticalement */
            justify-content: center; /* Centre le texte horizontalement */
        }

        .selected {
            background-color: <?php echo $couleur; ?>;
            
        }
                
        </style>

                <div class="sidebar" id="sidebar">
                    <div class="menu">
                        <div class="menu-item active" data-tab="1" onclick="showTab(1)">⚙️ Veuillez choisir un service</div>
                        <div class="menu-item" data-tab="2" onclick="attemptShowTab(2)">📅 Date & Heure</div>
                        <div class="menu-item" data-tab="3" onclick="attemptShowTab(3)">📞 Vos coordonnées</div>
                    </div>


                    <div class="contact">
                        <p>Contact</p>
                        <?php echo ($email_rdvinfo); ?>
                        <br>
                        <?php echo ($telephone_rdvinfo); ?>
                    </div>
                    

                    <div class="am-fs-sb__menu" onclick="toggleSidebar()">
                        <span class="am-fs-sb__menu-text" id="menuText">Collapse menu</span>
                        <span class="am-icon-arrow-circle-right">➔</span>
                    </div>
                    
                </div>

                <div class="form-container">
            <form name="formrdv" id="formrdv" action="" method="POST">
                <div class="tab-content active" id="tab-1">
                    <h2>Veuillez choisir un service</h2>
                    <div>
                        <label for="rdv_categorie">Service <span>*</span>:</label>
                        <select name="rdv_categorie" id="rdv_categorie" required>
                            <option value="">--choisir--</option>
                            <?php
                                foreach ($categories as $category) {
                                    echo '<option value="' . $category->term_id . '">' . $category->name . '</option>';
                                }
                            ?>
                        </select>

                        <label for="rdv_service">Type de service <span>*</span>:</label>
                        <select name="rdv_service" id="rdv_service" required>
                            <option value="">--choisir--</option>
                        </select>
                    </div>
                    <button type="button" onclick="nextTab(1)">Continuer</button>
                </div>

                <div class="tab-content" id="tab-2">
                    <h2>Date & Heure</h2>
                    <div class="calendar-container">
                        <div class="calendar-controls">
                            <select id="month-select"></select>
                            <select id="year-select"></select>
                        </div>
                        <div id="calendar" class="calendar">
                            <!-- Les dates seront générées ici par JavaScript -->
                        </div>
                    </div>

                    <div id="selected-date-display" class="selected-date-display"></div>
                    <div class="time-container">
                        <div class="time-slots" id="time-slots">
                            <!-- Les créneaux horaires seront générés ici par JavaScript -->
                        </div>
                    </div>
                    <input type="hidden" id="rdv_date" name="rdv_date" value="">
                    <input type="hidden" id="rdv_time" name="rdv_time" value="">
                    <input type="hidden" id="globalRdvTime" name="globalRdvTime" value="">

                    <button type="button" onclick="prevTab(2)">Retour</button>
                    <button type="button" onclick="nextTab(2)">Continuer</button>
                </div>

                <div class="tab-content" id="tab-3">
                    <h2>Vos coordonnées</h2>

                    <label for="rdv_genreclient">Genre <span>*</span>:</label>
                    <select id="rdv_genreclient" name="rdv_genreclient" required>
                        <option value="" disabled selected>Choisir...</option>
                        <option value="Mr">Mr</option>
                        <option value="Mme">Mme</option>
                        <option value="Mlle">Mlle</option>
                        <option value="Société">Société</option>
                    </select>

                    <label for="rdv_immatriculation">N° de plaque d'immatriculation <span>*</span>:</label>
                    <input type="text" id="rdv_immatriculation" name="rdv_immatriculation" placeholder="AB-123-CD..." maxlength="9" required pattern="[A-Z]{2}-[0-9]{3}-[A-Z]{2}" title="Le format doit être deux lettres majuscules, un tiret, trois chiffres, un tiret, deux lettres majuscules">

                    <label for="rdv_nomclient">Nom et Prénom <span>*</span>:</label>
                    <input type="text" id="rdv_nomclient" name="rdv_nomclient" placeholder="saisissez nom" required pattern="[A-Za-z\s]+" title="Veuillez entrer uniquement des lettres.">

                    <label for="rdv_emailclient">E-mail <span>*</span>:</label>
                    <input type="email" id="rdv_emailclient" name="rdv_emailclient" placeholder="saisissez email" required>

                    <label for="rdv_telclient">Téléphone <span>*</span>:</label>
                    <input type="tel" id="rdv_telclient" name="rdv_telclient" placeholder="saisissez téléphone" required>

                    <label for="rdv_commentaireclient">Commentaire :</label>
                    <textarea id="rdv_commentaireclient" name="rdv_commentaireclient" rows="3" cols="23" placeholder="Commentaire"></textarea>
                    <button type="button" onclick="prevTab(3)">Retour</button>
                    <button type="submit">Soumettre</button>
                </div>
            </form>
        </div>

        <div class="congratulations-container" id="congratulations-container" style="display:none;">
            <h1>Félicitations!</h1>
            <p>Votre rendez-vous a été réservé avec succès.</p>
            <div class="details-container" id="appointment-details">
                
                <!-- Details of appointment will be displayed here -->
            </div>
            
        </div>
<?php
}

function rdpress_handle_form_avocat(){

    echo "ici copie form avocat";

}


function rdpress_handle_form_medecin(){

    echo "ici copie form medecin";

}


add_action('wp_ajax_rdvpress_insertform', 'rendezvous_insert_callbacks');
add_action('wp_ajax_nopriv_rdvpress_insertform', 'rendezvous_insert_callbacks');
function rendezvous_insert_callbacks() {
    if (!wp_verify_nonce($_POST['security'], 'is_nonce_security_formrdvpress')) {
        wp_die();
    }

    $rdv_categorievalue = $_POST['rdv_categorie'];
    $rdv_servicevalue = $_POST['rdv_service'];
    $rdv_datevalue = $_POST['rdv_date'];
    $rdv_timevalue = $_POST['rdv_time'];
    $rdv_immatriculationvalue = $_POST['rdv_immatriculation'];
    $rdv_genreclientvalue = $_POST['rdv_genreclient'];
    $rdv_nomclientvalue = $_POST['rdv_nomclient'];
    $rdv_emailclientvalue = $_POST['rdv_emailclient'];
    $rdv_telclientvalue = $_POST['rdv_telclient'];
    $rdv_commentaireclientvalue = $_POST['rdv_commentaire'];
    $rdv_etatvalue = 'enattente';

    // Récupérer le titre du service
    $servicetitle = get_the_title($rdv_servicevalue);

    $post_array = array(
        'post_title'   => 'Rendez vous : ' . $rdv_nomclientvalue . ' - ' . $servicetitle . ' - ' . $rdv_datevalue . ' ' . $rdv_timevalue,
        'post_content' => 'Rendez vous : ' . $rdv_nomclientvalue . ' pour le service ' . $servicetitle,
        'post_type'    => 'rdvpress_rendez_vous',
        'post_name'    => 'rendez-vous-' . sanitize_title($rdv_nomclientvalue . '-' . $servicetitle),
        'post_status'  => 'publish',
    );

    $post_id = wp_insert_post($post_array);

    if (!is_wp_error($post_id)) {
        update_post_meta($post_id, 'rdv_categorie', $rdv_categorievalue);
        update_post_meta($post_id, 'rdv_service', $rdv_servicevalue);
        update_post_meta($post_id, 'rdv_date', $rdv_datevalue);
        update_post_meta($post_id, 'rdv_time', $rdv_timevalue);
        update_post_meta($post_id, 'rdv_immatriculation', $rdv_immatriculationvalue);
        update_post_meta($post_id, 'rdv_genreclient', $rdv_genreclientvalue);
        update_post_meta($post_id, 'rdv_nomclient', $rdv_nomclientvalue);
        update_post_meta($post_id, 'rdv_emailclient', $rdv_emailclientvalue);
        update_post_meta($post_id, 'rdv_telclient', $rdv_telclientvalue);
        update_post_meta($post_id, 'rdv_commentaireclient', $rdv_commentaireclientvalue);
        update_post_meta($post_id, 'rdv_etat', $rdv_etatvalue);
        $response['success'] = true;
        $response['data'] = $post_id;
    } else {
        $response['success'] = false;
        $response['data'] = [];
    }

    // Utiliser $servicetitle dans l'email de confirmation
    $to = $rdv_emailclientvalue;
    $name = $rdv_nomclientvalue;
    $date = $rdv_datevalue;
    $time = $rdv_timevalue;

    $email_result = sendConfirmationEmail($to, $name, $servicetitle, $date, $time);
    if ($email_result) {
        wp_send_json($response);
    }
}




add_action( 'wp_ajax_service_cat_option', 'get_listservice_cat_option' );
add_action( 'wp_ajax_nopriv_service_cat_option', 'get_listservice_cat_option' );

function get_listservice_cat_option() {
	// If nonce verification fails die.
	if ( ! wp_verify_nonce( $_POST['security'], 'is_nonce_security_formrdvpress' ) ) {
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

    $bruteposts = new WP_Query($args);
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

add_action( 'wp_ajax_time_date_option', 'get_listtime_date_option' );
add_action( 'wp_ajax_nopriv_time_date_option', 'get_listtime_date_option' );

function get_listtime_date_option() {
    // If nonce verification fails die.
    if ( ! wp_verify_nonce( $_POST['security'], 'is_nonce_security_formrdvpress' ) ) {
        wp_die();
    }

    $rdv_categorie_id = $_POST['rdv_categorie_id'];
    $rdv_service_id = $_POST['rdv_service_id'];
    $rdv_date = $_POST['rdv_date'];
    


    $args = array(
        'post_type' => 'rdvpress_rendez_vous',
        'orderby' => 'post_name',
        'order' => 'ASC',
        'post_status' => 'publish',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'rdv_date',
                'value' => $rdv_date,
                'compare' => '='
            ),
            array(
                'key' => 'rdv_categorie',
                'value' => $rdv_categorie_id,
                'compare' => '='
            ),
            array(
                'key' => 'rdv_service',
                'value' => $rdv_service_id,
                'compare' => '='
            )
        ),
    );
       

    $bruteposts = new WP_Query($args);
    $postsrdv = $bruteposts->get_posts();
  
    
    $appointments = array();

    foreach ($postsrdv as $post) { 
        
        $rdv_title = $post->post_title;
        $rdv_id = $post->ID;

        // Get the rdv_time meta value
        $rdv_time = get_post_meta($rdv_id, 'rdv_time', true);

        // Append to the appointments array
        $appointments[] = array(
            
            'title' => $rdv_title,
            'date' => $rdv_date,
            'time' => $rdv_time
        );
    }
		

    // Encode the appointments array as JSON to send to the frontend
    echo json_encode($appointments);
    wp_die();


    wp_send_json_success( array(
        'my_data' => $content,  // Always pass your data here that you want to access in js file.
        'data_recieved_from_js' => $_POST,  // $_POST will contain the array of data object passed in js file second parameter. like action,name etc
    ) );
}

function sendConfirmationEmail($to, $name, $service, $date, $time) {

    $email_rdvreply = get_option('email_rdvreply');

    $headers = array();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . get_bloginfo('name') . ' <me@example.net>';

    //print_r($headers);

    $subject = "Votre rendez-vous a été pris avec succès";
    $message = "<p>Bonjour {$name},</p>
                <p>Votre rendez-vous pour le service {$service} a été confirmé pour le {$date} à {$time}.</p>
                <p>Merci.</p>";  

    if (wp_mail($to, $subject, $message, $headers)) { 
        return true;
    } else {
        return false;
    }
}
