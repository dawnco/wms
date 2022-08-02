<?php
/**
 * @author Dawnc
 * @date   2022-08-01
 */

namespace Wms\Lib;


class HttpClient
{

    private string $url = '';
    private int $status = 0;
    private $ch = null;

    /**
     * @var string[]
     */
    private array $requestHeader = [];
    private string $requestHeaderFromRequest = '';

    private string $requestBody = '';
    private string $responseHeader = '';
    private int $responseStatus = 0;
    private string $responseBody = '';
    public string $body = '';
    public string $error = '';

    public function __construct($url)
    {
        $this->url = $url;
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        $this->default();


    }

    private function default(): void
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:31.0) Gecko/20100101 Firefox/31.0");

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 1);
    }

    /**
     * @param array $header Content-type: text/plain   Content-length: 100
     */
    public function setHeader($header = ''): self
    {
        //
        $this->requestHeader[] = $header;
        return $this;
    }


    /**
     * @param array $header 例如  [
     *                      "bodyrawsize" => "1",
     *                      "apiversion" => "2",
     *                      "signaturemethod" => "3",
     *                      ]
     */
    public function setHeaderArr(array $header = []): void
    {
        foreach ($header as $k => $v) {
            $this->setHeader("$k: $v");
        }
    }


    public function setTimeout($second = 10): self
    {
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $second);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $second);
        return $this;
    }


    public function setData(string $data): self
    {
        $this->requestBody = $data;
        return $this;
    }

    /**
     * @param string $method "GET"，"POST"，"CONNECT等
     */
    public function setMethod(string $method = "GET"): self
    {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        return $this;
    }

    /**
     * @param $header [["content-type"=>"xxx"]]
     * @return void
     */
    public function setHeaders($header): self
    {
        $this->setHeaderArr($header);
        return $this;
    }

    public function request(): self
    {

        if ($this->requestHeader) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->requestHeader);
        }


        if ($this->requestBody) {
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->requestBody);
        }

        $body = curl_exec($this->ch);
        $info = curl_getinfo($this->ch);


        $this->error = curl_error($this->ch);

        $header_size = $info['header_size'];
        $request_size = $info['request_size'];

        $this->responseHeader = substr($body, 0, $header_size);
        $this->requestHeaderFromRequest = $info['request_header'] ?? '';
        $this->responseBody = substr($body, $header_size);
        $this->body = $this->responseBody;
        $httpStatus = $info['http_code'];

        $this->responseStatus = $httpStatus;

        return $this;
    }


    public function execute($fullPath = ""): self
    {
        if ($fullPath) {
            $this->url = $this->url . $fullPath;
        }
        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        return $this->request();
    }

    public function getBody(): string
    {
        return $this->responseBody;
    }

    public function getHeader(): string
    {
        return $this->responseHeader;
    }

    public function getResponseHeaderArr(): array
    {
        $arr = explode("\r\n", $this->responseHeader);
        $h = [];
        foreach ($arr as $v) {
            $t = explode(":", $v);
            $h[$t[0]] = $t[1] ?? '';
        }
        return $h;
    }

    public function getStatus(): int
    {
        return $this->responseStatus;
    }

    public function getStatusCode(): int
    {
        return $this->getStatus();
    }


    public function getResponseStatus(): int
    {
        return $this->getStatus();
    }

    public function getRequestHeader(): array
    {
        return $this->requestHeader;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function close()
    {
        curl_close($this->ch);
        $this->ch = null;
    }

    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getRequestHeaderFromRequest(): string
    {
        return $this->requestHeaderFromRequest;
    }
}
