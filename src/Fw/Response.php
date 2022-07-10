<?php
/**
 * @author Dawnc
 * @date   2020-05-27
 */

namespace Wms\Fw;

class Response
{

    protected array $headers = [];
    protected string $body = '';
    protected int $status = 200;

    /**
     * Map of standard HTTP status code/reason phrases.
     * @var array<int, string>
     */
    private static array $phrases
        = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-status',
            208 => 'Already Reported',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            511 => 'Network Authentication Required',
        ];

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader($name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function getHeaderLine($name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * @param $code
     * @param $reasonPhrase
     * @return Response
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $new = clone $this;
        $new->status = $code;
        return $new;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return Response
     */
    public function withHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $normalized = strtolower($name);
        $new = clone $this;
        $new->headers[$normalized] = $value;
        return $new;

    }

    /**
     * @param                  $name
     * @param array|string|int $value
     * @return Response
     */
    public function withAddedHeader($name, $value): Response
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $new = clone $this;
        $normalized = strtolower($name);
        $new->headers[$normalized] = array_merge($new->headers[$normalized], $value);
        return $new;
    }

    public function withoutHeader($name): Response
    {
        $normalized = strtolower($name);
        $new = clone $this;
        unset($new->headers[$normalized]);
        return $new;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Response
     */
    public function withContent(string $body): Response
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }


    public static function getReasonPhraseByCode(int $code): string
    {
        return self::$phrases[$code] ?? '';
    }

}
