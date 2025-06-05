<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

/**
* @OA\Tag(
*     name="HomePage",
*     description="Operations related to the homepage products"
* )
*/
class HomePageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/home/products",
     *     summary="Get products for the homepage",
     *     description="Retrieve a list of products with optional filters for urgency and discount",
     *     operationId="getHomePageProducts",
     *     tags={"HomePage"},
     *     @OA\Parameter(
     *         name="isUrgent",
     *         in="query",
     *         description="Filter products by urgency",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="discount",
     *         in="query",
     *         description="Filter products by discount availability",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
       $products =  Product::with(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic'])
            ->when($request->has('isUrgent') , fn($q) => $q->where('is_urgent' , true))
            ->when($request->has('discount') , fn($q) => $q->whereNotNull('discount'))
            ->latest()
            ->take(5)
            ->get();

        return ProductResource::collection($products);
    }
}