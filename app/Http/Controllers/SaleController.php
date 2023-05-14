<?php

namespace App\Http\Controllers;

use App\Constants\HttpCode;
use App\Constants\Message;
use App\Constants\StatusCode;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Sales",
 *     description="API endpoints of sales"
 * ),
 */
class SaleController extends Controller
{
    protected $service;

    public function __construct(SaleService $service)
    {
        $this->service = $service;
    }

    /**
     * Save Sale
     * @OA\Post(
     *     path="/api/sale",
     *     tags={"Sales"},
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="400", description="Bad Request"),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Payload Example: `{'vehicle_id': '60ad2383822394f3dd463', 'sale_date': '2023-05-14', 'sale_price': 170000000}`",
     *         @OA\JsonContent(
     *              required={"vehicle_id","sale_date","sale_price"},
     *              @OA\Property(property="vehicle_id", type="string", example="60ad2383822394f3dd463"),
     *              @OA\Property(property="sale_date", type="string", example="2023-05-14"),
     *              @OA\Property(property="sale_price", type="number", example="170000000")
     *         ),
     *     ),
     *     security={ {"bearerAuth": {}} }
     * )
     */
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required|string',
            'sale_date' => 'required|date',
            'sale_price' => 'required|numeric',
        ]);

        $validatedData['created_by'] = auth()->user()->id;

        try {
            $sale = $this->service->save($validatedData);

            return apiResponse(HttpCode::CREATED, StatusCode::CREATED, Message::CREATED, $sale);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return apiResponse(HttpCode::NOT_FOUND, StatusCode::NOT_FOUND, Message::VEHICLE_NOT_FOUND, ['vehicle_id' => $validatedData['vehicle_id']]);
        } catch (\Exception $e) {
            return apiResponse(HttpCode::INTERNAL_ERROR, StatusCode::INTERNAL_ERROR, Message::INTERNAL_ERROR, ['error' => $e->getMessage()]);
        }
    }


    /**
     * Get paginated list of sales
     * @OA\Get(
     *     path="/api/sales",
     *     tags={"Sales"},
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

        $sales = $this->service->getManyWithPagination($page, $perPage, $searchQuery, $filters, $sortField, $sortDirection);

        return apiResponse(HttpCode::SUCCESS, StatusCode::OK, Message::SUCCESS, $sales);
    }

    /**
     * @OA\Get(
     *     path="/api/sales-report/{vehicleId}",
     *     summary="Get sales report for a specific vehicle",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="vehicleId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="httpCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(property="message", type="string", example="Success"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehicle not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="httpCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="NOT_FOUND"),
     *             @OA\Property(property="message", type="string", example="Vehicle not found"),
     *         )
     *     ),
     *     security={ {"bearerAuth": {}} }
     * )
     */
    public function getSalesReportPerVehicle($vehicleId)
    {
        $report = $this->service->getSalesReportPerVehicle($vehicleId);

        return apiResponse(HttpCode::SUCCESS, StatusCode::OK, Message::SUCCESS, $report);
    }
}
