<?php

namespace App\Http\Controllers\admin;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::orderby('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $services
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|Unique:services,slug'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $service = new Service();
        $service->title = $request->title;
        $service->short_desc = $request->short_desc;
        $service->slug = Str::slug($request->slug);
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();


         // Save Temp Image Here
         if ($request->imageId > 0) {

            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $service->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);
                $destPath = public_path('uploads/services/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Create large thumbnail here
                $destPath = public_path('uploads/services/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $service->image = $fileName;
                $service->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Service Added Successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::find($id);

        if ($service == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Service Not Found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $service
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $service = Service::findOrFail($id);

        if ($service == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Service Not Found'
            ]);
        }
        $request->merge(['slug' => Str::slug($request->slug)]);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug,' . $id . ',id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $service->title = $request->title;
        $service->short_desc = $request->short_desc;
        $service->slug = Str::slug($request->slug);
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();


        // Save Temp Image Here
        if ($request->imageId > 0) {

            $oldImage = $service->image;
            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $service->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);
                $destPath = public_path('uploads/services/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Create large thumbnail here
                $destPath = public_path('uploads/services/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $service->image = $fileName;
                $service->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/services/large/' . $oldImage));
                    File::delete(public_path('uploads/services/small/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Service Updated Successfully"
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::find($id);

        $oldImage = $service->image;

        if ($service == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Service Not Found'
            ]);
        }

        if ($oldImage != null) {

            File::delete(public_path('uploads/services/large/' . $oldImage));
            File::delete(public_path('uploads/services/small/' . $oldImage));
        }

        $service->delete();

        return response()->json([
            'status' => true,
            'message' => "Service Deleted Successfully"
        ]);
    }
}
