<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdResource;
use App\Models\Ad;

class AdController extends Controller
{
    public function __invoke()
    {
        $ads = Ad::with('media')
            ->where('start_date' ,  '<=' , now())
            ->where('end_date' ,  '>=' , now())
            ->get();

        return AdResource::collection($ads);
    }
}
