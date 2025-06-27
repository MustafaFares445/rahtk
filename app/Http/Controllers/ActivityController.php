<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="Get all activities",
     *     tags={"Activities"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke()
    {
        return response()->json([
            'data'  => Activity::all()
        ]);
    }
}
