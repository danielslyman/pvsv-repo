<?php
/**
 * @var $this \eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab
 */
use eRecht24\LegalTexts\App\Helper;
?>
<div>
    <textarea id="debugInfoData" style="display: none; width: 100%; height: 350px; "></textarea>
    <div class="textCopied " style="display: none;border: 1px solid #ccd0d4; border-left: 4px solid rgb(122, 208, 58); padding: 5px 15px; margin: 10px 0">
		<?php _e('copied to clipboard', Helper::PLUGIN_TEXT_DOMAIN) ?>
    </div>
</div>
<div class="erecht24_debug_button_container">
    <button id="debugInfoButton" class="button button-primary" disabled>
		<?php _e( 'Copy debug information into your clipboard', Helper::PLUGIN_TEXT_DOMAIN ); ?>
    </button>
    <span id="copy_notice" class="erecht24-copy-notice">
        <?php _e( 'copied to clipboard', Helper::PLUGIN_TEXT_DOMAIN ); ?>
    </span>
</div>