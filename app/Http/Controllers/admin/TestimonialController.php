<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderby('created_at', 'DESC')->paginate(5);

        return response()->json([
            'status' => true,
            'data' => $testimonials
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required',
            'designation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $testimonial = new Testimonial();
        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->designation = $request->designation;
        $testimonial->status = $request->status;
        $testimonial->save();

        // Save Temp Image Here
        if ($request->imageId > 0) {

            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $testimonial->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);

                $destPath = public_path('uploads/testimonials/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(300, 300);
                $image->save($destPath);

                $testimonial->image = $fileName;
                $testimonial->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Testimonials Added Successfully"
        ]);
    }

    public function show(string $id){

        $testimonial = Testimonial::find($id);

        if ($testimonial == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Testimonial Not Found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $testimonial
        ]);
    }


    public function update(Request $request, string $id){

        $testimonial = Testimonial::find($id);

        $validator = Validator::make($request->all(), [
            'testimonial' => 'required',
            'citation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $testimonial->testimonial = $request->testimonial;
        $testimonial->citation = $request->citation;
        $testimonial->designation = $request->designation;
        $testimonial->status = $request->status;
        $testimonial->save();

        // Save Temp Image Here
        if ($request->imageId > 0) {

            $oldImage = $testimonial->image;
            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $testimonial->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);

                $destPath = public_path('uploads/testimonials/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(300, 300);
                $image->save($destPath);

                $testimonial->image = $fileName;
                $testimonial->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/testimonials/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Testimonials Updated Successfully"
        ]);

    }


    public function destroy(string $id){

        $testimonial = Testimonial::find($id);

        $oldImage = $testimonial->image;

        if ($testimonial == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Testimonial Not Found'
            ]);
        }

        if ($oldImage != null) {

            File::delete(public_path('uploads/testimonials/' . $oldImage));
        }

        $testimonial->delete();

        return response()->json([
            'status' => true,
            'message' => "Testimonial Deleted Successfully"
        ]);
    }
}
