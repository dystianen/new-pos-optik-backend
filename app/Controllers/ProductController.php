<?php

namespace App\Controllers;

use App\Libraries\R2Storage;
use App\Models\CustomerModel;
use App\Models\ProductAttributeModel;
use App\Models\ProductAttributeValueModel;
use App\Models\ProductCategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantAttributeModel;
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
    protected $productVariantAttributeModel;
    protected $r2;

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
        $this->productVariantAttributeModel = new ProductVariantAttributeModel();
        $this->r2 = new R2Storage();
    }

    // =======================
    // API FUNCTIONS
    // =======================

    // GET /api/products/new-eyewear
    public function apiListNewEyewear()
    {
        $search = $this->request->getVar('search');

        $builder = $this->productModel->builder();

        $builder->select('
            products.*,
            product_images.url AS product_image_url
        ');

        $builder->join(
            'product_images',
            'product_images.product_id = products.product_id AND product_images.is_primary = 1',
            'left'
        );

        if (!empty($search)) {
            $builder->like('products.product_name', $search);
        }

        $products = $builder
            ->orderBy('products.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'message' => 'Successfully!',
            'data' => $products
        ]);
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

        if (!$product) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['message' => 'Product not found']);
        }

        // Primary image
        $galleryImage = $this->productImageModel
            ->select('url, alt_text')
            ->where('product_id', $id)
            ->where('is_primary', 1)
            ->findAll();


        // Gallery images (non-primary)
        $variantImage = $this->productImageModel
            ->select('url, alt_text')
            ->where('product_id', $id)
            ->where('is_primary', 0)
            ->findAll();

        $product['gallery'] = $galleryImage;
        $product['variant_image'] = $variantImage;

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Successfully!',
            'data'    => $product
        ]);
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

        $id = $this->request->getGet('id');

        // --- STATIC DATA ---
        $data['categories'] = $this->categoryModel->findAll();

        // ✅ GET ATTRIBUTES WITH MASTER VALUES
        $attributes = $this->attributeModel->findAll();

        foreach ($attributes as &$attr) {
            // Load master values untuk setiap attribute
            $attr['values'] = $this->db->table('product_attribute_master_values')
                ->where('attribute_id', $attr['attribute_id'])
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
        }

        $data['attributes'] = $attributes;

        if (empty($id)) {
            log_message('debug', 'Mode: CREATE (no ID)');

            $data['product'] = null;
            $data['product_images'] = [];
            $data['pav_values'] = [];
            $data['selected_attributes'] = [];
            $data['selected_attribute_values'] = [];
            $data['variants'] = [];

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

        // Format: pav_values[attribute_id] = [pav_id, value, value2, ...]
        $pavValues = [];
        foreach ($pav as $row) {
            $attrId = $row['attribute_id'];

            if (!isset($pavValues[$attrId])) {
                $pavValues[$attrId] = [
                    'pav_ids' => [],
                    'values' => []
                ];
            }

            $pavValues[$attrId]['pav_ids'][] = $row['pav_id'];
            $pavValues[$attrId]['values'][] = $row['value'];
        }
        $data['pav_values'] = $pavValues;

        // ------------------------------------------------------------------
        // (NEW) — SELECTED ATTRIBUTE IDs
        // ------------------------------------------------------------------
        $variantAttrs = $this->productVariantAttributeModel
            ->where('product_id', $id)
            ->findAll();

        $selectedAttributes = array_column($variantAttrs, 'attribute_id');
        $data['selected_attributes'] = $selectedAttributes;

        // ------------------------------------------------------------------
        // (NEW) — SELECTED ATTRIBUTE VALUES (flat array)
        // ------------------------------------------------------------------
        $selectedAttributeValues = [];

        foreach ($pav as $row) {
            $attrId = $row['attribute_id'];

            if (!isset($selectedAttributeValues[$attrId])) {
                $selectedAttributeValues[$attrId] = [];
            }

            $selectedAttributeValues[$attrId][] = $row['value'];
        }

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
        $id = $post['id'] ?? null;

        log_message('debug', '========== SAVE PRODUCT START ==========');
        log_message('debug', 'POST: ' . json_encode($post));

        // -------------------------------------------------
        // VALIDATION
        // -------------------------------------------------
        $rules = [
            'product_name'  => 'required|min_length[3]',
            'product_price' => 'required|numeric',
            'category_id'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('failed', 'Invalid input');
        }

        $productData = [
            'category_id'   => $post['category_id'],
            'product_name'  => $post['product_name'],
            'product_price' => $post['product_price'],
            'product_brand' => $post['product_brand'] ?? null,
        ];

        try {

            // =================================================
            // START TRANSACTION
            // =================================================
            $db->transBegin();

            // -------------------------------------------------
            // SAVE PRODUCT
            // -------------------------------------------------
            if ($id) {
                $this->productModel->update($id, $productData);
                $productId = $id;
            } else {
                $this->productModel->insert($productData);
                $productId = $this->productModel->getInsertID();
            }

            log_message('debug', "Product ID: $productId");

            // -------------------------------------------------
            // PRODUCT IMAGES (PRIMARY)
            // -------------------------------------------------
            $files = $request->getFiles();

            if (!empty($files['images'])) {
                foreach ($files['images'] as $img) {
                    if (!$img->isValid() || $img->hasMoved()) continue;

                    $objectUrl = $this->r2->uploadFile(
                        $img->getTempName(),
                        $img->getRandomName()
                    );

                    if (!$objectUrl) {
                        throw new \Exception('Failed upload product image');
                    }

                    $this->productImageModel->insert([
                        'product_id'  => $productId,
                        'url'         => $objectUrl,
                        'alt_text'    => $post['product_name'],
                        'mime_type'   => $img->getClientMimeType(),
                        'size_bytes'  => $img->getSize(),
                        'is_primary'  => 1,
                    ]);
                }
            }

            // -------------------------------------------------
            // VARIANT ATTRIBUTE TOGGLE (ON / OFF)
            // -------------------------------------------------
            $variantAttributes = $post['variant_attributes'] ?? [];

            // reset toggle lama
            $this->productVariantAttributeModel
                ->where('product_id', $productId)
                ->delete();

            // simpan toggle baru
            foreach ($variantAttributes as $attrId) {
                $this->productVariantAttributeModel->insert([
                    'product_id'   => $productId,
                    'attribute_id' => $attrId,
                ]);
            }


            // -------------------------------------------------
            // PRODUCT ATTRIBUTE VALUES
            // -------------------------------------------------
            if (!empty($post['attributes'])) {
                foreach ($post['attributes'] as $attrId => $value) {

                    $exists = $this->pavModel
                        ->where([
                            'product_id'   => $productId,
                            'attribute_id' => $attrId
                        ])
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

            // -------------------------------------------------
            // VARIANTS
            // -------------------------------------------------
            $existingVariants = $this->variantModel
                ->where('product_id', $productId)
                ->findAll();

            $existingIds = array_column($existingVariants, 'variant_id');
            $receivedIds = [];

            if (!empty($post['variants'])) {

                foreach ($post['variants'] as $index => $v) {

                    if (!is_array($v)) continue;

                    $variantId   = $v['variant_id'] ?? null;
                    $variantName = $v['label'] ?? 'Variant';
                    $price       = $v['price'] ?? null;

                    // ----------------------------
                    // INSERT / UPDATE VARIANT
                    // ----------------------------
                    if ($variantId) {

                        $this->variantModel->update($variantId, [
                            'variant_name' => $variantName,
                            'price'        => $price
                        ]);
                    } else {
                        $insert = $this->variantModel->insert([
                            'variant_id'   => $variantId,
                            'product_id'   => $productId,
                            'variant_name' => $variantName,
                            'price'        => $price
                        ]);

                        $variantId = $this->variantModel->getInsertID();

                        if (!$insert) {
                            throw new \Exception('Failed insert variant');
                        }
                    }

                    // HARD CHECK (FK SAFETY)
                    $variantExists = $this->variantModel
                        ->where('variant_id', $variantId)
                        ->first();

                    if (!$variantExists) {
                        throw new \Exception("Variant not exists after save: $variantId");
                    }

                    $receivedIds[] = $variantId;

                    // ----------------------------
                    // VARIANT IMAGE
                    // ----------------------------
                    $file = $request->getFile("variants.$index.image");

                    if ($file && $file->isValid() && !$file->hasMoved()) {

                        // Delete old image
                        $old = $this->variantImageModel
                            ->where('variant_id', $variantId)
                            ->first();

                        if ($old) {
                            $oldImg = $this->productImageModel
                                ->find($old['product_image_id']);

                            if ($oldImg) {
                                $this->r2->deleteFile($oldImg['url']);
                                $this->variantImageModel->delete($old['id']);
                                $this->productImageModel->delete($oldImg['product_image_id']);
                            }
                        }

                        $url = $this->r2->uploadFile(
                            $file->getTempName(),
                            $file->getRandomName()
                        );

                        if (!$url) {
                            throw new \Exception('Failed upload variant image');
                        }

                        $this->productImageModel->insert([
                            'product_id'       => $productId,
                            'url'              => $url,
                            'alt_text'         => $variantName,
                            'mime_type'        => $file->getClientMimeType(),
                            'size_bytes'       => $file->getSize(),
                            'is_primary'       => 0
                        ]);

                        $productImageId = $this->productImageModel->getInsertID();

                        $this->variantImageModel->insert([
                            'variant_id'       => $variantId,
                            'product_image_id' => $productImageId
                        ]);
                    }

                    // ----------------------------
                    // VARIANT ATTRIBUTE MAPPING
                    // ----------------------------
                    if (!empty($v['mapping'])) {

                        $mapping = is_string($v['mapping'])
                            ? json_decode($v['mapping'], true)
                            : $v['mapping'];

                        if (is_array($mapping)) {

                            // Hapus mapping lama
                            $this->pvValueModel
                                ->where('variant_id', $variantId)
                                ->delete();

                            foreach ($mapping as $item) {

                                // ✅ CEK FORMAT: apakah old format (string ID) atau new format (object)?
                                if (is_string($item)) {
                                    // OLD FORMAT: langsung pav_id
                                    $pavId = $item;
                                } else {
                                    // NEW FORMAT: {attribute_id: "...", value: "..."}
                                    $attributeId = $item['attribute_id'] ?? null;
                                    $value = $item['value'] ?? null;

                                    if (!$attributeId || !$value) {
                                        log_message('error', 'Invalid mapping item: ' . json_encode($item));
                                        continue;
                                    }

                                    // ✅ LOOKUP PAV_ID dari attribute_id + value
                                    $pav = $this->pavModel
                                        ->where('attribute_id', $attributeId)
                                        ->where('value', $value)
                                        ->first();

                                    if (!$pav) {
                                        throw new \Exception("PAV not found for attribute_id=$attributeId, value=$value");
                                    }

                                    $pavId = $pav['pav_id'];
                                }

                                // HARD SAFETY CHECK
                                if (!$this->pavModel->find($pavId)) {
                                    throw new \Exception("PAV not found: $pavId");
                                }

                                $this->pvValueModel->insert([
                                    'variant_id' => $variantId,
                                    'pav_id'     => $pavId
                                ]);
                            }
                        }
                    }
                }
            }

            // -------------------------------------------------
            // DELETE REMOVED VARIANTS
            // -------------------------------------------------
            foreach ($existingIds as $oldId) {

                if (!in_array($oldId, $receivedIds)) {

                    $this->pvValueModel->where('variant_id', $oldId)->delete();

                    $img = $this->variantImageModel
                        ->where('variant_id', $oldId)
                        ->first();

                    if ($img) {
                        $pimg = $this->productImageModel
                            ->find($img['product_image_id']);

                        if ($pimg) {
                            $this->r2->deleteFile($pimg['url']);
                            $this->productImageModel->delete($pimg['product_image_id']);
                        }

                        $this->variantImageModel->delete($img['id']);
                    }

                    $this->variantModel->delete($oldId);
                }
            }

            // =================================================
            // COMMIT
            // =================================================
            $db->transCommit();
            session()->setFlashdata('success', 'Product saved successfully');
        } catch (\Throwable $e) {

            $db->transRollback();

            log_message('error', $e->getMessage());
            log_message('error', $e->getTraceAsString());

            session()->setFlashdata('error', $e->getMessage());
        }

        log_message('debug', '========== SAVE PRODUCT END ==========');
        return redirect()->to('/products');
    }


    public function deleteImage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $json = $this->request->getJSON(true);
        $imageId = $json['image_id'] ?? null;
        $productId = $json['product_id'] ?? null;

        if (!$imageId || !$productId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid parameters'
            ])->setStatusCode(400);
        }

        try {
            // 🔍 Ambil data image
            $image = $this->productImageModel
                ->where('product_image_id', $imageId)
                ->where('product_id', $productId)
                ->first();

            if (!$image) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Image not found'
                ])->setStatusCode(404);
            }

            // 🔍 Cek apakah dipakai variant
            $variantImage = $this->variantImageModel
                ->where('product_image_id', $imageId)
                ->first();

            if ($variantImage) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cannot delete image. This image is used by a product variant.'
                ])->setStatusCode(400);
            }

            $r2Key = $image['url'];

            try {
                $this->r2->deleteFile($r2Key);
                log_message('info', 'R2 deletion success: ' . $r2Key);
            } catch (\Throwable $e) {
                log_message('error', 'R2 deletion FAILED: ' . $e->getMessage());
            }

            // 🗑 3. Hapus record DB
            $this->productImageModel
                ->where('product_image_id', $imageId)
                ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function webDelete($id)
    {
        $this->productModel->delete($id);
        return redirect()->to('/products')->with('success', 'Product deleted successfully');
    }
}
