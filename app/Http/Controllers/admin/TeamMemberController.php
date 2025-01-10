<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class TeamMemberController extends Controller
{
    public function index()
    {
        $teams = TeamMember::orderby('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $teams
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $team = new TeamMember();
        $team->name = $request->name;
        $team->job_title = $request->job_title;
        $team->linkedin_url = $request->linkedin_url;
        $team->status = $request->status;
        $team->save();

        // Save Temp Image Here
        if ($request->imageId > 0) {

            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $team->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);

                $destPath = public_path('uploads/teams/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(300, 300);
                $image->save($destPath);

                $team->image = $fileName;
                $team->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Team Member Added Successfully"
        ]);
    }

    public function show(string $id){

        $team = TeamMember::find($id);

        if ($team == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Team Member Not Found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $team
        ]);
    }


    public function update(Request $request, string $id){

        $team = TeamMember::find($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'job_title' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $team->name = $request->name;
        $team->job_title = $request->job_title;
        $team->linkedin_url = $request->linkedin_url;
        $team->status = $request->status;
        $team->save();

        // Save Temp Image Here
        if ($request->imageId > 0) {

            $oldImage = $team->image;
            $tempImage =  TempImage::find($request->imageId);

            if ($tempImage != null) {

                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now') . $team->id . '.' . $ext;

                // Create small thumbnail here
                $sourcePath = public_path('uploads/temp/' . $tempImage->name);

                $destPath = public_path('uploads/teams/' . $fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(300, 300);
                $image->save($destPath);

                $team->image = $fileName;
                $team->save();

                if ($oldImage != '') {
                    File::delete(public_path('uploads/teams/' . $oldImage));
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => "Team Member Updated Successfully"
        ]);

    }


    public function destroy(string $id){

        $team = TeamMember::find($id);

        $oldImage = $team->image;

        if ($team == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Team Not Found'
            ]);
        }

        if ($oldImage != null) {

            File::delete(public_path('uploads/teams/' . $oldImage));
        }

        $team->delete();

        return response()->json([
            'status' => true,
            'message' => "Team Member Deleted Successfully"
        ]);
    }
}
