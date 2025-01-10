<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{

    public function AllTeamMembers(){

        $teamMembers = TeamMember::where('status', 1)->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data' => $teamMembers
        ]);
    }

}
