<?php
declare(strict_types=1);

namespace eRecht24\LegalTexts\App\Service\StatusCheck;

class StatusCheckResponse
{
    private $success;
    private $message;
    private $data;

    /**
     * StatusCheckResponse constructor.
     * @param bool $success
     * @param string $message
     * @param $data
     */
    public function __construct(
        bool $success,
        string $message,
        $data = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get status check message
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set status checker message
     * @param string $message
     */
    public function setMessage(
        string $message
    ): void
    {
        $this->message = $message;
    }

    /**
     * Get status checker success flag
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get status checker data
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}