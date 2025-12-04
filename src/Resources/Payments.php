<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Resources;

use Sensei\PartnerSDK\Support\PaginatedResponse;

/**
 * Payment management resource
 *
 * Manage payments, refunds, and transactions
 */
class Payments extends Resource
{
    protected string $basePath = 'partner/payments';

    /**
     * List all payments
     */
    public function all(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path(), $params);
    }

    /**
     * Get a specific payment
     */
    public function get(int $id): array
    {
        return $this->find($id);
    }

    /**
     * Get payment by transaction ID
     */
    public function findByTransaction(string $transactionId): array
    {
        return $this->client->get($this->path('by-transaction'), ['transaction_id' => $transactionId]);
    }

    /**
     * Create a payment intent
     */
    public function createIntent(array $data): array
    {
        return $this->client->post($this->path('intent'), $data);
    }

    /**
     * Confirm a payment
     */
    public function confirm(string $paymentIntentId): array
    {
        return $this->client->post($this->path("intent/{$paymentIntentId}/confirm"));
    }

    /**
     * Cancel a payment
     */
    public function cancel(string $paymentIntentId): array
    {
        return $this->client->post($this->path("intent/{$paymentIntentId}/cancel"));
    }

    /**
     * Capture a payment (for authorized payments)
     */
    public function capture(string $paymentIntentId, ?int $amount = null): array
    {
        $data = $amount ? ['amount' => $amount] : [];
        return $this->client->post($this->path("intent/{$paymentIntentId}/capture"), $data);
    }

    // === Refunds ===

    /**
     * Create a refund
     */
    public function refund(int $paymentId, array $data = []): array
    {
        return $this->client->post($this->path("{$paymentId}/refund"), $data);
    }

    /**
     * Create a partial refund
     */
    public function partialRefund(int $paymentId, int $amount, string $reason = ''): array
    {
        return $this->client->post($this->path("{$paymentId}/refund"), [
            'amount' => $amount,
            'reason' => $reason,
        ]);
    }

    /**
     * List refunds
     */
    public function refunds(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('refunds'), $params);
    }

    /**
     * Get a specific refund
     */
    public function getRefund(int $refundId): array
    {
        return $this->client->get($this->path("refunds/{$refundId}"));
    }

    // === Invoices ===

    /**
     * List invoices
     */
    public function invoices(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('invoices'), $params);
    }

    /**
     * Get a specific invoice
     */
    public function invoice(int $invoiceId): array
    {
        return $this->client->get($this->path("invoices/{$invoiceId}"));
    }

    /**
     * Create an invoice
     */
    public function createInvoice(array $data): array
    {
        return $this->client->post($this->path('invoices'), $data);
    }

    /**
     * Send invoice to customer
     */
    public function sendInvoice(int $invoiceId): array
    {
        return $this->client->post($this->path("invoices/{$invoiceId}/send"));
    }

    /**
     * Mark invoice as paid
     */
    public function markInvoicePaid(int $invoiceId): array
    {
        return $this->client->post($this->path("invoices/{$invoiceId}/paid"));
    }

    /**
     * Void an invoice
     */
    public function voidInvoice(int $invoiceId): array
    {
        return $this->client->post($this->path("invoices/{$invoiceId}/void"));
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(int $invoiceId): array
    {
        return $this->client->get($this->path("invoices/{$invoiceId}/download"));
    }

    // === Payouts ===

    /**
     * List payouts (money received by partner)
     */
    public function payouts(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('payouts'), $params);
    }

    /**
     * Get a specific payout
     */
    public function payout(int $payoutId): array
    {
        return $this->client->get($this->path("payouts/{$payoutId}"));
    }

    /**
     * Get upcoming payout
     */
    public function upcomingPayout(): array
    {
        return $this->client->get($this->path('payouts/upcoming'));
    }

    /**
     * Get payout schedule
     */
    public function payoutSchedule(): array
    {
        return $this->client->get($this->path('payouts/schedule'));
    }

    // === Balance ===

    /**
     * Get current balance
     */
    public function balance(): array
    {
        return $this->client->get($this->path('balance'));
    }

    /**
     * Get balance history
     */
    public function balanceHistory(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('balance/history'), $params);
    }

    // === Disputes/Chargebacks ===

    /**
     * List disputes
     */
    public function disputes(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('disputes'), $params);
    }

    /**
     * Get a specific dispute
     */
    public function dispute(int $disputeId): array
    {
        return $this->client->get($this->path("disputes/{$disputeId}"));
    }

    /**
     * Submit dispute evidence
     */
    public function submitDisputeEvidence(int $disputeId, array $evidence): array
    {
        return $this->client->post($this->path("disputes/{$disputeId}/evidence"), $evidence);
    }

    /**
     * Accept dispute (concede)
     */
    public function acceptDispute(int $disputeId): array
    {
        return $this->client->post($this->path("disputes/{$disputeId}/accept"));
    }

    // === Coupons & Discounts ===

    /**
     * List coupons
     */
    public function coupons(array $params = []): PaginatedResponse
    {
        return $this->paginate($this->path('coupons'), $params);
    }

    /**
     * Get a specific coupon
     */
    public function coupon(int $couponId): array
    {
        return $this->client->get($this->path("coupons/{$couponId}"));
    }

    /**
     * Create a coupon
     */
    public function createCoupon(array $data): array
    {
        return $this->client->post($this->path('coupons'), $data);
    }

    /**
     * Update a coupon
     */
    public function updateCoupon(int $couponId, array $data): array
    {
        return $this->client->put($this->path("coupons/{$couponId}"), $data);
    }

    /**
     * Delete a coupon
     */
    public function deleteCoupon(int $couponId): array
    {
        return $this->client->delete($this->path("coupons/{$couponId}"));
    }

    /**
     * Validate a coupon code
     */
    public function validateCoupon(string $code, ?int $productId = null): array
    {
        $params = ['code' => $code];
        if ($productId) {
            $params['product_id'] = $productId;
        }
        return $this->client->get($this->path('coupons/validate'), $params);
    }

    // === Reports ===

    /**
     * Get payment statistics
     */
    public function statistics(array $params = []): array
    {
        return $this->client->get($this->path('statistics'), $params);
    }

    /**
     * Get transaction report
     */
    public function transactionReport(string $startDate, string $endDate): array
    {
        return $this->client->get($this->path('reports/transactions'), [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Export transactions
     */
    public function exportTransactions(string $format = 'csv', array $params = []): array
    {
        return $this->client->get($this->path('export'), array_merge($params, ['format' => $format]));
    }
}
