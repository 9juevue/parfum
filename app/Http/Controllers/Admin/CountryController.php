<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Country\StoreRequest;
use App\Http\Requests\Admin\Country\UpdateRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Services\Admin\CountryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService
    )
    {
    }

    public function index()
    {
        $countries = Country::with('media')->get();

        return Inertia::render('Admin/Country/Index', [
            'countries' => CountryResource::collection($countries)->resolve(),
        ]);
    }


    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request): array
    {
        $data = $request->validated();
        $country = $this->countryService->store($data);

        return CountryResource::make($country->load('media'))->resolve();
    }

    public function show(string $id)
    {
        $country = Country::with('media')->findOrFail($id);

        return CountryResource::make($country)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $country = $this->countryService->update($id, $data);

        return CountryResource::make($country->load('media'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->countryService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
