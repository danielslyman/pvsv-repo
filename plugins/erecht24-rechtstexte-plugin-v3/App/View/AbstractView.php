<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\View;

class AbstractView implements AbstractViewInterface
{
    protected $template;
    protected $data;

    /**
     * AbstractView constructor.
     * @param string|null $template
     * @param array $data
     */
    public function __construct(
        ?string $template = null,
        $data = []
    ) {
        $this->setTemplate($template);
        $this->data = $data;
    }

    /**
     * Function provides template
     * @param string|null $file
     * @return string
     */
    public function getTemplate(
        ?string $file = null
    ): string
    {
        $template = $file ?? $this->template;
        // get the plugin folder, users may install the plugin inside a folder not called `erecht24`
        $pluginFolder = basename( plugin_dir_path(  dirname( __FILE__ , 2 ) ) );
        return WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $pluginFolder . DIRECTORY_SEPARATOR . $template;
    }

    /**
     * Function sets template
     * @param null|string $template
     */
    public function setTemplate(
        ?string $template
    ): void
    {
        $this->template = $template;
    }

    /**
     * Function provides view data
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Function renders view
     */
    public function render()
    {
        $file = $this->getTemplate();

        // ensure the file exists
        if ( !file_exists($file) ) {
            print '<p style="color: red; padding: 5px; border: 1px solid red;">no valid Template file</p>';
            return;
        }

        require_once $file;
    }

    /**
     * Function renders child view
     * @param string $template
     * @return mixed|void
     */
    public function renderChild(string $template)
    {
        $file = $this->getTemplate($template);

        // ensure the file exists
        if ( !file_exists($file) ) {
            print '<p style="color: red; padding: 5px; border: 1px solid red;">no valid Template file</p>';
            return;
        }

        require_once $file;
    }
}
