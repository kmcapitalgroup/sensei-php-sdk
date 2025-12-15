<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

/**
 * Partner profile management resource
 *
 * Manage partner profile and business information
 */
class Profile extends Resource
{
    protected string $basePath = 'v1/partners/profile';

    /**
     * Get partner profile
     */
    public function get(): array
    {
        return $this->client->get($this->path());
    }

    /**
     * Update partner profile
     */
    public function updateProfile(array $data): array
    {
        return $this->client->put($this->path(), $data);
    }

    /**
     * Get business information
     */
    public function business(): array
    {
        return $this->client->get($this->path('business'));
    }

    /**
     * Update business information
     */
    public function updateBusiness(array $data): array
    {
        return $this->client->put($this->path('business'), $data);
    }

    /**
     * Get contact information
     */
    public function contact(): array
    {
        return $this->client->get($this->path('contact'));
    }

    /**
     * Update contact information
     */
    public function updateContact(array $data): array
    {
        return $this->client->put($this->path('contact'), $data);
    }

    /**
     * Upload profile picture/logo
     */
    public function uploadLogo(string $filePath): array
    {
        return $this->client->upload($this->path('logo'), $filePath, 'logo');
    }

    /**
     * Delete profile picture/logo
     */
    public function deleteLogo(): array
    {
        return $this->client->delete($this->path('logo'));
    }

    /**
     * Upload banner image
     */
    public function uploadBanner(string $filePath): array
    {
        return $this->client->upload($this->path('banner'), $filePath, 'banner');
    }

    /**
     * Delete banner image
     */
    public function deleteBanner(): array
    {
        return $this->client->delete($this->path('banner'));
    }

    /**
     * Get verification status
     */
    public function verificationStatus(): array
    {
        return $this->client->get($this->path('verification'));
    }

    /**
     * Request verification
     */
    public function requestVerification(array $documents = []): array
    {
        return $this->client->post($this->path('verification/request'), ['documents' => $documents]);
    }

    /**
     * Upload verification document
     */
    public function uploadVerificationDocument(string $filePath, string $documentType): array
    {
        return $this->client->upload($this->path('verification/documents'), $filePath, 'document', [
            'document_type' => $documentType,
        ]);
    }

    /**
     * Get social links
     */
    public function socialLinks(): array
    {
        return $this->client->get($this->path('social'));
    }

    /**
     * Update social links
     */
    public function updateSocialLinks(array $links): array
    {
        return $this->client->put($this->path('social'), ['links' => $links]);
    }

    /**
     * Get public profile URL
     */
    public function publicUrl(): array
    {
        return $this->client->get($this->path('public-url'));
    }

    /**
     * Update public profile slug
     */
    public function updateSlug(string $slug): array
    {
        return $this->client->put($this->path('slug'), ['slug' => $slug]);
    }

    /**
     * Check if slug is available
     */
    public function checkSlugAvailability(string $slug): array
    {
        return $this->client->get($this->path('slug/check'), ['slug' => $slug]);
    }

    /**
     * Get partner statistics
     */
    public function statistics(): array
    {
        return $this->client->get($this->path('statistics'));
    }

    /**
     * Get partner achievements/badges
     */
    public function achievements(): array
    {
        return $this->client->get($this->path('achievements'));
    }

    /**
     * Get partner tier/level information
     */
    public function tier(): array
    {
        return $this->client->get($this->path('tier'));
    }

    /**
     * Get branding settings
     */
    public function branding(): array
    {
        return $this->client->get($this->path('branding'));
    }

    /**
     * Update branding settings (colors, fonts, etc.)
     */
    public function updateBranding(array $branding): array
    {
        return $this->client->put($this->path('branding'), $branding);
    }

    /**
     * Get SEO settings
     */
    public function seo(): array
    {
        return $this->client->get($this->path('seo'));
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(array $seo): array
    {
        return $this->client->put($this->path('seo'), $seo);
    }
}
