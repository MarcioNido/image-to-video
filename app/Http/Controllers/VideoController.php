<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Jobs\ProcessVideo;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // @todo: not sure we need this endpoint
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVideoRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated["texts"] = json_decode($validated["texts"] ?? [], true);

        $video = $request->user()->videos()->create($validated);

        $images = $validated["images"];
        $i = 1;
        foreach ($images as $image) {
            /** @var UploadedFile $image */
            Storage::disk("workspace")->putFileAs(
                $video->id . "/uploaded-images",
                $image,
                $image->hashName()
            );

            $video->images()->create([
                "path" => Storage::disk("workspace")
                    ->path($video->id . "/uploaded-images/" . $image->hashName()),
                "order" => $i,
            ]);

            $i++;
        }

        $video->load("images");

        ProcessVideo::dispatch($video);

        return response()->json($video, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVideoRequest $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        //
    }
}
