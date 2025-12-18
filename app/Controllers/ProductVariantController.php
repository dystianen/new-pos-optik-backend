<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductVariantModel;
use CodeIgniter\API\ResponseTrait;

class ProductVariantController extends BaseController
{
    use ResponseTrait;
    protected $variantModel;
    public function __construct()
    {
        $this->variantModel = new ProductVariantModel();
    }

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
