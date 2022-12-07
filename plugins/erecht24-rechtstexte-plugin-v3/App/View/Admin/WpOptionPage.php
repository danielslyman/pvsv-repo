<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\View\Admin;

use eRecht24\LegalTexts\App\View\AbstractView;
use eRecht24\LegalTexts\App\View\Admin\WpOptionPage\StatusTab;

class WpOptionPage extends AbstractView
{
    const TEMPLATE_PATH = 'Inc/admin/views/WpOptionPage.php';
    const TAB_MENU_TEMPLATE_PATH = 'Inc/admin/views/WpOptionPage/TabMenu.php';

	/**
     * Provide eRecht24 logo
     * @return string
     */
    protected function getLogoUrl() : string
    {
        $pluginFolder = basename( plugin_dir_path(  dirname( __FILE__ , 3 ) ) );
        return plugins_url( $pluginFolder . '/Inc/admin/img/logo-erecht24-long-72-rgb.png');
    }

    /**
     * Parses the markdown to html
     */
    protected function renderDocumentationTab()
    {
        $parseDown = new \Parsedown();
        $pluginFolder = basename( plugin_dir_path(  dirname( __FILE__ , 3 ) ) );
        $docs      = file_get_contents(WP_PLUGIN_DIR . '/' . $pluginFolder . '/Inc/admin/docs/documentation.md');
        echo $parseDown->text( $docs );
    }

    /**
     * render status tab
     */
    protected function renderStatusTab()
    {
        $statusTab = new StatusTab(StatusTab::TEMPLATE_PATH);
        $statusTab->render();
    }

	/**
	 * Provide current tab
	 * @return string
	 */
    public function getActiveTab()
    {
        return (string)($_GET['tab'] ?? 'api_key');
    }

	/**
	 * Render Tab menu
	 */
    protected function renderTabMenu()
    {
	    $this->renderChild(self::TAB_MENU_TEMPLATE_PATH);
    }

	/**
	 * Render tab content.
	 */
	protected function renderTabContent()
	{
		switch ($this->getActiveTab()) {
			case 'documentation' :
				return $this->renderDocumentationTab();
			case 'statusTab' :
				return $this->renderStatusTab();
			default :
				settings_fields( sprintf('erecht24_%s_group', $this->getActiveTab()) );
				do_settings_sections( sprintf('erecht24_%s_page', $this->getActiveTab()) );
				submit_button();
				break;
		}
	}
}
