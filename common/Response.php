<?php

namespace app\common;

class Response
{
    const STATUS_OK = 'OK';

    const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    public string $status;

    /**
     * @var array
     */
    public array $data;

    public function __construct(string $status, array $data)
    {
        $this->status = $status;
        $this->data = $data;
    }

    public static function getErrorResponse(array $errorData): Response
    {
        return new self(self::STATUS_ERROR, $errorData);
    }

    public static function getSuccessResponse(array $data): Response
    {
        return new self(self::STATUS_OK, $data);
    }
}