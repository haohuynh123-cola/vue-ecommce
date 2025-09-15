<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageIntervention;

class ImageController extends Controller
{
    /**
     * Display a listing of images.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $type = $request->get('type');
            $search = $request->get('search');

            $query = Image::with(['uploader'])
                ->when($type, fn($q) => $q->ofType($type))
                ->when($search, fn($q) => $q->where('original_name', 'like', "%{$search}%"))
                ->ordered();

            $images = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Images retrieved successfully',
                'data' => $images->items(),
                'meta' => [
                    'current_page' => $images->currentPage(),
                    'last_page' => $images->lastPage(),
                    'per_page' => $images->perPage(),
                    'total' => $images->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a new image.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
                'type' => 'required|string|in:thumbnail,gallery,banner,avatar,logo',
                'alt_text' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'is_public' => 'boolean',
            ]);

            $file = $request->file('image');
            $originalName = $file->getClientOriginalName();
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = 'images/' . date('Y/m/d') . '/' . $filename;

            // Store the original image
            $storedPath = $file->storeAs('images/' . date('Y/m/d'), $filename, 'public');

            // Get image dimensions
            $image = ImageIntervention::make($file);
            $dimensions = [
                'width' => $image->width(),
                'height' => $image->height(),
            ];

            // Create thumbnail
            $this->createThumbnail($image, $path);

            // Create medium size
            $this->createMediumSize($image, $path);

            // Create large size
            $this->createLargeSize($image, $path);

            // Save image record
            $imageRecord = Image::create([
                'filename' => $filename,
                'original_name' => $originalName,
                'path' => $storedPath,
                'url' => Storage::disk('public')->url($storedPath),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'type' => $request->type,
                'alt_text' => $request->alt_text,
                'description' => $request->description,
                'metadata' => [
                    'dimensions' => $dimensions,
                    'exif' => $image->exif(),
                ],
                'is_public' => $request->boolean('is_public', true),
                'uploaded_by' => auth()->id(),
                'disk' => 'public',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => $imageRecord->load('uploader')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified image.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $image = Image::with(['uploader'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Image retrieved successfully',
                'data' => $image
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified image.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $image = Image::findOrFail($id);

            $validated = $request->validate([
                'alt_text' => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'is_public' => 'sometimes|boolean',
                'sort_order' => 'sometimes|integer|min:0',
            ]);

            $image->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'data' => $image->load('uploader')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified image.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $image = Image::findOrFail($id);
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach image to a model.
     */
    public function attach(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image_id' => 'required|exists:images,id',
                'imageable_type' => 'required|string',
                'imageable_id' => 'required|integer',
                'type' => 'required|string|in:thumbnail,gallery,banner,avatar,logo',
                'is_primary' => 'boolean',
                'sort_order' => 'integer|min:0',
            ]);

            $image = Image::findOrFail($request->image_id);
            $model = $request->imageable_type::findOrFail($request->imageable_id);

            // Attach image to model
            $model->images()->attach($image->id, [
                'type' => $request->type,
                'is_primary' => $request->boolean('is_primary', false),
                'sort_order' => $request->get('sort_order', 0),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Image attached successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to attach image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach image from a model.
     */
    public function detach(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'image_id' => 'required|exists:images,id',
                'imageable_type' => 'required|string',
                'imageable_id' => 'required|integer',
            ]);

            $image = Image::findOrFail($request->image_id);
            $model = $request->imageable_type::findOrFail($request->imageable_id);

            // Detach image from model
            $model->images()->detach($image->id);

            return response()->json([
                'success' => true,
                'message' => 'Image detached successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to detach image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create thumbnail version of the image.
     */
    private function createThumbnail($image, string $path): void
    {
        $pathInfo = pathinfo($path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        $thumbnail = clone $image;
        $thumbnail->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
    }

    /**
     * Create medium size version of the image.
     */
    private function createMediumSize($image, string $path): void
    {
        $pathInfo = pathinfo($path);
        $mediumPath = $pathInfo['dirname'] . '/medium/' . $pathInfo['filename'] . '_medium.' . $pathInfo['extension'];

        $medium = clone $image;
        $medium->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($mediumPath, $medium->encode());
    }

    /**
     * Create large size version of the image.
     */
    private function createLargeSize($image, string $path): void
    {
        $pathInfo = pathinfo($path);
        $largePath = $pathInfo['dirname'] . '/large/' . $pathInfo['filename'] . '_large.' . $pathInfo['extension'];

        $large = clone $image;
        $large->resize(1200, 900, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::disk('public')->put($largePath, $large->encode());
    }
}
