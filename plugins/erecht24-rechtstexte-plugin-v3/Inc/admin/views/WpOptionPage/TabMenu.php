<?php
/**
 * @var $this \eRecht24\LegalTexts\App\View\Admin\WpOptionPage
 */

use eRecht24\LegalTexts\App\Helper;

$active_tab = $this->getActiveTab();
?>
<div class="nav-tab-wrapper">
	<a href="?page=erecht24-settings&tab=api_key"
	   class="nav-tab <?php echo $active_tab === 'api_key' ? 'nav-tab-active' : ''; ?>">
		<?php if ( ! Helper::getApiKey() ): ?>
			<span class="dashicons dashicons-warning"></span>
		<?php else: ?>
			<span class="dashicons dashicons-admin-network"></span>
		<?php endif; ?>
		<?php _e( 'API Key', 'erecht24' ); ?>
	</a>
	<a href="?page=erecht24-settings&tab=imprint"
	   class="nav-tab <?php echo $active_tab === 'imprint' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-megaphone"></span> <?php _e( 'Imprint', 'erecht24' ); ?></a>
	<a href="?page=erecht24-settings&tab=privacy_policy"
	   class="nav-tab <?php echo $active_tab === 'privacy_policy' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-admin-users"></span> <?php _e( 'Privacy policy', 'erecht24' ); ?>
	</a>
	<a href="?page=erecht24-settings&tab=privacy_policy_social_media"
	   class="nav-tab <?php echo $active_tab === 'privacy_policy_social_media' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-facebook"></span> <?php _e( 'Privacy policy for social media profile', 'erecht24' ); ?>
	</a>
	<a href="?page=erecht24-settings&tab=google_analytics"
	   class="nav-tab <?php echo $active_tab === 'google_analytics' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-chart-line"></span> <?php _e( 'Google Analytics', 'erecht24' ); ?>
	</a>
	<a href="?page=erecht24-settings&tab=documentation"
	   class="nav-tab <?php echo $active_tab === 'documentation' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-editor-help"></span> <?php _e( 'Documentation', 'erecht24' ); ?>
	</a>
	<a href="?page=erecht24-settings&tab=statusTab"
	   class="nav-tab <?php echo $active_tab === 'statusTab' ? 'nav-tab-active' : ''; ?>"><span
			class="dashicons dashicons-lightbulb"></span> <?php _e( 'Plugin Status', 'erecht24' ); ?>
	</a>
</div>