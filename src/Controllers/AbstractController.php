<?php

namespace MaximusApi\Controllers;

use Illuminate\Http\Request;
use MaximusApi\Service\ApiService;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        return $this->apiService->all();
    }

    public function show($id)
    {
        $record = $this->apiService->find($id);

        if (!$record) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($record, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $record = $this->apiService->create($request);

        return response()->json($record, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $record = $this->apiService->update($request, $id);

        if (!$record) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($record, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $result = $this->apiService->delete($id);

        if (!$result) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['message' => 'Record deleted successfully'], Response::HTTP_OK);
    }

    public function search(Request $request)
    {
        return $this->apiService->search($request);
    }

    public function withRelations(Request $request)
    {
        return $this->apiService->withRelations($request);
    }

    public function searchWith(Request $request)
    {
        return $this->apiService->searchWith($request);
    }


}
