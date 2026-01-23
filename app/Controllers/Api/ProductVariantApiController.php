<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ProductVariantModel;
use CodeIgniter\API\ResponseTrait;

class ProductVariantApiController extends BaseController
{
    use ResponseTrait;
    protected $variantModel;
    public function __construct()
    {
        $this->variantModel = new ProductVariantModel();
    }

    // GET /api/variants
    public function getByProductId()
    {
        $request = $this->request;
        $productId = $request->getVar('productId');

        $variants = $this->variantModel
            ->where('product_id', $productId)
            ->findAll();

        return $this->respond([
            'status' => 200,
            'message' => 'Get variants successfully!',
            'data' => $variants
        ], 200);
    }
}
