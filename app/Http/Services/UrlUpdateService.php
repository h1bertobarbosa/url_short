<?php


namespace App\Http\Services;
use App\Exceptions\JsonValidationException;
use App\Url;
use Illuminate\Http\Request;

class UrlUpdateService
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
        $currentUser = $this->request->request->get('user');
        $data = $this->request->only(['original_url', 'url_code']);

        if(!empty($data['url_code']) && strlen($data['url_code']) > 16) {
            throw new JsonValidationException('This code must have a maximum of 16 characters');
        }

        $url = Url::where('id', $this->codeOrId)->orWhere('url_code', $this->codeOrId)->first();

        if(!$url) {
            throw new JsonValidationException('Data does not exist.');
        }

        if($url->user_name != $currentUser) {
            throw new JsonValidationException('You not is owner from this data', 401);
        }

        if(!empty($data['url_code'])) {
            $urlExists = Url::where('url_code', $data['url_code'])->where('id', '!=', $url->id)->first();
            if($urlExists) {
                throw new JsonValidationException('This code already exists in another record');
            }
        }

        $url->fill($data);
        $url->save();

        return $url->toArray();
    }
}
