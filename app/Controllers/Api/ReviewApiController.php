<?php

namespace App\Controllers\Api;

use App\Models\ReviewModel;
use App\Models\ReviewMediaModel;
use App\Libraries\R2Storage;

class ReviewApiController extends BaseApiController
{
    protected $reviewModel;
    protected $mediaModel;
    protected $r2;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->mediaModel = new ReviewMediaModel();
        $this->r2 = new R2Storage();
    }

    // =======================
    // GET /api/reviews
    // =======================
    public function index()
    {
        try {
            extract($this->getPaginationParams());
            $ratingFilter = $this->request->getGet('rating');

            $builder = $this->reviewModel
                ->select('reviews.*, customers.customer_name')
                ->join('customers', 'customers.customer_id = reviews.customer_id', 'left');

            if ($ratingFilter !== null && $ratingFilter !== '') {
                $builder->where('reviews.rating', (int)$ratingFilter);
            }

            $total = $builder->countAllResults(false);

            $reviews = $builder
                ->orderBy('reviews.created_at', 'DESC')
                ->findAll($per_page, $offset);

            // Fetch media for each review
            foreach ($reviews as &$review) {
                $review['media'] = $this->mediaModel
                    ->where('review_id', $review['review_id'])
                    ->findAll();
            }

            return $this->paginatedResponse($reviews, $total, $page, $per_page);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    // =======================
    // GET /api/reviews/product/{productId}
    // =======================
    public function getByProduct($productId)
    {
        try {
            extract($this->getPaginationParams());
            $ratingFilter = $this->request->getGet('rating');

            $builder = $this->reviewModel
                ->select('reviews.*, customers.customer_name')
                ->join('customers', 'customers.customer_id = reviews.customer_id', 'left')
                ->where('reviews.product_id', $productId);

            if ($ratingFilter !== null && $ratingFilter !== '') {
                $builder->where('reviews.rating', (int)$ratingFilter);
            }

            $total = $builder->countAllResults(false);

            $reviews = $builder
                ->orderBy('reviews.created_at', 'DESC')
                ->findAll($per_page, $offset);

            // Fetch media for each review
            foreach ($reviews as &$review) {
                $review['media'] = $this->mediaModel
                    ->where('review_id', $review['review_id'])
                    ->findAll();
            }

            // Get aggregate info (average rating, count, and star distribution)
            $db = db_connect();
            $aggregate = $db->table('reviews')
                ->select('COUNT(*) as total_reviews, AVG(rating) as average_rating')
                ->where('product_id', $productId)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();

            $stars = $db->table('reviews')
                ->select('rating, COUNT(*) as count')
                ->where('product_id', $productId)
                ->where('deleted_at', null)
                ->groupBy('rating')
                ->get()
                ->getResultArray();

            $ratingDistribution = [
                '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0,
            ];

            foreach ($stars as $s) {
                $ratingDistribution[(string)$s['rating']] = (int)$s['count'];
            }

            $averageRating = $aggregate['average_rating'] !== null ? round((float)$aggregate['average_rating'], 1) : 0;
            $totalReviews = (int)$aggregate['total_reviews'];

            $lastPage = ceil($total / $per_page);
            $response = [
                'items' => $reviews,
                'summary' => [
                    'average_rating' => $averageRating,
                    'total_reviews' => $totalReviews,
                    'rating_distribution' => $ratingDistribution
                ],
                'pagination' => [
                    'total' => $total,
                    'per_page' => $per_page,
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'from' => ($page - 1) * $per_page + 1,
                    'to' => min($page * $per_page, $total),
                ]
            ];

            return $this->successResponse($response);
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    // =======================
    // POST /api/reviews
    // =======================
    public function create()
    {
        try {
            $customerId = $this->getAuthenticatedCustomerId();
            
            $payload = $this->request->getPost();
            if (empty($payload)) {
                $payload = $this->getRequestBody(true);
            }

            $productId = $payload['product_id'] ?? null;
            $rating = isset($payload['rating']) ? (int)$payload['rating'] : null;
            $comment = $payload['comment'] ?? null;

            if (!$productId || !$rating) {
                return $this->validationErrorResponse([
                    'product_id' => 'Product ID is required',
                    'rating' => 'Rating is required'
                ]);
            }

            // Verify product exists
            $db = db_connect();
            $productExists = $db->table('products')
                ->where('product_id', $productId)
                ->where('deleted_at', null)
                ->countAllResults();

            if ($productExists === 0) {
                return $this->notFoundResponse('Product not found');
            }

            $db->transStart();

            $data = [
                'customer_id' => $customerId,
                'product_id' => $productId,
                'rating' => $rating,
                'comment' => $comment,
            ];

            $this->reviewModel->insert($data);
            $reviewId = $this->reviewModel->getInsertID();

            // Handle Multiple Files (images/videos)
            $files = $this->request->getFiles();
            if (isset($files['media'])) {
                foreach ($files['media'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        
                        $mime = $file->getMimeType();
                        $type = strpos($mime, 'video') === 0 ? 'video' : 'image';
                        $folder = $type === 'video' ? 'reviews/videos/' : 'reviews/images/';

                        $url = $this->r2->uploadFile(
                            $file->getTempName(),
                            $folder . $file->getRandomName()
                        );

                        if ($url) {
                            $this->mediaModel->insert([
                                'review_id' => $reviewId,
                                'file_url'  => $url,
                                'file_type' => $type
                            ]);
                        }
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->serverErrorResponse('Transaction failed');
            }

            $finalReview = $this->reviewModel->find($reviewId);
            $finalReview['media'] = $this->mediaModel->where('review_id', $reviewId)->findAll();

            return $this->createdResponse($finalReview, 'Review submitted successfully');
        } catch (\Throwable $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}
