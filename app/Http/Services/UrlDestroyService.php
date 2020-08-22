<?php


namespace App\Http\Services;
use App\Exceptions\JsonValidationException;
use App\Url;
use Illuminate\Http\Request;

class UrlDestroyService
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
        $currentUser = $this->request->request->get('user');

        if(!$url) {
            throw new JsonValidationException('Data does not exist.');
        }

        if($url->user_name != $currentUser) {
            throw new JsonValidationException('You not is owner from this data', 401);
        }

        $url->delete();
    }
}
