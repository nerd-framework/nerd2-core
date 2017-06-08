<?php

namespace Nerd2\Core;

class Response
{
    private const DEFAULT_RESPONSE_CODE = 200;
    private const REDIRECT_RESPONSE_CODE = 302;

    private $responseCode = self::DEFAULT_RESPONSE_CODE;
    private $headers = [];
    private $cookies = [];
    private $body = '';
    private $redirect = null;
    private $meta = [];

    private $responseCodes = [
        // 1xx: Informational - Request received, continuing process
        100 => "Continue",                       // [RFC7231, Section 6.2.1]
        101 => "Switching Protocols",            // [RFC7231, Section 6.2.2]
        102 => "Processing",                     // [RFC2518]
        // 2xx: Success - The action was successfully received, understood, and accepted
        200 => "OK",                             // [RFC7231, Section 6.3.1]
        201 => "Created",                        // [RFC7231, Section 6.3.2]
        202 => "Accepted",                       // [RFC7231, Section 6.3.3]
        203 => "Non-Authoritative Information",  // [RFC7231, Section 6.3.4]
        204 => "No Content",                     // [RFC7231, Section 6.3.5]
        205 => "Reset Content",                  // [RFC7231, Section 6.3.6]
        206 => "Partial Content",                // [RFC7233, Section 4.1]
        207 => "Multi-Status",                   // [RFC4918]
        208 => "Already Reported",               // [RFC5842]
        226 => "IM Used",                        // [RFC3229]
        // 3xx: Redirection - Further action must be taken in order to complete the request
        300 => "Multiple Choices",               // [RFC7231, Section 6.4.1]
        301 => "Moved Permanently",              // [RFC7231, Section 6.4.2]
        302 => "Found",                          // [RFC7231, Section 6.4.3]
        303 => "See Other",                      // [RFC7231, Section 6.4.4]
        304 => "Not Modified",                   // [RFC7232, Section 4.1]
        305 => "Use Proxy",                      // [RFC7231, Section 6.4.5]
        306 => "Unused",                         // [RFC7231, Section 6.4.6]
        307 => "Temporary Redirect",             // [RFC7231, Section 6.4.7]
        308 => "Permanent Redirect",             // [RFC7238]
        // 4xx: Client Error - The request contains bad syntax or cannot be fulfilled
        400 => "Bad Request",                    // [RFC7231, Section 6.5.1]
        401 => "Unauthorized",                   // [RFC7235, Section 3.1]
        402 => "Payment Required",               // [RFC7231, Section 6.5.2]
        403 => "Forbidden",                      // [RFC7231, Section 6.5.3]
        404 => "Not Found",                      // [RFC7231, Section 6.5.4]
        405 => "Method Not Allowed",             // [RFC7231, Section 6.5.5]
        406 => "Not Acceptable",                 // [RFC7231, Section 6.5.6]
        407 => "Proxy Authentication Required",  // [RFC7235, Section 3.2]
        408 => "Request Timeout",                // [RFC7231, Section 6.5.7]
        409 => "Conflict",                       // [RFC7231, Section 6.5.8]
        410 => "Gone",                           // [RFC7231, Section 6.5.9]
        411 => "Length Required",                // [RFC7231, Section 6.5.10]
        412 => "Precondition Failed",            // [RFC7232, Section 4.2]
        413 => "Payload Too Large",              // [RFC7231, Section 6.5.11]
        414 => "URI Too Long",                   // [RFC7231, Section 6.5.12]
        415 => "Unsupported Media Type",         // [RFC7231, Section 6.5.13]
        416 => "Range Not Satisfiable",          // [RFC7233, Section 4.4]
        417 => "Expectation Failed",             // [RFC7231, Section 6.5.14]
        422 => "Unprocessable Entity",           // [RFC4918]
        423 => "Locked",                         // [RFC4918]
        424 => "Failed Dependency",              // [RFC4918]
        426 => "Upgrade Required",               // [RFC7231, Section 6.5.15]
        428 => "Precondition Required",          // [RFC6585]
        429 => "Too Many Requests",              // [RFC6585]
        431 => "Request Header Fields Too Large",// [RFC6585]
        // 5xx: Server Error - The server failed to fulfill an apparently valid request
        500 => "Internal Server Error",          // [RFC7231, Section 6.6.1]
        501 => "Not Implemented",                // [RFC7231, Section 6.6.2]
        502 => "Bad Gateway",                    // [RFC7231, Section 6.6.3]
        503 => "Service Unavailable",            // [RFC7231, Section 6.6.4]
        504 => "Gateway Timeout",                // [RFC7231, Section 6.6.5]
        505 => "HTTP Version Not Supported",     // [RFC7231, Section 6.6.6]
        506 => "Variant Also Negotiates",        // [RFC2295]
        507 => "Insufficient Storage",           // [RFC4918]
        508 => "Loop Detected",                  // [RFC5842]
        510 => "Not Extended",                   // [RFC2774]
        511 => "Network Authentication Required" // [RFC6585]
    ];

