<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Product management resource
 *
 * Manage formations, services, and digital products
 */
class Products extends Resource
{
    protected string $basePath = 'v1/partners/products';

    /**
     * List all products with optional filters
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific product
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Create a new product (formation, service, etc.)
     */
    public function create(array $data): array
    {
        return $this->store($data);
    }

    /**
     * Update a product
     */
    public function updateProduct(int $id, array $data): array
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a product
     */
    public function delete(int $id): array
    {
        return $this->destroy($id);
    }

    /**
     * Publish a product (make it available)
     */
    public function publish(int $id): array
    {
        return $this->client->post($this->path("{$id}/publish"));
    }

    /**
     * Unpublish a product
     */
    public function unpublish(int $id): array
    {
        return $this->client->post($this->path("{$id}/unpublish"));
    }

    /**
     * Duplicate a product
     */
    public function duplicate(int $id): array
    {
        return $this->client->post($this->path("{$id}/duplicate"));
    }

    /**
     * Get product statistics
     */
    public function stats(int $id): array
    {
        return $this->client->get($this->path("{$id}/stats"));
    }

    // === Formations (Courses) ===

    /**
     * List formations
     */
    public function formations(array $params = []): PaginatedResponse
    {
        return $this->paginate('partner/formations', $params);
    }

    /**
     * Get a formation
     */
    public function formation(int $id): array
    {
        return $this->client->get("partner/formations/{$id}");
    }

    /**
     * Create a formation
     */
    public function createFormation(array $data): array
    {
        return $this->client->post('partner/formations', $data);
    }

    /**
     * Update a formation
     */
    public function updateFormation(int $id, array $data): array
    {
        return $this->client->put("partner/formations/{$id}", $data);
    }

    /**
     * Delete a formation
     */
    public function deleteFormation(int $id): array
    {
        return $this->client->delete("partner/formations/{$id}");
    }

    // === Formation Modules ===

    /**
     * List modules for a formation
     */
    public function modules(int $formationId): array
    {
        return $this->client->get("partner/formations/{$formationId}/modules");
    }

    /**
     * Create a module
     */
    public function createModule(int $formationId, array $data): array
    {
        return $this->client->post("partner/formations/{$formationId}/modules", $data);
    }

    /**
     * Update a module
     */
    public function updateModule(int $formationId, int $moduleId, array $data): array
    {
        return $this->client->put("partner/formations/{$formationId}/modules/{$moduleId}", $data);
    }

    /**
     * Delete a module
     */
    public function deleteModule(int $formationId, int $moduleId): array
    {
        return $this->client->delete("partner/formations/{$formationId}/modules/{$moduleId}");
    }

    /**
     * Reorder modules
     */
    public function reorderModules(int $formationId, array $order): array
    {
        return $this->client->post("partner/formations/{$formationId}/modules/reorder", ['order' => $order]);
    }

    // === Formation Lessons ===

    /**
     * List lessons for a module
     */
    public function lessons(int $formationId, int $moduleId): array
    {
        return $this->client->get("partner/formations/{$formationId}/modules/{$moduleId}/lessons");
    }

    /**
     * Create a lesson
     */
    public function createLesson(int $formationId, int $moduleId, array $data): array
    {
        return $this->client->post("partner/formations/{$formationId}/modules/{$moduleId}/lessons", $data);
    }

    /**
     * Update a lesson
     */
    public function updateLesson(int $formationId, int $moduleId, int $lessonId, array $data): array
    {
        return $this->client->put("partner/formations/{$formationId}/modules/{$moduleId}/lessons/{$lessonId}", $data);
    }

    /**
     * Delete a lesson
     */
    public function deleteLesson(int $formationId, int $moduleId, int $lessonId): array
    {
        return $this->client->delete("partner/formations/{$formationId}/modules/{$moduleId}/lessons/{$lessonId}");
    }

    /**
     * Reorder lessons
     */
    public function reorderLessons(int $formationId, int $moduleId, array $order): array
    {
        return $this->client->post(
            "partner/formations/{$formationId}/modules/{$moduleId}/lessons/reorder",
            ['order' => $order]
        );
    }

    // === Services ===

    /**
     * List services
     */
    public function services(array $params = []): PaginatedResponse
    {
        return $this->paginate('partner/services', $params);
    }

    /**
     * Get a service
     */
    public function service(int $id): array
    {
        return $this->client->get("partner/services/{$id}");
    }

    /**
     * Create a service
     */
    public function createService(array $data): array
    {
        return $this->client->post('partner/services', $data);
    }

    /**
     * Update a service
     */
    public function updateService(int $id, array $data): array
    {
        return $this->client->put("partner/services/{$id}", $data);
    }

    /**
     * Delete a service
     */
    public function deleteService(int $id): array
    {
        return $this->client->delete("partner/services/{$id}");
    }

    // === Pricing Tiers ===

    /**
     * List pricing tiers for a product
     */
    public function pricingTiers(int $productId): array
    {
        return $this->client->get($this->path("{$productId}/pricing"));
    }

    /**
     * Create a pricing tier
     */
    public function createPricingTier(int $productId, array $data): array
    {
        return $this->client->post($this->path("{$productId}/pricing"), $data);
    }

    /**
     * Update a pricing tier
     */
    public function updatePricingTier(int $productId, int $tierId, array $data): array
    {
        return $this->client->put($this->path("{$productId}/pricing/{$tierId}"), $data);
    }

    /**
     * Delete a pricing tier
     */
    public function deletePricingTier(int $productId, int $tierId): array
    {
        return $this->client->delete($this->path("{$productId}/pricing/{$tierId}"));
    }

    // === Categories ===

    /**
     * List product categories
     */
    public function categories(): array
    {
        return $this->client->get('partner/categories');
    }

    /**
     * Assign category to product
     */
    public function assignCategory(int $productId, int $categoryId): array
    {
        return $this->client->post($this->path("{$productId}/categories"), ['category_id' => $categoryId]);
    }

    /**
     * Remove category from product
     */
    public function removeCategory(int $productId, int $categoryId): array
    {
        return $this->client->delete($this->path("{$productId}/categories/{$categoryId}"));
    }

    // === Media ===

    /**
     * Upload product image
     */
    public function uploadImage(int $productId, string $filePath): array
    {
        return $this->client->upload($this->path("{$productId}/images"), $filePath, 'image');
    }

    /**
     * Delete product image
     */
    public function deleteImage(int $productId, int $imageId): array
    {
        return $this->client->delete($this->path("{$productId}/images/{$imageId}"));
    }

    /**
     * Upload product video
     */
    public function uploadVideo(int $productId, string $filePath): array
    {
        return $this->client->upload($this->path("{$productId}/videos"), $filePath, 'video');
    }

    /**
     * Delete product video
     */
    public function deleteVideo(int $productId, int $videoId): array
    {
        return $this->client->delete($this->path("{$productId}/videos/{$videoId}"));
    }

    // === Reviews ===

    /**
     * Get product reviews
     */
    public function reviews(int $productId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("{$productId}/reviews"), $params);
    }

    /**
     * Respond to a review
     */
    public function respondToReview(int $productId, int $reviewId, string $response): array
    {
        return $this->client->post(
            $this->path("{$productId}/reviews/{$reviewId}/respond"),
            ['response' => $response]
        );
    }

    /**
     * Report a review
     */
    public function reportReview(int $productId, int $reviewId, string $reason): array
    {
        return $this->client->post(
            $this->path("{$productId}/reviews/{$reviewId}/report"),
            ['reason' => $reason]
        );
    }
}
