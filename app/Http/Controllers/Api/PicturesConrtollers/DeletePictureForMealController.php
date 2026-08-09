<?php

namespace App\Http\Controllers\Api\PicturesConrtollers;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeletePictureForMealController extends Controller
{
    public function destroy($MealId)
    {
        $Meal = Meal::find($MealId);
        if (!$Meal) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'An error occurred, Undefined Meal.',
                'status' => 400
            ], 400);
        }

        $photo = $Meal->picture;
        if (!$photo) {
            return response()->json([
                'data' => null,
                'success' => false,
                'message' => 'An error occurred, Undefined photo.',
                'status' => 400
            ], 400);
        }

        // بناء المسار الكامل للصورة في نظام الملفات
        $filePath = public_path($photo->path); // لأن المسار في قاعدة البيانات يبدأ بـ 'storage/photos/...'

        // حذف الملف عن طريق unlink إذا كان موجود
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $Meal->picture_id = null;
        $Meal->save();

        if ($photo->delete()) {
            return response()->json([
                'data' => null,
                'success' => true,
                'message' => 'Picture deleted successfully.',
                'status' => 200
            ], 200);
        }

        return response()->json([
            'data' => null,
            'success' => false,
            'message' => 'Failed to delete picture record.',
            'status' => 500
        ], 500);
    }
}
