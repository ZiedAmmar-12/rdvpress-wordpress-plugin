<?php
namespace Inc\Api\Callbacks;
use Inc\Base\BaseController;
class AdminCallbacks extends BaseController
{
    public function adminReglages()
    {
        return require_once("$this->plugin_path/templates/admin.php");
    }
    public function adminServices()
    {
        return require_once("$this->plugin_path/templates/services.php");
    }
    public function adminCategories()
    {
        return require_once("$this->plugin_path/templates/categories.php");
    }
    public function adminRDV()
    {
        return require_once("$this->plugin_path/templates/rendez-vous.php");
    }
    public function rdvpressOptionsGroup($input)
    {
        return $input;
    }
    public function rdvpressAdminSection()
    {
        echo '';
    }
    public function rdvpressTypeRDV()
    {
        $select = get_option('type_rdv');
        $value = esc_attr($select);
?>
    <select name="type_rdv" required>
        <option value="">--- rendez-vous ---</option>
        <option value="garage" <?php if ($value == 'garage') { echo "selected"; } ?>>Garage</option>
    </select>
<?php
    }
    public function rdvpressColor()
    {
        $name = 'couleur';
        $value = get_option($name);
        echo '<input type="color" name="' . $name . '" value="' . $value . '" required>';
    }
    public function rdvpressEmailinfo()
    {
        $name = 'email_rdvinfo';
        $value = get_option($name);
        echo '<input type="email" name="' . $name . '" value="' . $value . '" placeholder="Entrez votre email" required>';
    }
    public function rdvpressTelephoneinfo()
    {
        $name = 'telephone_rdvinfo';
        $value = get_option($name);
        echo '<input type="tel" name="' . $name . '" value="' . $value . '" placeholder="Entrez votre numéro de téléphone" required>';
    }
    public function rdvpressnotif()
    {
        $name = 'rdvnotif';
        $value = get_option($name);
        $checked = ($value == 1) ? ' checked' : '';
        echo '<input type="checkbox" name="' . $name . '" value="1"' . $checked . ' required>';
    }
    public function rdvpressEmailnotif()
    {
        $name = 'email_rdvnotif';
        $value = get_option($name);
        echo '<input type="email" name="' . $name . '" value="' . $value . '" placeholder="Entrez liste emails à notifier" required>';
    }
    public function rdvpressEmailreply()
    {
        $name = 'email_rdvreply';
        $value = get_option($name);
        echo '<input type="email" name="' . $name . '" value="' . $value . '" placeholder="Entrez liste emails à reply" required>';
    }
}