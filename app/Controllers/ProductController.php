<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductAttributeModel;
use App\Models\ProductAttributeValueModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantImageModel;
use App\Models\ProductVariantModel;
use App\Models\ProductVariantValueModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProductController extends BaseController
{
    protected $productModel;
    protected $productImageModel;
    protected $attributeModel;
    protected $pavModel;
    protected $variantModel;
    protected $pvValueModel;
    protected $categoryModel;
    protected $variantImageModel;
    protected $customerModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->productImageModel = new ProductImageModel();
        $this->categoryModel = new ProductCategoryModel();
        $this->attributeModel = new ProductAttributeModel();
        $this->pavModel = new ProductAttributeValueModel();
        $this->variantModel = new ProductVariantModel();
        $this->variantImageModel = new ProductVariantImageModel();
        $this->pvValueModel = new ProductVariantValueModel();
        $this->customerModel = new CustomerModel();
    }

    // =======================
    // API FUNCTIONS
    // =======================

    // GET /api/products/new-eyewear
    public function apiListNewEyewear()
    {
        $search = $this->request->getVar('search');

        $builder = $this->productModel->builder();

        if (!empty($search)) {
            $builder->like('product_name', $search);
        }

        $products = $builder
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        $response = [
            'status' => 200,
            'message' => 'Successfully!',
            'data' => $products
        ];

        return $this->response->setJSON($response);
    }

    // GET /api/product/recommendations
    public function apiProductRecommendations()
    {
        $limit = (int) $this->request->getVar('limit');
        $search = $this->request->getVar('search');

        try {
            $decode = $this->decodedToken();
            $customer = $this->customerModel->find($decode->user_id);
        } catch (\Exception $e) {
            $customer = null;
        }

        // Query produk dengan pencarian jika ada
        $builder = $this->productModel->builder();

        if (!empty($search)) {
            $builder->like('product_name', $search);
        }

        $products = $builder
            ->limit($limit)
            ->get()
            ->getResultArray();

        $recommendations = [];

        if (!$customer || empty($customer['customer_eye_history']) || empty($customer['customer_preferences'])) {
            foreach ($products as $product) {
                $product['score'] = 0;
                $recommendations[] = $product;
            }
        } else {
            $eyeHistoryData = json_decode($customer['customer_eye_history'], true);
            $preferencesData = json_decode($customer['customer_preferences'], true);

            foreach ($products as $product) {
                $score = 0;

                // Power range matching
                if (!empty($product['power_range']) && is_array($eyeHistoryData)) {
                    $range = explode('-', $product['power_range']);
                    if (count($range) === 2) {
                        $min = floatval(trim($range[0]));
                        $max = floatval(trim($range[1]));
                        $leftSphere = isset($eyeHistoryData['left_eye']['sphere']) ? floatval($eyeHistoryData['left_eye']['sphere']) : null;
                        $rightSphere = isset($eyeHistoryData['right_eye']['sphere']) ? floatval($eyeHistoryData['right_eye']['sphere']) : null;

                        if (
                            ($leftSphere !== null && $leftSphere >= $min && $leftSphere <= $max) ||
                            ($rightSphere !== null && $rightSphere >= $min && $rightSphere <= $max)
                        ) {
                            $score += 2;
                        }
                    }
                }

                // UV protection matching
                if (!empty($product['uv_protection']) && is_array($preferencesData)) {
                    if (in_array(strtolower($product['uv_protection']), array_map('strtolower', (array)$preferencesData))) {
                        $score += 1;
                    }
                }

                // Color matching
                if (!empty($product['color']) && is_array($preferencesData)) {
                    if (in_array(strtolower($product['color']), array_map('strtolower', (array)$preferencesData))) {
                        $score += 1;
                    }
                }

                // Coating matching
                if (!empty($product['coating']) && is_array($preferencesData)) {
                    if (in_array(strtolower($product['coating']), array_map('strtolower', (array)$preferencesData))) {
                        $score += 1;
                    }
                }

                $product['score'] = $score;
                $recommendations[] = $product;
            }

            // Urutkan berdasarkan score
            usort($recommendations, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });
        }

        $response = [
            'status' => 200,
            'message' => 'Successfully!',
            'data' => $recommendations,
        ];

        return $this->response->setJSON($response);
    }

    // GET /api/products/{id}
    public function apiProductDetail($id)
    {
        $product = $this->productModel->find($id);
        if ($product) {
            $response = [
                'status' => 200,
                'message' => 'Succesfully!',
                'data' => $product
            ];

            return $this->response->setJSON($response);
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
            ->setJSON(['message' => 'Product not found']);
    }

    public function apiProduct()
    {
        $category = $this->request->getVar('category');
        $search   = $this->request->getVar('search');
        $page     = $this->request->getVar('page') ?? 1;
        $limit    = $this->request->getVar('limit') ?? 10;

        $builder = $this->productModel;

        if ($category) {
            $builder = $builder->where('category_id', $category);
        }

        if ($search) {
            $builder = $builder->groupStart()
                ->like('product_name', $search)
                ->orLike('product_brand', $search)
                ->groupEnd();
        }

        $totalItems = $builder->countAllResults(false);

        $products = $builder
            ->orderBy('product_id', 'DESC')
            ->paginate($limit, 'products', $page);

        if (!$products) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['message' => 'No products found']);
        }

        $pager = [
            'currentPage' => $this->productModel->pager->getCurrentPage('products'),
            'totalPages'  => $this->productModel->pager->getPageCount('products'),
            'limit'       => $limit,
            'totalItems'  => $totalItems,
        ];

        $response = [
            'status'  => 200,
            'message' => 'Succesfully!',
            'data'    => $products,
            'pager'   => $pager,
        ];

        return $this->response->setJSON($response);
    }

    // =======================
    // WEB DASHBOARD FUNCTIONS
    // =======================

    // GET /products
    public function webIndex()
    {
        $attributes = $this->attributeModel->findAll();

        $currentPage = $this->request->getVar('page') ? (int)$this->request->getVar('page') : 1;
        $totalLimit = 10;
        $offset = ($currentPage - 1) * $totalLimit;

        $search = $this->request->getGet('search');

        $builder = $this->productModel
            ->join('product_categories', 'product_categories.category_id = products.category_id')
            ->orderBy('products.created_at', 'DESC');

        // Tambahkan filter pencarian jika ada keyword
        if (!empty($search)) {
            $builder->groupStart()
                ->like('products.product_name', $search)
                ->orLike('products.product_brand', $search)
                ->orLike('product_categories.category_name', $search)
                ->groupEnd();
        }

        // Clone builder untuk count
        $countBuilder = clone $builder;

        $products = $builder->findAll($totalLimit, $offset);
        $totalRows = $countBuilder->countAllResults(false);
        $totalPages = ceil($totalRows / $totalLimit);

        $data = [
            "attributes" => $attributes,
            "products" => $products,
            "pager" => [
                "totalPages" => $totalPages,
                "currentPage" => $currentPage,
                "limit" => $totalLimit,
            ],
            "search" => $search, // lempar ke view agar input tetap terisi
        ];

        return view('products/v_index', $data);
    }

    public function form()
    {
        $data = [];

        $id = $this->request->getVar('id');

        // --- STATIC DATA ---
        $data['categories'] = $this->categoryModel->findAll();
        $data['attributes'] = $this->attributeModel->findAll();

        // CREATE PRODUCT
        if (!$id) {
            return view('products/v_form', $data);
        }

        // ------------------------------------------------------------------
        // 1. PRODUCT
        // ------------------------------------------------------------------
        $product = $this->productModel->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('failed', 'Product not found.');
        }
        $data['product'] = $product;

        // ------------------------------------------------------------------
        // 2. PRODUCT IMAGES
        // ------------------------------------------------------------------
        $images = $this->productImageModel
            ->where('product_id', $id)
            ->where('is_primary', 1)
            ->where('deleted_at', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $data['product_images'] = $images;

        // ------------------------------------------------------------------
        // 3. PRODUCT ATTRIBUTE VALUES (PAV)
        // ------------------------------------------------------------------
        $pav = $this->pavModel
            ->where('product_id', $id)
            ->where('deleted_at', null)
            ->findAll();

        // Format: pav_values[attribute_id] = [pav_id, value]
        $pavValues = [];
        foreach ($pav as $row) {
            $pavValues[$row['attribute_id']] = [
                'pav_id' => $row['pav_id'],
                'value'  => $row['value'],
            ];
        }
        $data['pav_values'] = $pavValues;

        // ------------------------------------------------------------------
        // (NEW) — SELECTED ATTRIBUTE IDs
        // ------------------------------------------------------------------
        $selectedAttributes = array_unique(array_column($pav, 'attribute_id'));
        $data['selected_attributes'] = $selectedAttributes;

        // ------------------------------------------------------------------
        // (NEW) — SELECTED ATTRIBUTE VALUES
        // ------------------------------------------------------------------
        $selectedAttributeValues = array_column($pav, 'value');
        $data['selected_attribute_values'] = $selectedAttributeValues;

        // ------------------------------------------------------------------
        // 4. PRODUCT VARIANTS
        // ------------------------------------------------------------------
        $variants = $this->variantModel
            ->where('product_id', $id)
            ->where('deleted_at', null)
            ->findAll();

        foreach ($variants as &$v) {
            $variantId = $v['variant_id'];

            // Mapping ke PAV
            $pvValues = $this->pvValueModel
                ->where('variant_id', $variantId)
                ->where('deleted_at', null)
                ->findAll();

            $v['pav_mapping'] = array_column($pvValues, 'pav_id');

            // Variant Image
            $variantImage = $this->variantImageModel
                ->select('product_images.*')
                ->join('product_images', 'product_images.product_image_id = product_variant_images.product_image_id')
                ->where('product_variant_images.variant_id', $variantId)
                ->where('product_images.deleted_at', null)
                ->first();

            $v['variant_image'] = $variantImage;
        }

        $data['variants'] = $variants;

        return view('products/v_form', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        $request = $this->request;

        $post = $request->getPost();
        $id   = $post['id'] ?? null;

        // ---------------------------------------------------------
        // VALIDATION
        // ---------------------------------------------------------
        $rules = [
            'product_name'  => 'required|min_length[3]',
            'product_price' => 'required|numeric',
            'product_stock' => 'required|integer',
            'category_id'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('failed', 'Please check your input.');
        }

        // ---------------------------------------------------------
        // PRODUCT DATA
        // ---------------------------------------------------------
        $productData = [
            'category_id'   => $post['category_id'],
            'product_name'  => $post['product_name'],
            'product_price' => $post['product_price'],
            'product_stock' => $post['product_stock'],
            'product_brand' => $post['product_brand'] ?? null,
        ];

        try {

            $db->transStart();

            // ---------------------------------------------------------
            // INSERT / UPDATE PRODUCT
            // ---------------------------------------------------------
            if ($id) {
                $this->productModel->update($id, $productData);
                $productId = $id;
            } else {
                $this->productModel->insert($productData);
                $productId = $this->productModel->getInsertID();
            }

            // ---------------------------------------------------------
            // UPLOAD PRODUCT IMAGES (MULTIPLE)
            // ---------------------------------------------------------
            $images = $request->getFiles();

            if (!empty($images['images'])) {
                foreach ($images['images'] as $img) {

                    if ($img->isValid() && !$img->hasMoved()) {

                        $newName = $img->getRandomName();

                        // move to public/uploads/products/
                        $img->move(FCPATH . 'uploads/products', $newName);

                        $this->productImageModel->insert([
                            'product_id' => $productId,
                            'url'        => $newName,
                            'alt_text'   => $post['product_name'],
                            'mime_type'  => $img->getClientMimeType(),
                            'size_bytes' => $img->getSize(),
                            'is_primary' => 1
                        ]);
                    }
                }
            }

            // ---------------------------------------------------------
            // PRODUCT ATTRIBUTE VALUES
            // ---------------------------------------------------------
            if (!empty($post['attributes'])) {

                foreach ($post['attributes'] as $attrId => $value) {

                    $exists = $this->pavModel
                        ->where(['product_id' => $productId, 'attribute_id' => $attrId])
                        ->first();

                    if ($exists) {
                        $this->pavModel->update($exists['pav_id'], ['value' => $value]);
                    } else {
                        $this->pavModel->insert([
                            'product_id'   => $productId,
                            'attribute_id' => $attrId,
                            'value'        => $value
                        ]);
                    }
                }
            }

            // ---------------------------------------------------------
            // VARIANTS — SMART UPDATE
            // ---------------------------------------------------------
            $existing = $this->variantModel->where('product_id', $productId)->findAll();
            $existingIds = array_column($existing, 'variant_id');

            $receivedIds = [];

            if (!empty($post['variants'])) {

                foreach ($post['variants'] as $index => $v) {

                    $variantId = $v['variant_id'] ?? null;
                    $variantName = $v['label'] ?? 'Variant';
                    $price = $v['price'] ?? null;
                    $stock = $v['stock'] ?? null;

                    // UPDATE
                    if ($variantId) {

                        $this->variantModel->update($variantId, [
                            'variant_name' => $variantName,
                            'price'        => $price,
                            'stock'        => $stock
                        ]);

                        $receivedIds[] = $variantId;
                    }
                    // INSERT
                    else {
                        $this->variantModel->insert([
                            'product_id'   => $productId,
                            'variant_name' => $variantName,
                            'price'        => $price,
                            'stock'        => $stock
                        ]);

                        $variantId = $this->variantModel->getInsertID();
                        $receivedIds[] = $variantId;
                    }

                    // ---------------------------------------------------------
                    // VARIANT IMAGE (via getFile)
                    // ---------------------------------------------------------
                    $file = $request->getFile("variants.$index.image");

                    if ($file && $file->isValid() && !$file->hasMoved()) {

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/products', $newName);

                        $this->productImageModel->insert([
                            'product_id' => $productId,
                            'url'        => $newName,
                            'alt_text'   => $variantName,
                            'mime_type'  => $file->getClientMimeType(),
                            'size_bytes' => $file->getSize(),
                            'is_primary' => 0
                        ]);

                        $productImageId = $this->productImageModel->getInsertID();

                        $this->variantImageModel->insert([
                            'variant_id'       => $variantId,
                            'product_image_id' => $productImageId
                        ]);
                    }

                    // ---------------------------------------------------------
                    // VARIANT → ATTRIBUTE MAPPING
                    // ---------------------------------------------------------
                    if (!empty($v['mapping'])) {

                        $mapping = json_decode($v['mapping'], true);

                        $this->pvValueModel->where('variant_id', $variantId)->delete();

                        foreach ($mapping as $map) {

                            $pav = $this->pavModel->where([
                                'product_id'   => $productId,
                                'attribute_id' => $map['attribute_id'],
                                'value'        => $map['value']
                            ])->first();

                            if (!$pav) {
                                $this->pavModel->insert([
                                    'product_id'   => $productId,
                                    'attribute_id' => $map['attribute_id'],
                                    'value'        => $map['value']
                                ]);

                                $pavId = $this->pavModel->getInsertID();
                            } else {
                                $pavId = $pav['pav_id'];
                            }

                            $this->pvValueModel->insert([
                                'variant_id' => $variantId,
                                'pav_id'     => $pavId
                            ]);
                        }
                    }
                }
            }

            // ---------------------------------------------------------
            // DELETE REMOVED VARIANTS
            // ---------------------------------------------------------
            foreach ($existingIds as $old) {
                if (!in_array($old, $receivedIds)) {
                    $this->pvValueModel->where('variant_id', $old)->delete();
                    $this->variantImageModel->where('variant_id', $old)->delete();
                    $this->variantModel->delete($old);
                }
            }

            $db->transComplete();
            session()->setFlashdata('success', 'Product saved successfully.');
        } catch (\Throwable $e) {
            $db->transRollback();
            session()->setFlashdata('error', $e->getMessage());
        }

        return redirect()->to('/products');
    }


    public function webDelete($id)
    {
        $this->productModel->delete($id);
        return redirect()->to('/products')->with('success', 'Product deleted successfully');
    }
}
