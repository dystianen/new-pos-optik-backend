<?php

namespace App\Controllers\Api;

use App\Models\ProductAttributeModel;

class ProductAttributeApiController extends BaseApiController
{
    protected $attributeModel;

    public function __construct()
    {
        $this->attributeModel = new ProductAttributeModel();
    }

    // GET /api/products/attributes
    public function apiListProductAttribute()
    {
        $attributes = $this->attributeModel->findAll();

        $response = [
            'status' => 200,
            'message' => 'Successfully',
            'data' => $attributes
        ];
        return $this->response->setJSON($response);
    }
}
