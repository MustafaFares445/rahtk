<?php

namespace App\Http\Controllers;

use App\Enums\ProductTypes;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     *     description="Retrieve a list of products with optional filters for urgency, discount, and type.",
     *     operationId="getHomePageProducts",
     *     tags={"HomePage"},
     *     @OA\Parameter(
     *         name="isUrgent",
     *         in="query",
     *         description="Filter products by urgency. If true, only urgent products are returned.",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="discount",
     *         in="query",
     *         description="Filter products by discount availability. If true, only products with discounts are returned.",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter products by type. Must be one of the valid product types.",
     *         required=false,
     *         @OA\Schema(type="string", enum={"farm", "estate", "car", "school", "electronic"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation. Returns a list of products.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input. At least one of isUrgent or discount is required, or the provided type is invalid."
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'isUrgent' => 'nullable|boolean',
            'discount' => 'nullable|boolean',
            'type' => ['nullable' , 'string' , Rule::in(array_column(ProductTypes::cases() , 'value'))],
        ]);

        if (!$request->has('isUrgent') && !$request->has('discount')) {
            return response()->json(['error' => 'At least one of isUrgent or discount is required.'], 400);
        }

        $products =  Product::with(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic'])
            ->when($request->has('isUrgent') , fn($q) => $q->where('is_urgent' , true))
            ->when($request->has('discount') , fn($q) => $q->whereNotNull('discount'))
            ->when($request->has('type') , fn($q) =>  $q->whereHas($request->get('type')))
            ->latest()
            ->take(5)
            ->get();

        return ProductResource::collection($products);
    }
}