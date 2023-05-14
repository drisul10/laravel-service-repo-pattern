<?php

namespace App\Http\Controllers;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Vehicles",
 *     description="API endpoints of vehicle"
 * ),
 */
class VehicleController extends Controller
{
    protected $service;

    public function __construct(VehicleService $service)
    {
        $this->service = $service;
    }

    /**
     * Save vehicle
     * @OA\Post(
     *     path="/api/vehicle",
     *     tags={"Vehicles"},
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="400", description="Bad Request"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Example for Car:
     *         `{'type': 'car', 'name': 'Honda Brio', 'release_year': 2015, 'color': 'white', 'price': 170000000, 'engine': 'engine2', 'passenger_capacity': 4, 'car_type': 'carType1'}`
     *         Example for Motorcycle:
     *         `{'type': 'motorcycle', 'name': 'Yamaha Nmax 155', 'release_year': 2023, 'color': 'red', 'price': 35000000, 'engine': 'engine1', 'suspension_type': 'suspension1', 'transmission_type': 'automatic'}`",
     *         @OA\JsonContent(
     *              required={"type","name","release_year","color","price","engine"},
     *              @OA\Property(property="type", type="string", example="motorbike", enum={"car", "motorcycle"}),
     *              @OA\Property(property="name", type="string", example="Yamaha Nmax 155"),
     *              @OA\Property(property="release_year", type="number", example="2023"),
     *              @OA\Property(property="color", type="string", example="red"),
     *              @OA\Property(property="price", type="number", example="35000000"),
     *              @OA\Property(property="engine", type="string", example="engine1"),
     *              @OA\Property(property="suspension_type", type="string", example="suspension1"),
     *              @OA\Property(property="transmission_type", type="string", example="automatic"),
     *              @OA\Property(property="passenger_capacity", type="number", example="4"),
     *              @OA\Property(property="car_type", type="string", example="carType1")
     *         ),
     *     ),
     *     security={ {"bearerAuth": {}} }
     * )
     */
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|in:car,motorcycle',
            'name' => 'required|string',
            'release_year' => 'required|numeric',
            'color' => 'required|string',
            'price' => 'required|numeric',
            'engine' => 'required|string',
        ]);

        $request->validate([
            'suspension_type' => Rule::requiredIf($request->type == 'motorcycle'),
            'transmission_type' => Rule::requiredIf($request->type == 'motorcycle'),
            'passenger_capacity' => Rule::requiredIf($request->type == 'car'),
            'car_type' => Rule::requiredIf($request->type == 'car'),
        ]);

        $validatedData['created_by'] = auth()->user()->id;

        try {
            $vehicle = $this->service->save($validatedData);
            return apiResponse(HttpCode::CREATED, StatusCode::CREATED, Message::CREATED, $vehicle);
        } catch (\Exception $e) {
            return apiResponse(HttpCode::INTERNAL_ERROR, StatusCode::INTERNAL_ERROR, Message::INTERNAL_ERROR, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get paginated list of vehicles
     * @OA\Get(
     *     path="/api/vehicles",
     *     tags={"Vehicles"},
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="searchQuery",
     *         in="query",
     *         description="Search query",
     *         required=false,
     *         @OA\Schema(type="string", example="brio")
     *     ),
     *     @OA\Parameter(
     *         name="filters",
     *         in="query",
     *         description="Additional filters",
     *         required=false,
     *         @OA\Schema(type="string", example="type:car")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Parameter(
     *         name="sortDirection",
     *         in="query",
     *         description="Sort direction",
     *         required=false,
     *         @OA\Schema(type="string", example="desc")
     *     ),
     *     security={ {"bearerAuth": {}} }
     * )
     */
    public function getManyWithPagination(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('perPage', 15);
        $searchQuery = $request->get('searchQuery', null);
        $filters = $request->get('filters', []);
        $sortField = $request->get('sortField', 'id');
        $sortDirection = $request->get('sortDirection', 'asc');

        $vehicles = $this->service->getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection);

        return apiResponse(HttpCode::SUCCESS, StatusCode::OK, Message::SUCCESS, $vehicles);
    }
}
