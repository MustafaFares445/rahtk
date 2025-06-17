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
use App\Models\Farm;
use App\Models\SchoolClass;
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
     *     description="Retrieve a list of available filters for a given product type. The filters are dynamically generated based on the product type.",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type of product to filter. Must be one of the following: estate, car, school, electronic, building.",
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
     *             ),
     *             example={
     *                 "estate": {
     *                     "rooms": "1, 2, 3",
     *                     "area": "100, 200, 300",
     *                     "floorsNumber": "1, 2",
     *                     "isFurnished": "true, false",
     *                     "floor": "1, 2, 3"
     *                 }
     *             }
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
            ],
            'school' => [
                'kg1',
                'kg2',
                'kg3',
                '1st',
                '2nd',
                '3rd',
                '4th',
                '5th',
                '6th',
                '7th',
                '8th',
                '9th',
                '10th',
                '11th',
                '12th',
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
            'farm' => [
                'type' => null,
                'bedrooms' => null,
                'bathrooms' => null,
                'floors_number' => null,
                'size' => null,
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
                ];
                $modelQuery = Car::query();
                $currentType = 'car';
                break;
            case ProductTypes::SCHOOL->value:
                $filters = [];
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
            case ProductTypes::FARM->value:
                $filters = [
                    'type',
                    'bedrooms',
                    'bathrooms',
                    'floors_number',
                    'size',
                ];
                $modelQuery = Farm::query();
                $currentType = 'farm';
                break;
            default:
                throw new BadRequestException('invalid type.');
                break;
        }

        if($request->get('type') != 'school'){
            $data['school'] = [];

            $productsFilters = $modelQuery->get($filters);

            foreach($filters as $filter) {
                $camelCaseKey = lcfirst(str_replace('_', '', ucwords($filter, '_')));
                $data[$currentType][$camelCaseKey] = $productsFilters->pluck($filter)->values()->unique()->toArray();
            }
        }

        return $data;
    }

    /**
     * @OA\Get(
     *     path="/api/search",
     *     summary="Search for products based on type and filters",
     *     description="Search for products by specifying a product type and optional filters. The results can be paginated.",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="The type of product to search for. Must be one of the following: estate, car, school, electronic, building, farm.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"estate", "car", "school", "electronic", "building", "farm"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="Text to search in product title or address.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Filters to apply to the search. Each filter should be an array where the first element is the filter key and the second is the value. Example: [['rooms', 2], ['area', 100]].",
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
     *         name="isUrgent",
     *         in="query",
     *         description="Filter by urgent products.",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="discount",
     *         in="query",
     *         description="Filter by products with discount.",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="minPrice",
     *         in="query",
     *         description="Minimum price filter.",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="maxPrice",
     *         in="query",
     *         description="Maximum price filter.",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="schoolClasses",
     *         in="query",
     *         description="Filter by school classes.",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page. Default is 15.",
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
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0',
        ]);

        $productsQuery = Product::with(['media' , 'farm' , 'estate' , 'car' , 'school' , 'electronic'])
            ->when($request->has('isUrgent') , fn($q) => $q->where('is_urgent' , true))
            ->when($request->has('discount') , fn($q) => $q->whereNotNull('discount'))
            ->when($request->has('minPrice'), fn($q) => $q->where('price', '>=', $request->get('minPrice')))
            ->when($request->has('maxPrice'), fn($q) => $q->where('price', '<=', $request->get('maxPrice')));

        if($request->has('text')){
            $text = $request->get('text');
            $words = explode(' ', $text);

            $productsQuery->where(function($q) use ($words) {
                foreach ($words as $word) {
                    $q->orWhere('title', 'like', '%' . $word . '%')
                      ->orWhere('address', 'like', '%' . $word . '%');
                }
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

        if($request->has('schoolClasses')){
            $productsQuery->whereHas('school.schoolClasses' , function($q) use ($request){
                $q->where(function($subQuery) use ($request) {
                    foreach($request->get('schoolClasses') as $schooolClass){
                        $subQuery->orWhere('type' , $schooolClass);
                    }
                });
            });
        }

        return ProductResource::collection($productsQuery->paginate($request->get('perPage' , 15)));
    }
}
