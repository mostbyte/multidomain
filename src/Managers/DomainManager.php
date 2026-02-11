<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainManager
{
    protected string $subdomain;

    public function __construct(protected Request $request) {}

    public function setSubdomain(string $subdomain): static
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function getFullDomain(): string
    {
        return $this->request->getSchemeAndHttpHost();
    }

    public function getSubDomain(): string
    {
        return $this->subdomain ?? Str::of(parse_url(request()->url(), PHP_URL_PATH))->trim('/')->explode('/')[0];
    }

    public function getLocale(): string
    {
        return $this->request->getLocale();
    }
}
