<?php


namespace App\Http\Services;

use App\Exceptions\JsonValidationException;
use App\Url;
use Illuminate\Http\Request;

class UrlShowService
{
    private $request;
    private $codeOrId;

    public function __construct(Request $request, $codeOrId)
    {
        $this->request = $request;
        $this->codeOrId = $codeOrId;
    }

    public function execute()
    {
        $url = Url::where('id', $this->codeOrId)->orWhere('url_code', $this->codeOrId)->first();

        if(!$url) {
            throw new JsonValidationException('Data does not exist.');
        }

        return array_merge(['minified_url' => $this->request->getSchemeAndHttpHost().'/'.$url->url_code], $url->toArray());
    }
}
