<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Compliance management resource
 *
 * GDPR, DPA, Tax compliance, and data management
 */
class Compliance extends Resource
{
    protected string $basePath = 'partner/compliance';

    // === GDPR ===

    /**
     * Get GDPR compliance status
     */
    public function gdprStatus(): array
    {
        return $this->client->get($this->path('gdpr/status'));
    }

    /**
     * Get data processing agreements
     */
    public function dpaList(): array
    {
        return $this->client->get($this->path('gdpr/dpa'));
    }

    /**
     * Sign data processing agreement
     */
    public function signDpa(int $dpaId, array $signerInfo): array
    {
        return $this->client->post($this->path("gdpr/dpa/{$dpaId}/sign"), $signerInfo);
    }

    /**
     * Get current DPA document
     */
    public function getCurrentDpa(): array
    {
        return $this->client->get($this->path('gdpr/dpa/current'));
    }

    /**
     * Download signed DPA
     */
    public function downloadDpa(int $dpaId): array
    {
        return $this->client->get($this->path("gdpr/dpa/{$dpaId}/download"));
    }

    /**
     * Get data export request
     */
    public function dataExportRequests(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('gdpr/export-requests'), $params);
    }

    /**
     * Create data export request for a user
     */
    public function requestDataExport(int $userId): array
    {
        return $this->client->post($this->path('gdpr/export'), ['user_id' => $userId]);
    }

    /**
     * Get data export download URL
     */
    public function getDataExportUrl(int $requestId): array
    {
        return $this->client->get($this->path("gdpr/export/{$requestId}/download"));
    }

    /**
     * Get deletion requests
     */
    public function deletionRequests(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('gdpr/deletion-requests'), $params);
    }

    /**
     * Request user data deletion
     */
    public function requestDeletion(int $userId, string $reason = ''): array
    {
        return $this->client->post($this->path('gdpr/deletion'), [
            'user_id' => $userId,
            'reason' => $reason,
        ]);
    }

    /**
     * Get consent records
     */
    public function consents(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('gdpr/consents'), $params);
    }

    /**
     * Record user consent
     */
    public function recordConsent(int $userId, string $consentType, bool $granted): array
    {
        return $this->client->post($this->path('gdpr/consents'), [
            'user_id' => $userId,
            'consent_type' => $consentType,
            'granted' => $granted,
        ]);
    }

    /**
     * Get consent types
     */
    public function consentTypes(): array
    {
        return $this->client->get($this->path('gdpr/consent-types'));
    }

    /**
     * Get data retention settings
     */
    public function retentionSettings(): array
    {
        return $this->client->get($this->path('gdpr/retention'));
    }

    /**
     * Update data retention settings
     */
    public function updateRetentionSettings(array $settings): array
    {
        return $this->client->put($this->path('gdpr/retention'), $settings);
    }

    // === Tax Compliance ===

    /**
     * Get tax settings
     */
    public function taxSettings(): array
    {
        return $this->client->get($this->path('tax/settings'));
    }

    /**
     * Update tax settings
     */
    public function updateTaxSettings(array $settings): array
    {
        return $this->client->put($this->path('tax/settings'), $settings);
    }

    /**
     * Get tax rates
     */
    public function taxRates(array $params = []): array
    {
        return $this->client->get($this->path('tax/rates'), $params);
    }

    /**
     * Create tax rate
     */
    public function createTaxRate(array $data): array
    {
        return $this->client->post($this->path('tax/rates'), $data);
    }

    /**
     * Update tax rate
     */
    public function updateTaxRate(int $rateId, array $data): array
    {
        return $this->client->put($this->path("tax/rates/{$rateId}"), $data);
    }

    /**
     * Delete tax rate
     */
    public function deleteTaxRate(int $rateId): array
    {
        return $this->client->delete($this->path("tax/rates/{$rateId}"));
    }

    /**
     * Get VAT information
     */
    public function vatInfo(): array
    {
        return $this->client->get($this->path('tax/vat'));
    }

    /**
     * Update VAT information
     */
    public function updateVatInfo(array $data): array
    {
        return $this->client->put($this->path('tax/vat'), $data);
    }

    /**
     * Validate VAT number
     */
    public function validateVatNumber(string $vatNumber, string $countryCode): array
    {
        return $this->client->get($this->path('tax/vat/validate'), [
            'vat_number' => $vatNumber,
            'country_code' => $countryCode,
        ]);
    }

    /**
     * Get tax reports
     */
    public function taxReports(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('tax/reports'), $params);
    }

    /**
     * Generate tax report
     */
    public function generateTaxReport(string $startDate, string $endDate, string $type = 'summary'): array
    {
        return $this->client->post($this->path('tax/reports'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
        ]);
    }

    /**
     * Download tax report
     */
    public function downloadTaxReport(int $reportId, string $format = 'pdf'): array
    {
        return $this->client->get($this->path("tax/reports/{$reportId}/download"), ['format' => $format]);
    }

    // === Legal Documents ===

    /**
     * Get legal documents
     */
    public function legalDocuments(): array
    {
        return $this->client->get($this->path('legal'));
    }

    /**
     * Get terms of service
     */
    public function termsOfService(): array
    {
        return $this->client->get($this->path('legal/terms'));
    }

    /**
     * Get privacy policy
     */
    public function privacyPolicy(): array
    {
        return $this->client->get($this->path('legal/privacy'));
    }

    /**
     * Update custom legal document
     */
    public function updateLegalDocument(string $documentType, array $content): array
    {
        return $this->client->put($this->path("legal/{$documentType}"), $content);
    }

    /**
     * Accept terms
     */
    public function acceptTerms(string $documentType, string $version): array
    {
        return $this->client->post($this->path("legal/{$documentType}/accept"), ['version' => $version]);
    }

    // === Audit Logs ===

    /**
     * Get audit logs
     */
    public function auditLogs(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('audit'), $params);
    }

    /**
     * Get specific audit log entry
     */
    public function auditLog(int $logId): array
    {
        return $this->client->get($this->path("audit/{$logId}"));
    }

    /**
     * Export audit logs
     */
    public function exportAuditLogs(string $startDate, string $endDate, string $format = 'csv'): array
    {
        return $this->client->get($this->path('audit/export'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'format' => $format,
        ]);
    }

    // === Security & Certifications ===

    /**
     * Get security certifications
     */
    public function certifications(): array
    {
        return $this->client->get($this->path('certifications'));
    }

    /**
     * Get security status
     */
    public function securityStatus(): array
    {
        return $this->client->get($this->path('security'));
    }

    /**
     * Run compliance check
     */
    public function runComplianceCheck(): array
    {
        return $this->client->post($this->path('check'));
    }

    /**
     * Get compliance checklist
     */
    public function checklist(): array
    {
        return $this->client->get($this->path('checklist'));
    }

    /**
     * Mark checklist item complete
     */
    public function completeChecklistItem(int $itemId, array $evidence = []): array
    {
        return $this->client->post($this->path("checklist/{$itemId}/complete"), ['evidence' => $evidence]);
    }
}
