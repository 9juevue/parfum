<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Product\ProductCategoryEnum;
use App\Enums\Product\ProductGenderEnum;
use App\Enums\Product\ProductSubtypeCategoryEnum;
use App\Enums\Product\ProductSubtypeFormatEnum;
use App\Enums\Product\ProductSubtypeTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Http\Resources\AromaChordResource;
use App\Http\Resources\AromaGroupResource;
use App\Http\Resources\AromaNoteResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\PerfumerResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductSubtypeFormatResource;
use App\Http\Resources\ProductSubtypeResource;
use App\Models\AromaChord;
use App\Models\AromaGroup;
use App\Models\AromaNote;
use App\Models\Brand;
use App\Models\Perfumer;
use App\Models\Product;
use App\Models\ProductSubtype;
use App\Models\ProductSubtypeFormat;
use App\Services\Admin\BreadcrumbService;
use App\Services\Admin\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected BreadcrumbService $breadcrumbService,
    )
    {
    }

    public function index(Request $request)
    {
        $products = Product::with('media', 'brand', 'subtypes')->paginate(5);

        return Inertia::render('Admin/Product/Index', [
            'products' => ProductResource::collection($products)->resolve(),
            'productCategories' => ProductCategoryEnum::optionsByKey(),
            'productSubtypeCategories' => collect(ProductSubtypeCategoryEnum::optionsByKey())
                ->only(['perfume', 'bathline'])
                ->values()
                ->toArray(),
            'brands' => BrandResource::collection(Brand::all())->resolve(),
            'perfumers' => PerfumerResource::collection(Perfumer::all())->resolve(),
            'genders' => ProductGenderEnum::optionsByKey(),
            'aromaNotes' => AromaNoteResource::collection(AromaNote::with('noteGrouping')->get())->resolve(),
            'aromaGroups' => AromaGroupResource::collection(AromaGroup::all())->resolve(),
            'aromaChords' => AromaChordResource::collection(AromaChord::all())->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $product = $this->productService->store($data);

        return ProductResource::make($product->load('media'))->resolve();
    }

    public function edit(string $id)
    {
        $product = Product::with(
            'media',
            'subtypes',
            'brand',
            'synonyms',
            'perfumers',
            'aromaGroups',
            'aromaChords',
            'topNotes',
            'middleNotes',
            'baseNotes',
            'similarProducts',
            'similarTo'
        )->find($id);
        return Inertia::render('Admin/Product/Edit', [
            'product' => ProductResource::make($product)->resolve(),
            'productCategories' => ProductCategoryEnum::optionsByKey(),
            'productSubtypeTypes' => ProductSubtypeTypeEnum::optionsByKey(),
            'productSubtypeFormats' => ProductSubtypeFormatResource::collection(ProductSubtypeFormat::with('media')->get())->resolve(),
            'productSubtypeFormatTypes' => ProductSubtypeFormatEnum::optionsByKey(),
            'productSubtypeCategories' => collect(ProductSubtypeCategoryEnum::optionsByKey())
                ->only(['perfume', 'bathline'])
                ->values()
                ->toArray(),
            'brands' => BrandResource::collection(Brand::all())->resolve(),
            'perfumers' => PerfumerResource::collection(Perfumer::all())->resolve(),
            'genders' => ProductGenderEnum::optionsByKey(),
            'aromaNotes' => AromaNoteResource::collection(AromaNote::with('noteGrouping')->get())->resolve(),
            'aromaGroups' => AromaGroupResource::collection(AromaGroup::all())->resolve(),
            'aromaChords' => AromaChordResource::collection(AromaChord::all())->resolve(),
            'breadcrumbs' => $this->breadcrumbService->make($product)
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $product = $this->productService->update($id, $data);

        return ProductResource::make($product->load(
            'media',
            'brand',
            'synonyms',
            'perfumers',
            'aromaGroups',
            'aromaChords',
            'topNotes',
            'middleNotes',
            'baseNotes',
            'similarProducts',
            'similarTo'
        ))->resolve();
    }


    public function destroy(string $id)
    {
        $deleted = $this->productService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }

    public function filter(Request $request)
    {
        $query = Product::with('media', 'brand', 'subtypes');

        if ($search = $request->input('search')) {
            $query->where('title', 'LIKE', '%' . $search . '%');
        }

        if ($categoryType = $request->input('category_type')) {
            $query->where('category_type', $categoryType);
        }

        if ($brandIds = $request->input('brand_id')) {
            $query->whereIn('brand_id', $brandIds);
        }

        $sortField = $request->input('sortField');
        $sortOrder = $request->input('sortOrder');

        if ($sortField) {
            $direction = $sortOrder == 1 ? 'asc' : 'desc';

            if (in_array($sortField, ['title', 'is_active'])) {
                $query->orderBy($sortField, $direction);
            }
        }

        $perPage = $request->input('rows', 10);

        $products = $query->orderBy('title')->paginate($perPage);

        return ProductResource::collection($products);
    }
}
