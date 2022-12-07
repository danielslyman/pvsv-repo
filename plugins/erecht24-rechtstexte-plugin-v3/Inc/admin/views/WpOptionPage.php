<?php
/**
 * @var $this \eRecht24\LegalTexts\App\View\Admin\WpOptionPage
 */
?>

<div id="ajaxLoader">
    <div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
</div>
<div class="wrap erecht24-settings-wrapper">
    <img src="<?php echo $this->getLogoUrl(); ?>" class="erecht24-logo"/>
    <h1 id="erecht24-settings-title">
        <?php _e( 'eRecht24 - Disclaimer texts', 'erecht24' ); ?>
    </h1>
    <p class="about-text">
        <?php _e( 'Here you can enable or disable various core features of the plugin in order to keep the site optimized for your needs.', 'erecht24' ); ?>
    </p>

    <?php echo $this->renderTabMenu() ?>

    <form method="post" action="options.php" id="<?php echo $this->getActiveTab(); ?>_form">
        <?php echo $this->renderTabContent();        ?>
    </form>

    <div class="poweredby">
        <hr/>
        <p><?php echo _e( 'Powered by <a href="https://www.e-recht24.de" target="_blank">eRecht24</a>', 'erecht24' ); ?></p>
    </div>
</div>
