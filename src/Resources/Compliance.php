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
    protected string $basePath = 'v1/partners/compliance';

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
        return $this->client->get($this->path('dpa'));
    }

    /**
     * Sign data processing agreement
     */
    public function signDpa(int $dpaId, array $signerInfo): array
    {
        return $this->client->post($this->path('dpa/sign'), $signerInfo);
    }

    /**
     * Get current DPA document
     */
    public function getCurrentDpa(): array
    {
        return $this->client->get($this->path('dpa'));
    }

    /**
     * Download signed DPA
     */
    public function downloadDpa(int $dpaId): array
    {
        return $this->client->get($this->path('dpa/download'));
    }

    /**
     * Get data export requests list
     */
    public function dataExportRequests(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('gdpr/exports'), $params);
    }

    /**
     * Create data export request for a user
     */
    public function requestDataExport(int $userId): array
    {
        return $this->client->post($this->path('gdpr/export'), ['user_id' => $userId]);
    }

    /**
     * Get data export request by ID
     */
    public function getDataExportRequest(int $requestId): array
    {
        return $this->client->get($this->path("gdpr/exports/{$requestId}"));
    }

    /**
     * Get data export download URL
     */
    public function getDataExportUrl(int $requestId): array
    {
        return $this->client->get($this->path("gdpr/exports/{$requestId}/download"));
    }

    /**
     * Get deletion requests list
     */
    public function deletionRequests(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('gdpr/deletions'), $params);
    }

    /**
     * Get deletion request by ID
     */
    public function getDeletionRequest(int $requestId): array
    {
        return $this->client->get($this->path("gdpr/deletions/{$requestId}"));
    }

    /**
     * Request user data deletion
     */
    public function requestDeletion(int $userId, string $reason = ''): array
    {
        return $this->client->post($this->path('gdpr/delete'), [
            'user_id' => $userId,
            'reason' => $reason,
        ]);
    }

    /**
     * Approve a deletion request
     */
    public function approveDeletion(int $requestId): array
    {
        return $this->client->patch($this->path("gdpr/deletions/{$requestId}/approve"));
    }

    /**
     * Reject a deletion request
     */
    public function rejectDeletion(int $requestId, string $reason = ''): array
    {
        return $this->client->patch($this->path("gdpr/deletions/{$requestId}/reject"), [
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
     * Get data retention policies
     */
    public function retentionPolicies(array $params = []): array
    {
        return $this->client->get($this->path('retention/policies'), $params);
    }

    /**
     * Get data retention settings (alias for retentionPolicies)
     */
    public function retentionSettings(): array
    {
        return $this->retentionPolicies();
    }

    /**
     * Create data retention policy
     */
    public function createRetentionPolicy(array $data): array
    {
        return $this->client->post($this->path('retention/policies'), $data);
    }

    /**
     * Get specific retention policy
     */
    public function getRetentionPolicy(int $policyId): array
    {
        return $this->client->get($this->path("retention/policies/{$policyId}"));
    }

    /**
     * Update data retention policy
     */
    public function updateRetentionPolicy(int $policyId, array $settings): array
    {
        return $this->client->put($this->path("retention/policies/{$policyId}"), $settings);
    }

    /**
     * Update data retention settings (alias for updateRetentionPolicy)
     */
    public function updateRetentionSettings(array $settings): array
    {
        // For backwards compatibility, update first policy or create new one
        $policies = $this->retentionPolicies();
        if (!empty($policies['data'][0]['id'])) {
            return $this->updateRetentionPolicy($policies['data'][0]['id'], $settings);
        }
        return $this->createRetentionPolicy($settings);
    }

    /**
     * Delete retention policy
     */
    public function deleteRetentionPolicy(int $policyId): array
    {
        return $this->client->delete($this->path("retention/policies/{$policyId}"));
    }

    /**
     * Preview retention policy enforcement
     */
    public function previewRetentionPolicy(int $policyId): array
    {
        return $this->client->post($this->path("retention/policies/{$policyId}/preview"));
    }

    /**
     * Enforce retention policy
     */
    public function enforceRetentionPolicy(int $policyId): array
    {
        return $this->client->post($this->path("retention/policies/{$policyId}/enforce"));
    }

    /**
     * Get retention policy enforcement history
     */
    public function retentionPolicyHistory(int $policyId): array
    {
        return $this->client->get($this->path("retention/policies/{$policyId}/history"));
    }

    /**
     * Activate legal hold on retention policy
     */
    public function activateLegalHold(int $policyId): array
    {
        return $this->client->post($this->path("retention/policies/{$policyId}/legal-hold/activate"));
    }

    /**
     * Release legal hold on retention policy
     */
    public function releaseLegalHold(int $policyId): array
    {
        return $this->client->post($this->path("retention/policies/{$policyId}/legal-hold/release"));
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
