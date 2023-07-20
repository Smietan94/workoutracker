<?php

declare(strict_types=1);

namespace App;

use Psr\Http\Message\ResponseInterface as Response;

class ResponseFormatter
{
    public function asJson(
        Response $response, 
        mixed $data,
        int $flags = \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_HEX_APOS | \JSON_THROW_ON_ERROR
    ): Response {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(\json_encode($data, $flags));

        return $response;
    }

    public function asDataTable(
        Response $response,
        array $data,
        int $draw,
        int $total
    ) {
        return $this->asJson(
            $response,
            [
                'data'            => $data,
                'draw'            => $draw,
                'recordsTotal'    => $total,
                'recordsFiltered' => $total
            ]
        );
    }
}