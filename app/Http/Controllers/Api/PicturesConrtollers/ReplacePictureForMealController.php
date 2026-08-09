<?php

namespace App\Http\Controllers\Api\PicturesConrtollers;

use Illuminate\Http\Request;
use App\Models\Picture;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ReplacePictureForMealController extends Controller
{
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'meal_id' => 'required|exists:meals,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => $validator->errors(),
                'status' => 422,
            ], 422);
        }

        $Meal = Meal::find($request->meal_id);

        if (!$Meal) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'An error occurred, Undefined Meal.',
                'status' => 404,
            ], 404);
        }

        // حذف الصورة القديمة لو موجودة، مع تحديث الـ picture_id إلى null أولاً
        if ($Meal->picture) {
            $oldPhoto = $Meal->picture;

            // فصل العلاقة أولاً لتجنب مشاكل الـ foreign key
            $Meal->picture_id = null;
            $Meal->save();

            $oldPhotoPath = public_path($oldPhoto->path);
            if (File::exists($oldPhotoPath)) {
                File::delete($oldPhotoPath);
            }

            $oldPhoto->delete();
        }

        $extension = $request->file('photo')->extension();
        $randomName = Str::random(40);
        $fileName = $randomName . '.' . $extension;

        $destinationPath = public_path('storage/photos');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $saved = $request->file('photo')->move($destinationPath, $fileName);

        if (!$saved) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'An error occurred, You can try again later.',
                'status' => 400,
            ], 400);
        }

        $relativePath = 'storage/photos/' . $fileName;

        $photoModel = Picture::create([
            'name' => $randomName,
            'path' => $relativePath,
        ]);

        if (!$photoModel) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'An error occurred, You can try again later.',
                'status' => 400,
            ], 400);
        }

        // تحديث picture_id داخل جدول الوجبات
        $Meal->picture_id = $photoModel->id;
        $Meal->save();

        try {
            $photoModel['url'] = asset($relativePath);

            $mealData = $Meal->toArray();
            $mealData['photo'] = $photoModel;

            return response()->json([
                'success' => true,
                'message' => 'Picture uploaded successfully.',
                'status' => 201,
                'data' => $mealData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Server error occurred:' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.',
                'data' => null,
                'status' => 500
            ], 500);
        }
    }
}
