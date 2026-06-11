<?php

namespace Helix\Http;

class JsonResponse extends Response
{
    public function __construct(
        mixed $data = [],
        int $status = 200,
        array $headers = [],
        int $jsonOptions = JSON_UNESCAPED_UNICODE
    ) {
        parent::__construct(
            json_encode($data, $jsonOptions) ?: '',
            $status,
            array_merge($headers, ['Content-Type' => 'application/json; charset=utf-8'])
        );
    }
}
