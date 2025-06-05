<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Models\Ad;

class AdController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ads",
     *     summary="Get all active ads",
     *     tags={"Ads"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/AdResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function __invoke()
    {
        $ads = Ad::with('media')
            ->where('start_date' ,  '<=' , now())
            ->where('end_date' ,  '>=' , now())
            ->get();

        return AdResource::collection($ads);
    }
}
