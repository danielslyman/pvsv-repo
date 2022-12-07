<?php
/**
 * @var $this \eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab
 */
use eRecht24\LegalTexts\App\Helper;
?>
<div>
	<?php if (!$this->everythingIsOk()): ?>
        <p class="erecht24-statusError"><?php _e( 'Please check the following information, the plugin is not working properly.', 'erecht24' );  ?></p>
	<?php endif; ?>

    <table id="statusCheckTable" class="form-table">
        <tbody>
        <?php echo $this->renderTableRow($this->wpRemoteCheck, __('wp_remote_request is working', Helper::PLUGIN_TEXT_DOMAIN)) ?>
        <?php echo $this->renderTableRow($this->apiConnectionCheck, __('eRecht24 Server is available', Helper::PLUGIN_TEXT_DOMAIN)) ?>
        <?php echo $this->renderTableRow($this->apiConfigCheck, __('Configuration is valid', Helper::PLUGIN_TEXT_DOMAIN)) ?>
        <?php echo $this->renderTableRow($this->pingPongCheck, __('eRecht24 server can push', Helper::PLUGIN_TEXT_DOMAIN)) ?>
        <?php echo $this->renderTableRow($this->domainChangeCheck, __('WordPress Address (URL) did not change', Helper::PLUGIN_TEXT_DOMAIN)) ?>
        <?php if ($this->showRefreshButton()): ?>
            <tr>
                <th>&nbsp;</th>
                <td>
                    <button id="refreshConnection" class="button button-primary" >
                        <?php _e( 'Refresh Connection', Helper::PLUGIN_TEXT_DOMAIN ); ?>
                    </button>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
