<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brand\StoreRequest;
use App\Http\Requests\Admin\Brand\UpdateRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CountryResource;
use App\Models\Brand;
use App\Models\Country;
use App\Services\Admin\BrandService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    )
    {
    }

    public function index()
    {
        $brands = Brand::with('media', 'country', 'synonyms')->get();
        $countries = Country::all();

        return Inertia::render('Admin/Brand/Index', [
            'brands' => BrandResource::collection($brands)->resolve(),
            'countries' => CountryResource::collection($countries)->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $brand = $this->brandService->store($data);

        return BrandResource::make($brand->load('media', 'country', 'synonyms'))->resolve();
    }


    public function show(string $id)
    {
        $brand = Brand::with('media', 'country', 'synonyms')->findOrFail($id);

        return BrandResource::make($brand)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $brand = $this->brandService->update($id, $data);

        return BrandResource::make($brand->load('media', 'country', 'synonyms'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->brandService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
