<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;


class ServiceController extends Controller
{
        public function LatestService(){

            $services = Service::where('status', 1)->orderBy('created_at', 'DESC')->take(4)->get();
            return response()->json([
                'status' => true,
                'data' => $services
            ]);

        }

        public function AllService(){

            $services = Service::where('status', 1)->orderBy('created_at', 'DESC')->get();
            return response()->json([
                'status' => true,
                'data' => $services
            ]);

        }

        public function ServiceDetails(string $id){

            $service = Service::findOrFail($id);

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

}
