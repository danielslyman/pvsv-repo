<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\View\Admin\WpOptionPage;

use eRecht24\LegalTexts\App\Api\ApiResponse;
use eRecht24\LegalTexts\App\Helper;
use eRecht24\LegalTexts\App\Service\StatusCheck\ApiConnectionCheck;
use eRecht24\LegalTexts\App\Service\StatusCheck\DomainChangeCheck;
use eRecht24\LegalTexts\App\Service\StatusCheck\PingPongCheck;
use eRecht24\LegalTexts\App\Service\StatusCheck\ApiConfigCheck;
use eRecht24\LegalTexts\App\Service\StatusCheck\StatusCheckResponse;
use eRecht24\LegalTexts\App\Service\StatusCheck\WpRemoteCheck;
use eRecht24\LegalTexts\App\View\AbstractView;

class StatusTab extends AbstractView
{
    const TEMPLATE_PATH = 'Inc/admin/views/WpOptionPage/StatusTab.php';
    const STATUS_TABLE_TEMPLATE_PATH = 'Inc/admin/views/WpOptionPage/StatusTab/StatusTable.php';
    const DEBUG_DATA_TEMPLATE_PATH = 'Inc/admin/views/WpOptionPage/StatusTab/DebugData.php';

    protected $wpRemoteCheck = null;
    protected $apiConnectionCheck = null;
    protected $apiConfigCheck = null;
    protected $pingPongCheck = null;
    protected $checksSkipped = true;
    protected $domainChangeCheck = null;

    /**
     * Function checks if plugin is working
     * do not change order
     */
    public function runStatusChecks() : StatusTab
    {
    	$this->checksSkipped = false;

        $this->checkApiConnection();
        $this->checkWpRemote();
        $this->checkConfiguration();
        $this->checkPingPong();
        $this->checkDomainChange();

        return $this;
    }

    /**
     * Check connection to eRecht24 Server
     */
    private function checkApiConnection()
    {
        $apiCheck = new ApiConnectionCheck();
        $this->apiConnectionCheck = $apiCheck->check();
    }

    /**
     * Check if wp_remote_request is working
     */
    private function checkWpRemote()
    {
        // if previous test was successful, wp_remote must be successful too
        if ($this->apiConnectionCheck->isSuccess()) {
            $this->wpRemoteCheck = new StatusCheckResponse(
                true,
                __('wp_remote_request is working properly.', Helper::PLUGIN_TEXT_DOMAIN)
            );
            return;
        }

        /** @var ApiResponse $responseData */
        $responseData = $this->apiConnectionCheck->getData();
        // originResponse is only available if a connection Error occurred
        $connection = (is_null($responseData))
            ? null
            : $responseData->getData('originResponse') ?? true;

        $statusCheck = new WpRemoteCheck($connection);
        $this->wpRemoteCheck = $statusCheck->check();
    }

    /**
     * Function checks if wp config is matching eRecht24 server config
     */
    private function checkConfiguration()
    {
        if (!$this->apiConnectionCheck->isSuccess()) {
            $this->apiConfigCheck = new StatusCheckResponse(
                false,
                $this->apiConnectionCheck->getMessage()
            );
            return;
        }

        $data = $this->apiConnectionCheck->getData() ?? null;
        $pushCheck = new ApiConfigCheck($data);
        $this->apiConfigCheck = $pushCheck->check();
    }

    /**
     * Function checks if eRecht24 server can push
     */
    private function checkPingPong()
    {
        if (!$this->apiConfigCheck->isSuccess()) {
            $this->pingPongCheck = new StatusCheckResponse(
                false,
                $this->apiConfigCheck->getMessage()
            );
            return;
        }

        $pingPongCheck = new PingPongCheck();
        $this->pingPongCheck = $pingPongCheck->check();
    }

    private function checkDomainChange()
    {
        $domainChangeCheck = new DomainChangeCheck();
        $this->domainChangeCheck = $domainChangeCheck->check();
    }

	/**
     * Check general status
     * @return bool
     */
    protected function everythingIsOk()
    {
    	if ($this->checksSkipped)
    		return true;

        return $this->wpRemoteCheck && $this->wpRemoteCheck->isSuccess()
            && $this->apiConnectionCheck && $this->apiConnectionCheck->isSuccess()
            && $this->apiConfigCheck && $this->apiConfigCheck->isSuccess()
            && $this->pingPongCheck && $this->pingPongCheck->isSuccess()
            && $this->domainChangeCheck && $this->domainChangeCheck->isSuccess()
        ;
    }

	/**
     * Function checks if refresh button should be enabled
     * @return bool
     */
    protected function showRefreshButton()
    {
        // (connection ok, config not ok) OR domain changed
        return ((!($this->apiConfigCheck && $this->apiConfigCheck->isSuccess())
                && ($this->apiConnectionCheck && $this->apiConnectionCheck->isSuccess()))
            || !($this->domainChangeCheck && $this->domainChangeCheck->isSuccess()));
    }

	/**
     * Render status table row
     * @param null|StatusCheckResponse $statusCheck
     * @param string $title
     * @return string
     */
    protected function renderTableRow(
        ?StatusCheckResponse $statusCheck,
        string $title
    ) {
    	if (is_null($statusCheck)) {
		    return sprintf('<tr><th scope="row">%s</th><td><p class="erecht24-statusrow loading">%s %s</p></td></tr>',
			    $title,
			    '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>',
			    __('checking', Helper::PLUGIN_TEXT_DOMAIN)
		    );
	    }

        $cssClass = ($statusCheck->isSuccess())
            ? 'erecht24-row-check'
            : 'erecht24-row-error';

        return sprintf('<tr><th scope="row">%s</th><td><p class="erecht24-statusrow %s">%s</p></td></tr>',
            $title,
            $cssClass,
            $statusCheck->getMessage()
        );
    }


	/**
	 * render status table
	 */
	public function renderStatusTable()
	{
		$this->renderChild(self::STATUS_TABLE_TEMPLATE_PATH);
	}

	/**
	 * render status table
	 */
	public function renderAjaxStatusTable(): string
	{
		$file = $this->getTemplate(self::STATUS_TABLE_TEMPLATE_PATH);

		// ensure the file exists
		if ( !file_exists($file) ) {
			return '<p style="color: red; padding: 5px; border: 1px solid red;">no valid Template file</p>';
		}

		ob_start();
		include($file);
		$var=ob_get_contents();
		ob_end_clean();

		return $var;
	}

	/**
	 * render status table
	 */
	public function renderDebugData()
	{
		$this->renderChild(self::DEBUG_DATA_TEMPLATE_PATH);
	}
}
