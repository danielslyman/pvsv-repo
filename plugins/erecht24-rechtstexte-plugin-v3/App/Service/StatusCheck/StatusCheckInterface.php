<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

interface StatusCheckInterface
{
    /**
     * @return StatusCheckResponse
     */
    public function check() : StatusCheckResponse;
}