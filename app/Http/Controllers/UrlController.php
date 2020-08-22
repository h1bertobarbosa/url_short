<?php

namespace App\Http\Controllers;

use App\Exceptions\JsonValidationException;
use App\Http\Services\UrlDestroyService;
use App\Http\Services\UrlIndexService;
use App\Http\Services\UrlShowService;
use App\Http\Services\UrlStoreService;
use App\Http\Services\UrlUpdateService;
use Illuminate\Http\Request;


class UrlController extends Controller
{
    public function index($code)
    {
        try {
            $indexService = new UrlIndexService($code);
            $originalUrl = $indexService->execute();

            return response()->redirectTo($originalUrl, 301);
        } catch (JsonValidationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function store(Request $request)
    {
        try {
            $storeService = new UrlStoreService($request);
            $responseData = $storeService->execute();

            return response()->json($responseData, 201);
        } catch (JsonValidationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function show(Request $request, $codeOrId)
    {
        try {
            $destroyService = new UrlShowService($request, $codeOrId);
            $responseData = $destroyService->execute();

            return response()->json($responseData);
        } catch (JsonValidationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function update(Request $request, $codeOrId)
    {
        try {
            $updateService = new UrlUpdateService($request, $codeOrId);
            $responseData = $updateService->execute();

            return response()->json($responseData);
        } catch (JsonValidationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy(Request $request, $codeOrId)
    {
        try {
            $destroyService = new UrlDestroyService($request, $codeOrId);
            $destroyService->execute();

            return response('', 204);
        } catch (JsonValidationException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
