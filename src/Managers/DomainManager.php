<?php

namespace Mostbyte\Multidomain\Managers;

use Illuminate\Http\Request;

class DomainManager
{
    protected string $subdomain;

    public function __construct(protected Request $request)
    {
    }

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
        return $this->subdomain;
    }

    public function getLocale(): string
    {
        return $this->request->getLocale();
    }
}
