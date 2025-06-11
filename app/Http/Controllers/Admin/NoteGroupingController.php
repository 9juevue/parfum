<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoteGrouping\StoreRequest;
use App\Http\Requests\Admin\NoteGrouping\UpdateRequest;
use App\Http\Resources\NoteGroupingResource;
use App\Http\Resources\CountryResource;
use App\Models\NoteGrouping;
use App\Models\Country;
use App\Services\Admin\NoteGroupingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NoteGroupingController extends Controller
{
    public function __construct(
        protected NoteGroupingService $noteGroupingService
    )
    {
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $noteGrouping = $this->noteGroupingService->store($data);

        return NoteGroupingResource::make($noteGrouping)->resolve();
    }


    public function show(string $id)
    {
        $noteGrouping = NoteGrouping::query()->findOrFail($id);

        return NoteGroupingResource::make($noteGrouping)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $noteGrouping = $this->noteGroupingService->update($id, $data);

        return NoteGroupingResource::make($noteGrouping)->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->noteGroupingService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
