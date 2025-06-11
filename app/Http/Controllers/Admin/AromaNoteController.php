<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AromaNote\StoreRequest;
use App\Http\Requests\Admin\AromaNote\UpdateRequest;
use App\Http\Resources\AromaNoteResource;
use App\Http\Resources\NoteGroupingResource;
use App\Models\AromaNote;
use App\Models\NoteGrouping;
use App\Services\Admin\AromaNoteService;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AromaNoteController extends Controller
{
    public function __construct(
        protected AromaNoteService $aromaNoteService
    )
    {
    }

    public function index()
    {
        $aromaNotes = AromaNote::with('media', 'noteGrouping')->get();
        $noteGroupings = NoteGrouping::all();

        return Inertia::render('Admin/AromaNote/Index', [
            'aromaNotes' => AromaNoteResource::collection($aromaNotes)->resolve(),
            'noteGroupings' => NoteGroupingResource::collection($noteGroupings)->resolve(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $aromaNote = $this->aromaNoteService->store($data);

        return AromaNoteResource::make($aromaNote->load('media', 'noteGrouping'))->resolve();
    }


    public function show(string $id)
    {
        $aromaNote = AromaNote::with('media', 'noteGrouping')->findOrFail($id);

        return AromaNoteResource::make($aromaNote)->resolve();
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $aromaNote = $this->aromaNoteService->update($id, $data);

        return AromaNoteResource::make($aromaNote->load('media', 'noteGrouping'))->resolve();
    }

    public function destroy(string $id)
    {
        $deleted = $this->aromaNoteService->delete($id);

        if ($deleted) {
            return response()->noContent();
        }

        return response()->json([
            'message' => 'Сущность не найдена'
        ], Response::HTTP_NOT_FOUND);
    }
}
