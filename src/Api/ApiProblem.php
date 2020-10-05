<?php

declare(strict_types=1);

namespace App\Api;


class ApiProblem
{
    const TYPE_VALIDATION_ERROR = 'validation error';

    const TYPE_INVALID_BODY_FORMAT = 'invalid format';

    private static $titles = [
        self::TYPE_INVALID_BODY_FORMAT=>'Invalid json body format',
        self::TYPE_VALIDATION_ERROR => 'validation error'
    ];

    private $statusCode;

    private $type;

    private $title;

    private $extraData = [];

    /**
     * ApiProblem constructor.
     * @param $statusCode
     * @param null $type
     */
    public function __construct($statusCode, $type=null)
    {
        if (!isset(self::$titles[$type])) {
            throw InvalidArgumentException("There is no title for this type ".$type);
        }
        $this->title = self::$titles[$type];
        $this->type = $type;
        $this->statusCode = $statusCode;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->extraData,
            [
                'statusCode'=>$this->statusCode,
                'type'=>$this->type,
                'title'=>$this->title
            ]
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value): void
    {
        $this->extraData[$name] = $value;
    }
}