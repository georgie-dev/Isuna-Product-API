<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all products
     *
     * Returns a paginated list of products. Supports search, filtering, and sorting.
     *
     * @queryParam search string Search by name or description. Example: chair
     * @queryParam min_price number Filter by minimum price. Example: 10
     * @queryParam max_price number Filter by maximum price. Example: 500
     * @queryParam in_stock boolean Filter to only in-stock products. Example: true
     * @queryParam sort_by string Sort by field (price, created_at, name). Example: price
     * @queryParam sort_dir string Sort direction (asc, desc). Example: asc
     * @queryParam page integer Page number. Example: 1
     *
     * @response 200 scenario="Success" {
     *   "data": [
     *     {
     *       "id": "019e319c-f361-723e-9a3a-74362624ac38",
     *       "name": "Premium Chair",
     *       "description": "Designed for maximum comfort and durability.",
     *       "price": 99.99,
     *       "stock": 50,
     *       "created_at": "2026-05-16 17:00:00",
     *       "updated_at": "2026-05-16 17:00:00"
     *     }
     *   ],
     *   "links": {
     *     "first": "http://localhost:8000/api/v1/products?page=1",
     *     "last": "http://localhost:8000/api/v1/products?page=5",
     *     "prev": null,
     *     "next": "http://localhost:8000/api/v1/products?page=2"
     *   },
     *   "meta": {
     *     "current_page": 1,
     *     "per_page": 15,
     *     "total": 75
     *   }
     * }
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by stock availability
        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        $sortBy = in_array($request->sort_by, ['price', 'created_at', 'name'])
            ? $request->sort_by
            : 'created_at';
        $sortDir = in_array($request->sort_dir, ['asc', 'desc'])
            ? $request->sort_dir
            : 'desc';

        $query->orderBy($sortBy, $sortDir);

        return ProductResource::collection($query->paginate(15));
    }
    /**
     * Create a product
     *
     * Creates a new product. Requires authentication.
     *
     * @authenticated
     *
     * @bodyParam name string required The product name. Example: Premium Chair
     * @bodyParam description string A brief description of the product. Example: A very comfortable chair.
     * @bodyParam price number required The product price. Example: 99.99
     * @bodyParam stock integer required The available stock quantity. Example: 50
     *
     * @response 201 scenario="Product created" {
     *   "data": {
     *     "id": "019e319c-f361-723e-9a3a-74362624ac38",
     *     "name": "Premium Chair",
     *     "description": "A very comfortable chair.",
     *     "price": 99.99,
     *     "stock": 50,
     *     "created_at": "2026-05-16 17:00:00",
     *     "updated_at": "2026-05-16 17:00:00"
     *   }
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "price": ["The price field is required."]
     *   }
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated"
     * }
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }
    /**
     * Get a product
     *
     * Returns a single product by ID.
     *
     * @response 200 scenario="Success" {
     *   "data": {
     *     "id": "019e319c-f361-723e-9a3a-74362624ac38",
     *     "name": "Premium Chair",
     *     "description": "A very comfortable chair.",
     *     "price": 99.99,
     *     "stock": 50,
     *     "created_at": "2026-05-16 17:00:00",
     *     "updated_at": "2026-05-16 17:00:00"
     *   }
     * }
     * @response 404 scenario="Product not found" {
     *   "message": "Resource not found."
     * }
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
    /**
     * Update a product
     *
     * Updates an existing product. Requires authentication.
     *
     * @authenticated
     *
     * @bodyParam name string The product name. Example: Premium Chair
     * @bodyParam description string A brief description of the product. Example: A very comfortable chair.
     * @bodyParam price number The product price. Example: 99.99
     * @bodyParam stock integer The available stock quantity. Example: 50
     *
     * @response 200 scenario="Product updated" {
     *   "data": {
     *     "id": "019e319c-f361-723e-9a3a-74362624ac38",
     *     "name": "Premium Chair",
     *     "description": "A very comfortable chair.",
     *     "price": 149.99,
     *     "stock": 30,
     *     "created_at": "2026-05-16 17:00:00",
     *     "updated_at": "2026-05-16 17:30:00"
     *   }
     * }
     * @response 404 scenario="Product not found" {
     *   "message": "Resource not found."
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated."
     * }
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return new ProductResource($product);
    }
    /**
     * Delete a product
     *
     * Soft deletes a product. Requires authentication.
     *
     * @authenticated
     *
     * @response 200 scenario="Product deleted" {
     *   "message": "Product deleted successfully."
     * }
     * @response 404 scenario="Product not found" {
     *   "message": "Resource not found."
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated"
     * }
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}