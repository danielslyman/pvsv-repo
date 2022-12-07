<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\View;

interface AbstractViewInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param string|null $file
     * @return string
     */
    public function getTemplate(?string $file) : string;

    /**
     * @param string $template
     */
    public function setTemplate(string $template) : void;

    /**
     * @return mixed
     */
    public function render();

    /**
     * @param string $template
     * @return mixed
     */
    public function renderChild(string $template);
}
