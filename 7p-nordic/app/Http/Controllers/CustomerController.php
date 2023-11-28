<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CustomerController
 *
 * @package App\Http\Controllers
 *
 * @group Customer Management
 * Controller for managing customers.
 *
 * @OA\Tag(name="Customers")
 * @OA\SecurityScheme(
 *     securityScheme="Authorization",
 *     type="http",
 *     scheme="basic"
 * )
 */
class CustomerController extends Controller
{
    /**
     * Display the list of customers.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/customers",
     *     summary="Get a list of customers",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Customers retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CustomerCollection")
     *         )
     *     ),
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $customers = QueryBuilder::for(Customer::class)
            ->defaultSort('id')
            ->defaultSorts(['id', 'first_name', 'last_name', 'dob', 'username'])
            ->paginate();

        return response()->json([
            'message' => 'Customers retrieved successfully',
            'data' => new CustomerCollection($customers),
        ]);
    }

    /**
     * Display the specified customer.
     *
     * @param Customer $customer
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Get a specific customer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/CustomerResource")
     *     ),
     * )
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'message' => 'Customer retrieved successfully',
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Store the customer.
     *
     * @param StoreCustomerRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Store a new customer",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Customer data",
     *         @OA\JsonContent(ref="#/components/schemas/StoreCustomerRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Customer retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CustomerResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="property_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The error message for this property.")
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $customer = Customer::create($validated);

        return response()->json([
            'message' => 'Customer saved successfully',
            'data' => new CustomerResource($customer),
        ], 201);
    }

    /**
     * Update the specified customer.
     *
     * @param UpdateCustomerRequest $request
     * @param Customer $customer
     * @return JsonResponse
     *
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     summary="Update a specific customer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Customer data",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCustomerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/CustomerResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="property_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The error message for this property.")
     *                 )
     *             )
     *         )
     *     ),
     * )
     */
    public function update(UpdateCustomerRequest $request, Customer $customer) : JsonResponse {
        $validatedData = $request->validated();

        $customer->update($validatedData);

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Delete the specified customer.
     *
     * @param Customer $customer
     * @return JsonResponse
     *
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     summary="Delete a specific customer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted successfully",
     *     ),
     * )
     */
    public function destroy(Customer $customer) : JsonResponse {
        $id = $customer->id;
        $customer->delete();

        return response()->json([
            'message' => "Customer $id deleted successfully"
        ]);
    }
}