    public static function create(): Response
    {
        return new self();
    }

    public function setHeader(string $name, string $value): Response
    {
        $normalizedName = $this->normalizeHeaderName($name);

        if (!array_key_exists($normalizedName, $this->headers)) {
            $this->headers[$normalizedName] = $value;
        } else if (is_array($this->headers[$normalizedName])) {
            $this->headers[$normalizedName][] = $value;
        } else {
            $this->headers[$normalizedName] = [$this->headers[$normalizedName], $value];
        }

        return $this;
    }

    public function setCookie(string $name, string $value): Response
    {
        $this->cookies[$name] = $value;

        return $this;
    }

    public function setResponseCode(int $responseCode): Response
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function setBody($body): Response
    {
        $this->body = $body;

        return $this;
    }

    public function setRedirect(string $url): Response
    {
        $this->redirect = $url;

        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function sendTo(Backend $backend): void
    {
        $backend->sendResponseCode($this->responseCode);

        foreach ($this->headers as $name => $value) {
            $backend->sendHeader($name, $value);
        }

        foreach ($this->cookies as $name => $value) {
            $backend->sendCookie($name, $value);
        }

        $backend->sendBody($this->body);
    }

    private function isJsonBody(): bool
    {
        return is_array($this->body) || $this->body instanceof \JsonSerializable;
    }

    public function prepare(Request $request): void
    {
        $this->normalizeHeaders();

        if ($request->getMethod() === 'HEAD') {
            $this->setBody('');
        }

        if (!is_null($this->redirect)) {
            $this->setResponseCode(self::REDIRECT_RESPONSE_CODE)
                 ->setHeader('Location', $this->redirect);
        }

        if ($this->isJsonBody()) {
            $this->body = json_encode($this->body);
            if (!$this->hasHeader('Content-Type')) {
                $this->setHeader('Content-Type', 'application/json');
            }
        }

        if ($this->isErrorResponse() && $this->hasNoBody()) {
            $this->setBody($this->responseCodes[$this->getResponseCode()]);
        }
    }

    private function normalizeHeaders(): void
    {
        $keys = array_keys($this->headers);
        $this->headers = array_reduce($keys, function ($acc, $key) {
            $headerValue = $this->headers[$key];
            $acc[$key] = $this->normalizeHeaderValue($headerValue);
            return $acc;
        }, []);
    }

    private function normalizeHeaderName($header): string
    {
        return implode('-', array_map('ucfirst', explode('-', $header)));
    }

    private function normalizeHeaderValue($value): string
    {
        return is_array($value) ? implode(';', $value) : $value;
    }

    public function hasHeader($name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    public function hasNoBody(): bool
    {
        return empty($this->body);
    }

    public function isErrorResponse(): bool
    {
        return $this->responseCode >= 400;
    }

    public function isOkResponse(): bool
    {
        return ! $this->isErrorResponse();
    }
}
