<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function LatestService(){

        $projects = Project::where('status', 1)->orderBy('created_at', 'DESC')->take(4)->get();
        return response()->json([
            'status' => true,
            'data' => $projects
        ]);

    }

    public function AllService(){

        $projects = Project::where('status', 1)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $projects
        ]);

    }

    public function ProjectDetail(string $id){
        $project = Project::findOrFail($id);

        if ($project == null) {
            return response()->json([
                'status' => false,
                'errors' => 'Project Not Found'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $project
        ]);

    }
}
