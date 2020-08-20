<?php

namespace App\Http\Controllers;

use App\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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

        return response()->json($createdUrl, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string or int $codeOrId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($codeOrId)
    {
        $url = Url::where('id', $codeOrId)->orWhere('url_code', $codeOrId)->first();

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        return response()->json($url);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string or int $codeOrId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $codeOrId)
    {
        $url = Url::where('id', $codeOrId)->orWhere('url_code', $codeOrId)->first();

        if(!$url) {
            return response()->json(['error' => 'Data does not exist.'], 400);
        }

        $currentUser = $request->request->get('user');

        if($url->user_name != $currentUser) {
            return response()->json(['error' => 'You not is owner from this data'], 401);
        }

        $url->fill($request->only(['original_url', 'url_code']));
        $url->save();
        return response()->json($url);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string or int  $codeOrId
     * @return \Illuminate\Http\JsonResponse
     */
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
