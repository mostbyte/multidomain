<?php

namespace Mostbyte\Multidomain\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;

class SuccessCommandResponse implements Responsable
{
    public function __construct(protected string $message, protected $status) {}

    public function toResponse($request): Response
    {
        return response([
            'success' => $this->status === 0,
            'status' => $this->status,
            'message' => $this->message,
        ]);
    }
}
