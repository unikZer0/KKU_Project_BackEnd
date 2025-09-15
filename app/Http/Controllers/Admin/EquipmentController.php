<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with('category')->get();
        $categories = Category::all();
        return view('admin.equipment.index', compact('equipments', 'categories'));
    }

    public function store(Request $request)
{
    try {
        $data = $request->validate([
            "code" => "nullable|string|unique:equipments,code|max:10",
            "name" => "required|string",
            "description" => "nullable|string",
            "categories_id" => "required|integer|exists:categories,id",
            "status" => "required|in:available,retired,maintenance",
            "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120",
        ]);

        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('equipments', 'public');
                $paths[] = '/storage/' . $path;
            }
        }

        $data['photo_path'] = json_encode($paths);

        $equipment = Equipment::create($data);

        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'create',
            'target_type' => 'equipment',
            'target_id' => $equipment->id,
            'description' => "Created equipment: {$equipment->name} (ID {$equipment->id})",
        ]);

        return response()->json([
            "status" => true,
            "message" => "Equipment created successfully",
            "data" => $equipment->load('category')
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        $errors = $e->errors();
        $errorMessages = [];
        foreach ($errors as $field => $messages) {
            $errorMessages = array_merge($errorMessages, $messages);
        }
        
        return response()->json([
            "status" => false,
            "message" => implode(', ', $errorMessages),
            "errors" => $errors
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => "Server error: " . $e->getMessage()
        ], 500);
    }
}

 public function update(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validatedData = $request->validate([
                "code" => "nullable|string|max:10",
                "name" => "required|string",
                "description" => "nullable|string",
                "categories_id" => "required|integer|exists:categories,id",
                "status" => "required|in:available,retired,maintenance",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,gif|max:5120", 
                "images_to_delete" => "nullable|array",
                "images_to_delete.*" => "string", 
                "selected_main_identifier" => "nullable|string", 
            ]);

            $currentPhotos = json_decode($equipment->photo_path ?? '[]', true);

            if ($request->filled('images_to_delete')) {
                $imagesToDelete = $request->input('images_to_delete');
                $currentPhotos = array_values(array_diff($currentPhotos, $imagesToDelete));
                foreach ($imagesToDelete as $imageUrl) {
                    $path = str_replace('/storage/', '', $imageUrl);
                    Storage::disk('public')->delete($path);
                }
            }
            $newlyUploadedPhotos = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('equipments', 'public');
                    $newlyUploadedPhotos[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => '/storage/' . $path,
                    ];
                }
            }
            $newPhotoPaths = array_column($newlyUploadedPhotos, 'path');
            $finalPhotoList = array_merge($currentPhotos, $newPhotoPaths);
            
            $mainPhotoIdentifier = $request->input('selected_main_identifier');
            $mainPhotoUrl = null;

            if ($mainPhotoIdentifier) {
                if (in_array($mainPhotoIdentifier, $finalPhotoList)) {
                    $mainPhotoUrl = $mainPhotoIdentifier;
                } else {
                    foreach ($newlyUploadedPhotos as $uploadedPhoto) {
                        if ($uploadedPhoto['original_name'] === $mainPhotoIdentifier) {
                            $mainPhotoUrl = $uploadedPhoto['path'];
                            break;
                        }
                    }
                }
            }

            if ($mainPhotoUrl) {
                $finalPhotoList = array_values(array_diff($finalPhotoList, [$mainPhotoUrl]));
                array_unshift($finalPhotoList, $mainPhotoUrl);
            }

            $updatePayload = $validatedData;
            $updatePayload['photo_path'] = json_encode(array_values($finalPhotoList));

            unset($updatePayload['images'], $updatePayload['images_to_delete'], $updatePayload['selected_main_identifier']);
            
            $equipment->update($updatePayload);

            return response()->json([
                "status" => true,
                "message" => "Equipment updated successfully",
                "data" => $equipment->fresh()->load('category')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => false,
                "message" => "Validation error",
                "errors" => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Server error: " . $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);

        if ($equipment->photo_path) {
            $oldPath = str_replace('/storage/', '', $equipment->photo_path);
            Storage::disk('public')->delete($oldPath);
        }

        Log::create([
            'admin_id' => Auth::id() ?? 1,
            'action' => 'delete',
            'target_type' => 'equipment',
            'target_id' => $id,
            'description' => "Deleted equipment: {$equipment->name} (ID {$equipment->code})",
        ]);

        $equipment->delete();

        return response()->json([
            "status" => true,
            "message" => "Equipment deleted successfully"
        ]);
    }
}
