<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Http\Request;

class DomainManager
{
    public function __construct(protected Request $request)
    {
    }

    public function getDomain(): string
    {
        return $this->request->host();
    }

    /**
     * @return string
     */
    public function getFullDomain(): string
    {
        return $this->request->getSchemeAndHttpHost();
    }


    public function getSubDomain(): string
    {
        $subdomain = str_replace($this->removeSchemeAndPort(), '', $this->getDomain());
        return str_ends_with($subdomain, '.') ? substr($subdomain, 0, -1) : $subdomain;
    }

    public function getLocale(): string
    {
        return $this->request->getLocale();
    }

    protected function removeSchemeAndPort(): string
    {
        return preg_replace('/http:\/\/|https:\/\/|:\d+/', '', config('multidomain.url'));
    }
}
