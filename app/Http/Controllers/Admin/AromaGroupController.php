<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AromaGroup\StoreRequest;
use App\Http\Requests\Admin\AromaGroup\UpdateRequest;
use App\Http\Resources\AromaGroupResource;
use App\Http\Resources\CountryResource;
use App\Models\AromaGroup;
use App\Models\Country;
use App\Services\Admin\AromaGroupService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AromaGroupController extends Controller
{
    public function __construct(
        protected AromaGroupService $aromaGroupService
    )
    {
    }

    public function index()
    {
        $aromaGroups = AromaGroup::with('media')->get();

        return Inertia::render('Admin/AromaGroup/Index', [
            'aromaGroups' => AromaGroupResource::collection($aromaGroups)->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $aromaGroup = $this->aromaGroupService->store($data);

        return AromaGroupResource::make($aromaGroup->load('media'))->resolve();
    }


    public function show(string $id)
    {
        $aromaGroup = AromaGroup::with('media')->findOrFail($id);

        return AromaGroupResource::make($aromaGroup)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $aromaGroup = $this->aromaGroupService->update($id, $data);

        return AromaGroupResource::make($aromaGroup->load('media'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->aromaGroupService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
