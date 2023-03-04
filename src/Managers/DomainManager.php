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
        return str_replace($this->removeSchemeAndPort(), '', $this->getDomain());
    }

    public function getLocale(): string
    {
        return $this->request->getLocale();
    }

    protected function removeSchemeAndPort(): string
    {
        return preg_replace('/http:\/\/|https:\/\/|:\d+/', '', config('app.url'));
    }
}
