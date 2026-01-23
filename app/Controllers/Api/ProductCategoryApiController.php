<?php

namespace App\Controllers\Api;

use App\Models\ProductCategoryModel;

class ProductCategoryApiController extends BaseApiController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ProductCategoryModel();
    }

    // GET /api/products/categories
    public function apiListProductCategory()
    {
        $categories = $this->categoryModel->findAll();
        return $this->successResponse($categories);
    }
}
