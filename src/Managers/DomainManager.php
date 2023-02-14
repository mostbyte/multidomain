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
        return explode('.', $this->getDomain(), 2)[0];
    }

    public function getLocale(): string
    {
        return $this->request->getLocale();
    }

}
