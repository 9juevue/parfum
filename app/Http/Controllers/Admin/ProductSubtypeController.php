<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductSubtype\StoreRequest;
use App\Http\Requests\Admin\ProductSubtype\UpdateRequest;
use App\Http\Resources\ProductSubtypeResource;
use App\Http\Resources\CountryResource;
use App\Models\ProductSubtype;
use App\Models\Country;
use App\Services\Admin\ProductSubtypeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ProductSubtypeController extends Controller
{
    public function __construct(
        protected ProductSubtypeService $productSubtypeService
    )
    {
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $productSubtype = $this->productSubtypeService->store($data);

        return ProductSubtypeResource::make($productSubtype->load('productSubtypeFormat'))->resolve();
    }


    public function show(string $id)
    {
        $productSubtype = ProductSubtype::query()->findOrFail($id);

        return ProductSubtypeResource::make($productSubtype)->resolve();
    }

    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $productSubtype = $this->productSubtypeService->update($id, $data);

        return ProductSubtypeResource::make($productSubtype)->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->productSubtypeService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
