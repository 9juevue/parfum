<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AromaChord\StoreRequest;
use App\Http\Requests\Admin\AromaChord\UpdateRequest;
use App\Http\Resources\AromaChordResource;
use App\Models\AromaChord;
use App\Services\Admin\AromaChordService;
use App\Services\Admin\BreadcrumbService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AromaChordController extends Controller
{
    public function __construct(
        protected AromaChordService $aromaChordService,
    )
    {
    }

    public function index()
    {
        $aromaChords = AromaChord::with('media')->get();

        return Inertia::render('Admin/AromaChord/Index', [
            'aromaChords' => AromaChordResource::collection($aromaChords)->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $aromaChord = $this->aromaChordService->store($data);

        return AromaChordResource::make($aromaChord->load('media'))->resolve();
    }


    public function show(string $id)
    {
        $aromaChord = AromaChord::with('media')->findOrFail($id);

        return AromaChordResource::make($aromaChord)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $aromaChord = $this->aromaChordService->update($id, $data);

        return AromaChordResource::make($aromaChord->load('media'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->aromaChordService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
