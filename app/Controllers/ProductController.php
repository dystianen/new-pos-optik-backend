<?php

namespace App\Controllers;

use App\Libraries\R2Storage;
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
        $id = $post['id'] ?? null;

        log_message('debug', '========== SAVE PRODUCT START ==========');
        log_message('debug', 'POST Data: ' . json_encode($post));

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

        $productData = [
            'category_id'   => $post['category_id'],
            'product_name'  => $post['product_name'],
            'product_price' => $post['product_price'],
            'product_stock' => $post['product_stock'],
            'product_brand' => $post['product_brand'] ?? null,
        ];

        try {
            // KUMPULKAN DATA IMAGE YANG MAU DIHAPUS DULU (SEBELUM TRANSACTION)
            $imagesToDelete = [];

            log_message('debug', 'Step 1: Collecting images to delete');

            if (!empty($post['variants']) && is_array($post['variants'])) {

                log_message('debug', 'Variants count: ' . count($post['variants']));

                foreach ($post['variants'] as $index => $v) {

                    try {
                        log_message('debug', "Processing variant index: $index");
                        log_message('debug', "Variant data: " . json_encode($v));

                        // PERBAIKAN: Cek apakah $v adalah array
                        if (!is_array($v)) {
                            log_message('error', "Variant at index $index is not an array: " . gettype($v));
                            continue;
                        }

                        $variantId = $v['variant_id'] ?? null;
                        $file = $request->getFile("variants.$index.image");

                        log_message('debug', "Variant ID: $variantId, File valid: " . ($file && $file->isValid() ? 'YES' : 'NO'));

                        if ($variantId && $file && $file->isValid() && !$file->hasMoved()) {

                            log_message('debug', "Checking old image for variant: $variantId");

                            // Cek image lama
                            $oldVariantImage = $this->variantImageModel
                                ->where('variant_id', $variantId)
                                ->first();

                            log_message('debug', 'Old Variant Image: ' . json_encode($oldVariantImage));

                            if ($oldVariantImage && is_array($oldVariantImage)) {

                                $oldProductImage = $this->productImageModel
                                    ->find($oldVariantImage['product_image_id']);

                                log_message('debug', 'Old Product Image: ' . json_encode($oldProductImage));

                                if ($oldProductImage && is_array($oldProductImage)) {

                                    $imagesToDelete[] = [
                                        'variant_id' => $variantId,
                                        'product_image_id' => $oldProductImage['product_image_id'],
                                        'filename' => $oldProductImage['url'],
                                        'full_path' => $oldProductImage['url']
                                    ];

                                    log_message('debug', 'Added to delete queue: ' . $oldProductImage['url']);
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        log_message('error', "Error processing variant $index: " . $e->getMessage());
                        log_message('error', "Line: " . $e->getLine() . " in " . $e->getFile());
                    }
                }
            }

            log_message('debug', 'Step 2: Images to delete: ' . json_encode($imagesToDelete));

            // HAPUS IMAGE LAMA SEBELUM TRANSACTION DIMULAI
            foreach ($imagesToDelete as $img) {

                try {
                    log_message('debug', 'Deleting image: ' . json_encode($img));

                    // Hapus file fisik
                    if (!empty($img['filename'])) {
                        $this->r2->deleteFile($img['filename']);
                        log_message('debug', 'Deleted R2 object: ' . $img['filename']);
                    }

                    // Hapus record
                    if (isset($img['variant_id']) && isset($img['product_image_id'])) {
                        $this->variantImageModel->where('variant_id', $img['variant_id'])->delete();
                        $this->productImageModel->delete($img['product_image_id']);
                        log_message('debug', 'Database records deleted for variant: ' . $img['variant_id']);
                    }
                } catch (\Throwable $e) {
                    log_message('error', 'Error deleting image: ' . $e->getMessage());
                }
            }

            log_message('debug', 'Step 3: Starting transaction');

            // BARU MULAI TRANSACTION
            $db->transStart();

            // INSERT / UPDATE PRODUCT
            if ($id) {
                $this->productModel->update($id, $productData);
                $productId = $id;
            } else {
                $this->productModel->insert($productData);
                $productId = $this->productModel->getInsertID();
            }

            log_message('debug', 'Product saved with ID: ' . $productId);

            // UPLOAD PRODUCT IMAGES (MULTIPLE)
            $images = $request->getFiles();

            if (!empty($images['images'])) {
                foreach ($images['images'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $newName = $img->getRandomName();
                        $tempPath = $img->getTempName();

                        $objectKey = $newName;

                        // Unggah file ke R2
                        $objectUrl = $this->r2->uploadFile($tempPath, $objectKey);

                        if ($objectUrl) {
                            log_message('debug', 'objectUrl: ' . $objectUrl);

                            $this->productImageModel->insert([
                                'product_id' => $productId,
                                'url'        => $objectUrl,
                                'alt_text'   => $post['product_name'],
                                'mime_type'  => $img->getClientMimeType(),
                                'size_bytes' => $img->getSize(),
                                'is_primary' => 1
                            ]);
                        } else {
                            log_message('error', 'Gagal mengunggah file ke R2: ' . $newName);
                        }
                    }
                }
            }

            // PRODUCT ATTRIBUTE VALUES
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

            log_message('debug', 'Step 4: Processing variants');

            // VARIANTS
            $existing = $this->variantModel->where('product_id', $productId)->findAll();
            $existingIds = array_column($existing, 'variant_id');
            $receivedIds = [];

            if (!empty($post['variants'])) {
                foreach ($post['variants'] as $index => $v) {

                    try {
                        log_message('debug', "Saving variant index: $index");

                        // VALIDASI: Pastikan $v adalah array
                        if (!is_array($v)) {
                            log_message('error', "Variant $index is not array, skipping");
                            continue;
                        }

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
                            log_message('debug', "Updated variant: $variantId");
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
                            log_message('debug', "Inserted new variant: $variantId");
                        }

                        // VARIANT IMAGE (UPLOAD BARU)
                        $file = $request->getFile("variants.$index.image");

                        if ($file && $file->isValid() && !$file->hasMoved()) {
                            $tempPath = $file->getTempName();
                            $newName = $file->getRandomName();

                            $objectUrl = $this->r2->uploadFile($tempPath, $newName);

                            if ($objectUrl) {
                                $this->productImageModel->insert([
                                    'product_id' => $productId,
                                    'url'        => $objectUrl,
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

                                log_message('debug', 'Uploaded variant image to R2: ' . $newName);
                            }
                        }

                        // VARIANT → ATTRIBUTE MAPPING
                        if (!empty($v['mapping'])) {
                            $mapping = is_string($v['mapping']) ? json_decode($v['mapping'], true) : $v['mapping'];

                            if (is_array($mapping)) {
                                $this->pvValueModel->where('variant_id', $variantId)->delete();

                                foreach ($mapping as $map) {
                                    if (!is_array($map)) continue;

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
                    } catch (\Throwable $e) {
                        log_message('error', "Error saving variant $index: " . $e->getMessage());
                        log_message('error', "Line: " . $e->getLine());
                    }
                }
            }

            log_message('debug', 'Step 5: Deleting removed variants');

            // DELETE REMOVED VARIANTS
            foreach ($existingIds as $old) {
                if (!in_array($old, $receivedIds)) {
                    $this->pvValueModel->where('variant_id', $old)->delete();

                    if ($oldVariantImage) {
                        $img = $this->productImageModel->find($oldVariantImage['product_image_id']);

                        if ($img && !empty($img['url'])) {
                            $filename = basename($img['url']); // hanya ambil nama object R2
                            $this->r2->deleteFile($filename);
                            log_message('debug', "Deleted R2 object for variant: $filename");
                        }
                    }
                    $this->variantImageModel->where('variant_id', $old)->delete();
                    $this->variantModel->delete($old);
                    log_message('debug', 'Deleted variant: ' . $old);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Transaction failed!');
                session()->setFlashdata('error', 'Transaction failed. Please try again.');
            } else {
                log_message('debug', 'Transaction completed successfully');
                session()->setFlashdata('success', 'Product saved successfully.');
            }
        } catch (\Throwable $e) {
            log_message('error', 'Save product error: ' . $e->getMessage());
            log_message('error', 'Error on line: ' . $e->getLine() . ' in file: ' . $e->getFile());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
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
