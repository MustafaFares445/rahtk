<?php

namespace App\Http\Controllers;

use App\Enums\ProductTypes;
use App\Http\Resources\ProductResource;
use App\Models\Product;
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products/{product}",
     *     summary="Get a product by ID",
     *     description="Returns a single product with related media, farm, estate, car, school, and electronic data",
     *     operationId="getProductById",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="slug of the product to return",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function __invoke(Product $product)
    {
        $product->increment('view');
        $product->load(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic']);

        if($product->type == ProductTypes::SCHOOL->value){
            $product->load([
                'school.media',
                'school.schoolClasses'
            ]);
        }
        return new ProductResource($product , true);
    }
}
