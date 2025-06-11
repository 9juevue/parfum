<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Perfumer\StoreRequest;
use App\Http\Requests\Admin\Perfumer\UpdateRequest;
use App\Http\Resources\PerfumerResource;
use App\Http\Resources\CountryResource;
use App\Models\Perfumer;
use App\Models\Country;
use App\Services\Admin\PerfumerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PerfumerController extends Controller
{
    public function __construct(
        protected PerfumerService $perfumerService
    )
    {
    }

    public function index()
    {
        $perfumers = Perfumer::with('media')->get();
        $countries = Country::all();

        return Inertia::render('Admin/Perfumer/Index', [
            'perfumers' => PerfumerResource::collection($perfumers)->resolve(),
            'countries' => CountryResource::collection($countries)->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $perfumer = $this->perfumerService->store($data);

        return PerfumerResource::make($perfumer->load('media', 'country', 'synonyms'))->resolve();
    }


    public function show(string $id)
    {
        $perfumer = Perfumer::with('media', 'country', 'synonyms')->findOrFail($id);

        return PerfumerResource::make($perfumer)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $perfumer = $this->perfumerService->update($id, $data);

        return PerfumerResource::make($perfumer->load('media', 'country', 'synonyms'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->perfumerService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
