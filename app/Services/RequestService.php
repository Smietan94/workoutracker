<?php

declare(strict_types=1);

namespace App\Services;

#region Use-Statements
use App\Contracts\SessionInterface;
use App\DTO\DataTableQueryParams;
use Psr\Http\Message\ServerRequestInterface as Request;
#endregion

class RequestService
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function getReferer(Request $request): string
    {
        $referer = $request->getHeader('referer')[0];

        if (! $referer) {
            return $this->session->get('previousUrl');
        }

        $refererHost = \parse_url($referer, \PHP_URL_HOST);

        if ($refererHost !== $request->getUri()->getHost()) {
            $referer = $this->session->get('previousUrl');
        }

        return $referer;
    }

    public function isXhr(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function getDataTableQueryParams(Request $request): DataTableQueryParams
    {
        $params = $request->getQueryParams();

        return new DataTableQueryParams(
            (int) $params['draw'],
            (int) $params['start'],
            (int) $params['length'],
            $params['columns'][$params['order'][0]['column']]['data'],
            $params['order'][0]['dir'],
            $params['search']['value'],
        );
    }
}