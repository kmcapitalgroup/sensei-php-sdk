<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Certificates resource
 *
 * Manage certificates and credentials
 */
class Certificates extends Resource
{
    protected string $basePath = 'partner/certificates';

    // === Certificate Templates ===

    /**
     * List certificate templates
     */
    public function templates(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('templates'), $params);
    }

    /**
     * Get a template
     */
    public function template(int $templateId): array
    {
        return $this->client->get($this->path("templates/{$templateId}"));
    }

    /**
     * Create template
     */
    public function createTemplate(array $data): array
    {
        return $this->client->post($this->path('templates'), $data);
    }

    /**
     * Update template
     */
    public function updateTemplate(int $templateId, array $data): array
    {
        return $this->client->put($this->path("templates/{$templateId}"), $data);
    }

    /**
     * Delete template
     */
    public function deleteTemplate(int $templateId): array
    {
        return $this->client->delete($this->path("templates/{$templateId}"));
    }

    /**
     * Duplicate template
     */
    public function duplicateTemplate(int $templateId): array
    {
        return $this->client->post($this->path("templates/{$templateId}/duplicate"));
    }

    /**
     * Upload template background
     */
    public function uploadTemplateBackground(int $templateId, string $filePath): array
    {
        return $this->client->upload($this->path("templates/{$templateId}/background"), $filePath, 'background');
    }

    /**
     * Preview template
     */
    public function previewTemplate(int $templateId, array $variables = []): array
    {
        return $this->client->post($this->path("templates/{$templateId}/preview"), $variables);
    }

    // === Issued Certificates ===

    /**
     * List issued certificates
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a certificate
     */
    public function get(int $certificateId): array
    {
        return $this->client->get($this->path($certificateId));
    }

    /**
     * Get certificate by credential ID
     */
    public function getByCredentialId(string $credentialId): array
    {
        return $this->client->get($this->path("by-credential/{$credentialId}"));
    }

    /**
     * Issue certificate to user
     */
    public function issue(int $userId, int $templateId, array $data = []): array
    {
        return $this->client->post($this->path('issue'), array_merge($data, [
            'user_id' => $userId,
            'template_id' => $templateId
        ]));
    }

    /**
     * Bulk issue certificates
     */
    public function bulkIssue(array $userIds, int $templateId, array $data = []): array
    {
        return $this->client->post($this->path('bulk-issue'), array_merge($data, [
            'user_ids' => $userIds,
            'template_id' => $templateId
        ]));
    }

    /**
     * Revoke certificate
     */
    public function revoke(int $certificateId, string $reason): array
    {
        return $this->client->post($this->path("{$certificateId}/revoke"), ['reason' => $reason]);
    }

    /**
     * Reinstate certificate
     */
    public function reinstate(int $certificateId): array
    {
        return $this->client->post($this->path("{$certificateId}/reinstate"));
    }

    /**
     * Download certificate PDF
     */
    public function download(int $certificateId): array
    {
        return $this->client->get($this->path("{$certificateId}/download"));
    }

    /**
     * Get public verification URL
     */
    public function verificationUrl(int $certificateId): array
    {
        return $this->client->get($this->path("{$certificateId}/verification-url"));
    }

    /**
     * Verify certificate
     */
    public function verify(string $credentialId): array
    {
        return $this->client->get($this->path("verify/{$credentialId}"));
    }

    /**
     * Send certificate to user email
     */
    public function sendEmail(int $certificateId): array
    {
        return $this->client->post($this->path("{$certificateId}/send-email"));
    }

    // === User Certificates ===

    /**
     * Get user's certificates
     */
    public function userCertificates(int $userId, array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path("users/{$userId}"), $params);
    }

    // === Product Certificates ===

    /**
     * Get certificates for product completion
     */
    public function productCertificates(int $productId): array
    {
        return $this->client->get($this->path("products/{$productId}"));
    }

    /**
     * Link template to product
     */
    public function linkToProduct(int $productId, int $templateId, array $requirements = []): array
    {
        return $this->client->post($this->path("products/{$productId}/link"), [
            'template_id' => $templateId,
            'requirements' => $requirements
        ]);
    }

    /**
     * Unlink template from product
     */
    public function unlinkFromProduct(int $productId, int $templateId): array
    {
        return $this->client->delete($this->path("products/{$productId}/templates/{$templateId}"));
    }

    // === Expiration ===

    /**
     * Get expiring certificates
     */
    public function expiring(int $days = 30): PaginatedResponse
    {
        return $this->paginate($this->path('expiring'), ['days' => $days]);
    }

    /**
     * Renew certificate
     */
    public function renew(int $certificateId, array $data = []): array
    {
        return $this->client->post($this->path("{$certificateId}/renew"), $data);
    }

    // === Statistics ===

    /**
     * Get certificate statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    // === Badges (Digital Credentials) ===

    /**
     * Export as Open Badge
     */
    public function exportOpenBadge(int $certificateId): array
    {
        return $this->client->get($this->path("{$certificateId}/open-badge"));
    }

    /**
     * Share to LinkedIn
     */
    public function linkedInShareUrl(int $certificateId): array
    {
        return $this->client->get($this->path("{$certificateId}/linkedin-share"));
    }
}
