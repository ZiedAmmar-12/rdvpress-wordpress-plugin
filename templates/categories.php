<div class="wrap">

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('rdvpress_plugin_categories_settings');
        do_settings_sections('rdvpress_categories');
        submit_button();
        ?>
    </form>
</div>