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
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/search/filters",
     *     summary="Get filters for a specific product type",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type of product to filter",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"estate", "car", "school", "electronic", "building"}
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
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="invalid type.")
     *         )
     *     )
     * )
     */
    public function getFilters(Request $request)
    {
        $data = [
            'estate' => [
                'rooms' => null,
                'area' => null,
                'floorsNumber' => null,
                'isFurnished' => null,
                'floor' => null,
            ],
            'car' => [
                'model' => null,
                'year' => null,
                'kilo' => null,
            ],
            'school' => [
                'quate' => null,
                'workingDuration' => null,
                'foundingDate' => null,
                'manager' => null,
                'managerDescription' => null,
            ],
            'electronic' => [
                'model' => null,
                'brand' => null,
                'year' => null,
            ],
            'building' => [
                'type' => null,
                'brand' => null,
                'options' => null,
            ],
        ];

        $filters = [];
        $modelQuery = null;
        $currentType = null;

        switch($request->get('type')) {
            case ProductTypes::ESTATE->value:
                $filters = [
                    'rooms',
                    'area',
                    'floors_number',
                    'is_furnished',
                    'floor',
                ];
                $modelQuery = Estate::query();
                $currentType = 'estate';
                break;
            case ProductTypes::CAR->value:
                $filters = [
                    'model',
                    'year',
                    'kilo',
                ];
                $modelQuery = Car::query();
                $currentType = 'car';
                break;
            case ProductTypes::SCHOOL->value:
                $filters = [
                    'quate',
                    'working_duration',
                    'founding_date',
                    'manager',
                    'manager_description',
                ];
                $modelQuery = School::query();
                $currentType = 'school';
                break;
            case ProductTypes::ELECTRONIC->value:
                $filters = [
                    'model',
                    'brand',
                    'year',
                ];
                $modelQuery = Electronic::query();
                $currentType = 'electronic';
                break;
            case ProductTypes::BUILDING->value:
                $filters = [
                    'type',
                    'brand',
                    'options',
                ];
                $modelQuery = Building::query();
                $currentType = 'building';
                break;
            default:
                throw new BadRequestException('invalid type.');
                break;
        }

        $productsFilters = $modelQuery->get($filters);

        // Populate only the current type's properties with actual data
        foreach($filters as $filter) {
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($filter, '_')));
            $data[$currentType][$camelCaseKey] = $productsFilters->pluck($filter)->values()->unique()->toArray();
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
     *             enum={"estate", "car", "school", "electronic", "building"}
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
     *         description="Filters to apply to the search. Each filter should be an array where the first element is the filter key and the second is the value.",
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
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid type or filter.")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'type' => ['nullable', 'string', Rule::in(array_map(fn($case) => $case->value, ProductTypes::cases()))],
            'isUrgent' => 'nullable|boolean',
            'discount' => 'nullable|boolean',
        ]);

        $productsQuery = Product::with(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic'])
            ->when($request->has('isUrgent') , fn($q) => $q->where('is_urgent' , true))
            ->when($request->has('discount') , fn($q) => $q->whereNotNull('discount'));

        if($request->has('text')){
            $productsQuery->where(function($q) use ($request) {
                $q->where('title' , 'like', '%' . $request->get('text') . '%')
                    ->orWhere('address' , 'like', '%' . $request->get('text') . '%');
            });
        }

        if($request->has('filters')){

            $productsQuery->whereHas($request->get('type') , function($q) use ($request){
                foreach($request->get('filters') as $filter){
                    $snakeCaseKey = Str::snake($filter[0]);
                    $q->where($snakeCaseKey , $filter[1]);
                }
            });

        }elseif($request->has('type')){
            $productsQuery->whereHas($request->get('type'));
        }

        return ProductResource::collection($productsQuery->paginate($request->get('perPage' , 15)));
    }
}
