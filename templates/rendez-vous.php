<h1>Rendez-vous</h1>
<?php settings_errors(); ?>
<div id="tab-2" class="tab-pane">
    <form method="post" action="options.php">
        <?php
        settings_fields('rdvpress_plugin_rendez_vous_settings');
        do_settings_sections('rdvpress_rendez_vous');
        submit_button();
        ?>
    </form>