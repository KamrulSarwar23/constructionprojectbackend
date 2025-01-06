<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;


class ProjectController extends Controller
{
    public function index(){
        $projects = Project::orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $projects
        ]);
    }

    public function store(Request $request){


        $request->merge(['slug' => Str::slug($request->slug)]);

       $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:projects,slug',
       ]);

       if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
       }

       $project = new Project();
       $project->title = $request->title;
       $project->slug = Str::slug($request->slug);
       $project->short_desc = $request->short_desc;
       $project->content = $request->content;
       $project->construction_type = $request->construction_type;
       $project->sector = $request->sector;
       $project->status = $request->status;
       $project->location = $request->location;
       $project->save();

        // Save Temp Image Here
        if ($request->imageId > 0) {

            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $project->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);
                $destPath = public_path('uploads/projects/small/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                // Create large thumbnail here
                $destPath = public_path('uploads/projects/large/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $project->image = $fileName;
                $project->save();
            }
        }

       return response()->json([
        'status' => true,
        'message' => 'Project Added Successfully'
    ]);

    }
}
