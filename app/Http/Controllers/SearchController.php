<?php

namespace App\Http\Controllers;

use App\Enums\ProductTypes;
use App\Http\Resources\ProductResource;
use App\Models\Building;
use App\Models\Car;
use App\Models\Electronic;
use App\Models\Estate;
use App\Models\Product;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/search/filter",
     *     summary="Get filters for a specific product type",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type of product to filter",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"ESTATE", "CAR", "SCHOOL", "ELECTRONIC", "BUILDING"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\AdditionalProperties(
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function getFilters(Request $request)
    {
        $filters = [];
        $modelQuery = null;
        switch($request->get('type')) {
            case ProductTypes::ESTATE->value:
                $filters = [
                    'rooms',
                    'area',
                    'floors_number',
                    'is_furnished',
                    'address',
                    'floor',
                ];
                $modelQuery = Estate::query();
                break;
            case ProductTypes::CAR->value:
                $filters = [
                    'model',
                    'year',
                    'kilo',
                ];
                $modelQuery = Car::query();
                break;
            case ProductTypes::SCHOOL->value:
                $filters = [
                    'quate',
                    'working_duration',
                    'founding_date',
                    'address',
                    'manager',
                    'manager_description',
                ];
                $modelQuery = School::query();
                break;
            case ProductTypes::ELECTRONIC->value:
                $filters = [
                    'model',
                    'brand',
                    'year',
                ];
                $modelQuery = Electronic::query();
                break;
            case ProductTypes::BUILDING->value:
                $filters = [
                    'type',
                    'brand',
                    'options',
                ];
                $modelQuery = Building::query();
                break;
            default:
                throw new BadRequestException('invalid type.');
                break;
        }

        $productsFilters = $modelQuery->get($filters);

        $data = [];
        foreach($filters as $filter){
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($filter, '_')));
            $data[$camelCaseKey] = $productsFilters->pluck($filter)->values()->unique()->toArray();
        }

        return $data;
    }

    /**
     * @OA\Get(
     *     path="/api/search",
     *     summary="Search for products based on type and filters",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type of product to search for",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"ESTATE", "CAR", "SCHOOL", "ELECTRONIC", "BUILDING"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="Text to search in product title or address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Filters to apply to the search",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
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
    public function search(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in(array_map(fn($case) => $case->value, ProductTypes::cases()))],
        ]);

        $productsQuery = Product::with(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic']);

        if($request->has('text')){
            $productsQuery->where(function($q) use ($request) {
                $q->where('title' , 'like', '%' . $request->get('text') . '%')
                    ->orWhere('address' , 'like', '%' . $request->get('text') . '%');
            });
        }

        if($request->has('filters')){

            $productsQuery->whereHas($request->get('type') , function($q) use ($request){
                foreach($request->get('filters') as $filter){
                    $q->where($filter[0] , $filter[1]);
                }
            });

        }else{
            $productsQuery->whereHas($request->get('type'));
        }

        return ProductResource::collection($productsQuery->paginate($request->get('perPage' , 15)));
    }
}
