<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    protected $errorCode;
    public function __construct(string $message = "", int $errorCode = 400)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $this->message], $this->errorCode);
        }
        return view('pages.error', ['message' => $this->message]);
    }
}
