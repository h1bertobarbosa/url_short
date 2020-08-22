<?php

namespace App\Http\Controllers;

use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index($code)
    {
        $url = Url::where('url_code', $code)->first();

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        $url->increment('clicks');

        return response()->redirectTo($url->original_url, 301);
    }

    public function store(Request $request)
    {
        $data = $request->only(['url']);
        $currentUser = $request->request->get('user');

        $validator = Validator::make($data, [
            'url' => 'required|active_url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $createdUrl = Url::create([
            'user_name' => $currentUser,
            'original_url' => $data['url'],
            'url_code' => Str::random(16),
            'clicks' => 0
        ]);

        return response()->json([
            'id' => $createdUrl->id,
            'original_url' => $createdUrl->original_url,
            'minified_url' => $request->getHost().'/'.$createdUrl->url_code], 201);
    }

    public function show(Request $request, $codeOrId)
    {
        $url = Url::where('id', $codeOrId)->orWhere('url_code', $codeOrId)->first();

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        $responseData = array_merge(['minified_url' => $request->getHost().'/'.$url->url_code], $url->toArray());

        return response()->json($responseData);
    }

    public function update(Request $request, $codeOrId)
    {
        $currentUser = $request->request->get('user');
        $data = $request->only(['original_url', 'url_code']);

        if(!empty($data['url_code']) && strlen($data['url_code']) > 16) {
            return response()->json(['url_code' => 'This code must have a maximum of 16 characters'], 400);
        }

        $url = Url::where('id', $codeOrId)->orWhere('url_code', $codeOrId)->first();

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        if($url->user_name != $currentUser) {
            return response()->json(['error' => 'You not is owner from this data'], 401);
        }

        if(!empty($data['url_code'])) {
            $urlExists = Url::where('url_code', $data['url_code'])->where('id', '!=', $url->id)->first();
            if($urlExists) {
                return response()->json(['url_code' => 'This code already exists in another record'], 400);
            }
        }

        $url->fill($data);
        $url->save();
        return response()->json($url);
    }

    public function destroy(Request $request, $codeOrId)
    {
        $url = Url::where('id', $codeOrId)->orWhere('url_code', $codeOrId)->first();
        $currentUser = $request->request->get('user');

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        if($url->user_name != $currentUser) {
            return response()->json(['error' => 'You not is owner from this data'], 401);
        }

        $url->delete();
        return response('', 204);
    }
}
