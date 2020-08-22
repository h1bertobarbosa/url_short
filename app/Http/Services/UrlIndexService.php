<?php


namespace App\Http\Services;
use App\Exceptions\JsonValidationException;
use App\Url;

class UrlIndexService
{
    private $urlCode;

    public function __construct($code)
    {
        $this->urlCode = $code;
    }

    public function execute()
    {
        $url = Url::where('url_code', $this->urlCode)->first();

        if(!$url) {
            throw new JsonValidationException('Data does not exist.');
        }

        $url->increment('clicks');

        return $url->original_url;
    }
}
