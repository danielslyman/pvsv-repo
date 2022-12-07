<?php
/**
 * @var $this \eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab
 */
use eRecht24\LegalTexts\App\Helper;
?>

<?php // General information ?>
<h2><?php _e( 'Status overview', Helper::PLUGIN_TEXT_DOMAIN ); ?></h2>
<p class="introduction"><?php _e( 'You can check here if your server satisfies the system requirements to run the plugin and if your server can reach the eRecht24 servers.', Helper::PLUGIN_TEXT_DOMAIN ); ?></p>

<?php // Status Table ?>
<div id="statusTableContainer">
    <?php echo $this->renderStatusTable(); ?>
</div>

<?php // Debug description ?>
<div>
    <p><?php _e( 'In case you need support please send an e-mail and attach debug information by following these simple steps:', Helper::PLUGIN_TEXT_DOMAIN ); ?></p>
    <ol>
        <li>
            <?php _e( 'Write an E-Mail to <a href="mailto:support@e-recht24.de"> support@e-recht24.de </a>.', Helper::PLUGIN_TEXT_DOMAIN ); ?>
        </li>
        <li>
            <?php _e( 'Describe your Problem as detailed as possible.', Helper::PLUGIN_TEXT_DOMAIN ); ?>
        </li>
        <li>
            <?php _e( 'Click the button labeled "Copy debug information into your clipboard".', Helper::PLUGIN_TEXT_DOMAIN ); ?>
        </li>
        <li>
            <?php _e( 'Add the information to your email by pasting it with "ctrl+v" or by right clicking and selecting paste.', Helper::PLUGIN_TEXT_DOMAIN ); ?>
        </li>
    </ol>
</div>

<?php // Debug Data ?>
<?php echo $this->renderDebugData(); ?>
