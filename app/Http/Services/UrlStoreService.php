<?php


namespace App\Http\Services;
use App\Exceptions\JsonValidationException;
use App\Url;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UrlStoreService
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        $data = $this->request->only(['url']);
        $currentUser = $this->request->request->get('user');

        $validator = Validator::make($data, [
            'url' => 'required|active_url'
        ]);

        if ($validator->fails()) {
            throw new JsonValidationException($validator->errors()->first());
        }

        $createdUrl = Url::create([
            'user_name' => $currentUser,
            'original_url' => $data['url'],
            'url_code' => Str::random(16),
            'clicks' => 0
        ]);

        return [
            'id' => $createdUrl->id,
            'original_url' => $createdUrl->original_url,
            'minified_url' => $this->request->getSchemeAndHttpHost().'/'.$createdUrl->url_code
        ];
    }
}
