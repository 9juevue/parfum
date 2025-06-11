<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Product\ProductSubtypeFormatEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductSubtypeFormat\StoreRequest;
use App\Http\Requests\Admin\ProductSubtypeFormat\UpdateRequest;
use App\Http\Resources\ProductSubtypeFormatResource;
use App\Http\Resources\CountryResource;
use App\Models\ProductSubtypeFormat;
use App\Models\Country;
use App\Services\Admin\ProductSubtypeFormatService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductSubtypeFormatController extends Controller
{
    public function __construct(
        protected ProductSubtypeFormatService $productSubtypeFormatService
    )
    {
    }

    public function index()
    {
        $productSubtypeFormats = ProductSubtypeFormat::with('media')->get();

        return Inertia::render('Admin/ProductSubtypeFormat/Index', [
            'productSubtypeFormats' => ProductSubtypeFormatResource::collection($productSubtypeFormats)->resolve(),
            'productSubtypeFormatTypes' => ProductSubtypeFormatEnum::optionsByKey(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $productSubtypeFormat = $this->productSubtypeFormatService->store($data);

        return ProductSubtypeFormatResource::make($productSubtypeFormat->load('media'))->resolve();
    }


    public function show(string $id)
    {
        $productSubtypeFormat = ProductSubtypeFormat::with('media')->findOrFail($id);

        return ProductSubtypeFormatResource::make($productSubtypeFormat)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $productSubtypeFormat = $this->productSubtypeFormatService->update($id, $data);

        return ProductSubtypeFormatResource::make($productSubtypeFormat->load('media'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->productSubtypeFormatService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }

    private function getAvailableSubtypeFormats()
    {
        $productSubtypeFormats = ProductSubtypeFormat::with('media')->get();
        $usedFormats = $productSubtypeFormats->map(function (ProductSubtypeFormat $productSubtypeFormat) {
            return $productSubtypeFormat->product_subtype_format->value;
        })->values()->toArray();

        return collect(ProductSubtypeFormatEnum::optionsByKey())
            ->filter(fn(array $item) => !in_array($item['value'], $usedFormats, true))
            ->toArray();
    }
}
