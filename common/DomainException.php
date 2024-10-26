<?php

namespace app\common;

class DomainException extends \Exception
{
    private array $domainError;

    public function __construct(array $domainError, int $code = 500, string $message = "Server error")
    {
        $this->domainError = $domainError;
        parent::__construct($message, $code);
    }

    /**
     * @return array
     */
    public function getDomainError(): array
    {
        return $this->domainError;
    }
}