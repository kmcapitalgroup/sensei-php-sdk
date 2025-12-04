# Sensei Partner SDK - Developer Guide

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Installation & Setup](#installation--setup)
3. [Authentication](#authentication)
4. [Making API Requests](#making-api-requests)
5. [Resource Reference](#resource-reference)
6. [Error Handling](#error-handling)
7. [Pagination](#pagination)
8. [Webhooks](#webhooks)
9. [Testing](#testing)
10. [Best Practices](#best-practices)
11. [Troubleshooting](#troubleshooting)

---

## Architecture Overview

### SDK Structure

```
sensei-partner-sdk/
├── src/
│   ├── Configuration.php          # Immutable config object
│   ├── PartnerClient.php          # Main HTTP client
│   ├── Exceptions/
│   │   ├── SenseiPartnerException.php   # Base exception
│   │   ├── AuthenticationException.php  # 401/403 errors
│   │   ├── ValidationException.php      # 422 errors
│   │   ├── NotFoundException.php        # 404 errors
│   │   ├── RateLimitException.php       # 429 errors
│   │   └── ServerException.php          # 5xx errors
│   ├── Resources/
│   │   ├── Resource.php           # Base resource class
│   │   ├── Affiliates.php         # Affiliate program
│   │   ├── Analytics.php          # Analytics & reporting
│   │   ├── ApiKeys.php            # API key management
│   │   ├── Certificates.php       # Certificates & credentials
│   │   ├── Compliance.php         # GDPR, DPA, taxes
│   │   ├── Coupons.php            # Discount coupons
│   │   ├── Dashboard.php          # Dashboard stats
│   │   ├── Events.php             # Live events & webinars
│   │   ├── Forums.php             # Discussion forums
│   │   ├── Gamification.php       # XP, badges, achievements
│   │   ├── Guilds.php             # Guild/community management
│   │   ├── Media.php              # Media library
│   │   ├── Messages.php           # Messaging system
│   │   ├── Notifications.php      # Push, email, SMS
│   │   ├── Payments.php           # Payment management
│   │   ├── Products.php           # Products (formations, services)
│   │   ├── Profile.php            # Partner profile
│   │   ├── Reviews.php            # Product reviews
│   │   ├── Settings.php           # Partner settings
│   │   ├── StripeConnect.php      # Stripe Connect
│   │   ├── Subscriptions.php      # Subscription management
│   │   ├── Users.php              # User/customer management
│   │   └── Webhooks.php           # Webhook management
│   ├── Support/
│   │   └── PaginatedResponse.php  # Pagination helper
│   └── Laravel/
│       ├── SenseiPartnerServiceProvider.php
│       └── Facades/
│           └── SenseiPartner.php
├── config/
│   └── sensei-partner.php         # Laravel config
├── tests/
└── composer.json
```

### Available Resources (23 total)

| Resource | Property | Description |
|----------|----------|-------------|
| Affiliates | `$client->affiliates` | Affiliate program, referrals, commissions |
| Analytics | `$client->analytics` | Analytics and reporting |
| ApiKeys | `$client->apiKeys` | API key management |
| Certificates | `$client->certificates` | Certificates and credentials |
| Compliance | `$client->compliance` | GDPR, DPA, tax compliance |
| Coupons | `$client->coupons` | Discount coupons and promotions |
| Dashboard | `$client->dashboard` | Dashboard statistics |
| Events | `$client->events` | Live events, webinars, workshops |
| Forums | `$client->forums` | Discussion forums |
| Gamification | `$client->gamification` | XP, levels, badges, achievements |
| Guilds | `$client->guilds` | Guild/community management |
| Media | `$client->media` | Media library management |
| Messages | `$client->messages` | Direct messages, conversations |
| Notifications | `$client->notifications` | Push, email, SMS notifications |
| Payments | `$client->payments` | Payment management |
| Products | `$client->products` | Products (formations, services) |
| Profile | `$client->profile` | Partner profile management |
| Reviews | `$client->reviews` | Product reviews and ratings |
| Settings | `$client->settings` | Partner settings |
| StripeConnect | `$client->stripeConnect` | Stripe Connect integration |
| Subscriptions | `$client->subscriptions` | Subscription management |
| Users | `$client->users` | User/customer management |
| Webhooks | `$client->webhooks` | Webhook management |

### Design Patterns

- **Immutable Configuration**: All config values are set at construction and cannot be changed
- **Lazy Resource Loading**: Resources are instantiated only when first accessed
- **Fluent Interface**: Methods return appropriate types for chaining
- **PSR-7 Compliant**: Uses Guzzle HTTP client with PSR-7 messages

---

## Installation & Setup

### Requirements

- PHP 8.1 or higher
- Guzzle HTTP client 7.0+
- Laravel 10+ (optional)

### Composer Installation

```bash
composer require sensei/partner-sdk
```

### Manual Installation

1. Clone the repository
2. Add to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/sensei-partner-sdk"
        }
    ],
    "require": {
        "sensei/partner-sdk": "*"
    }
}
```

3. Run `composer update`

### Laravel Setup

The SDK auto-registers with Laravel. Publish the config:

```bash
php artisan vendor:publish --tag=sensei-partner-config
```

Add to `.env`:

```env
SENSEI_PARTNER_API_KEY=sk_live_your_key_here
SENSEI_PARTNER_BASE_URL=https://api.senseitemple.com
SENSEI_PARTNER_TIMEOUT=30
SENSEI_PARTNER_VERIFY_SSL=true
```

---

## Authentication

### API Key Authentication

```php
use Sensei\PartnerSDK\Configuration;
use Sensei\PartnerSDK\PartnerClient;

$config = new Configuration(
    apiKey: 'sk_live_your_api_key'
);

$client = new PartnerClient($config);
```

### Bearer Token Authentication

For user-specific operations:

```php
$config = new Configuration(
    bearerToken: 'user_access_token_here'
);

$client = new PartnerClient($config);
```

### Combined Authentication

```php
$config = new Configuration(
    apiKey: 'sk_live_xxx',
    bearerToken: 'user_token'
);
```

### Key Types

| Prefix | Type | Usage |
|--------|------|-------|
| `sk_live_` | Secret Live | Production server-side |
| `sk_test_` | Secret Test | Testing server-side |
| `pk_live_` | Public Live | Production client-side |
| `pk_test_` | Public Test | Testing client-side |

```php
// Check key type
if ($config->isSecretKey()) {
    // Server-side operations allowed
}

if ($config->isTestMode()) {
    // Using test environment
}
```

### Switching Authentication at Runtime

```php
// Create new client with different auth
$adminClient = $client->withApiKey('sk_live_admin_key');
$userClient = $client->withBearerToken('user_token');
```

---

## Making API Requests

### Using Resources (Recommended)

```php
// Access resources via magic properties
$products = $client->products->all();
$user = $client->users->get(123);
$stats = $client->dashboard->overview();
```

### Direct HTTP Methods

```php
// GET request
$response = $client->get('partner/custom-endpoint', [
    'filter' => 'active',
    'page' => 1
]);

// POST request
$response = $client->post('partner/custom-endpoint', [
    'name' => 'Test',
    'value' => 123
]);

// PUT request
$response = $client->put('partner/custom-endpoint/1', [
    'name' => 'Updated'
]);

// PATCH request
$response = $client->patch('partner/custom-endpoint/1', [
    'status' => 'active'
]);

// DELETE request
$response = $client->delete('partner/custom-endpoint/1');
```

### File Uploads

```php
// Upload a file
$response = $client->upload(
    endpoint: 'partner/products/123/images',
    filePath: '/path/to/image.jpg',
    fieldName: 'image',
    data: ['caption' => 'Product image']
);
```

### Request Options

```php
// Full request with options
$response = $client->request('POST', 'endpoint', [
    'json' => ['key' => 'value'],
    'headers' => ['X-Custom-Header' => 'value'],
    'query' => ['filter' => 'active'],
    'timeout' => 60,
]);
```

---

## Resource Reference

### Products Resource

```php
$products = $client->products;

// List all products
$list = $products->all(['status' => 'published', 'per_page' => 20]);

// Get single product
$product = $products->get(123);

// Create product
$new = $products->create([
    'title' => 'My Course',
    'description' => 'Course description',
    'type' => 'formation',
    'price' => 9900, // in cents
    'currency' => 'EUR'
]);

// Update product
$updated = $products->updateProduct(123, [
    'title' => 'Updated Title'
]);

// Delete product
$products->delete(123);

// Publish/Unpublish
$products->publish(123);
$products->unpublish(123);

// Duplicate
$copy = $products->duplicate(123);

// Get statistics
$stats = $products->stats(123);
```

#### Formations (Courses)

```php
// List formations
$formations = $products->formations(['category' => 'programming']);

// Create formation
$formation = $products->createFormation([
    'title' => 'PHP Mastery',
    'description' => 'Complete PHP course',
    'level' => 'intermediate',
    'duration_hours' => 40
]);

// Modules
$modules = $products->modules($formationId);
$module = $products->createModule($formationId, [
    'title' => 'Introduction',
    'description' => 'Getting started'
]);
$products->updateModule($formationId, $moduleId, ['title' => 'New Title']);
$products->deleteModule($formationId, $moduleId);
$products->reorderModules($formationId, [3, 1, 2]); // New order

// Lessons
$lessons = $products->lessons($formationId, $moduleId);
$lesson = $products->createLesson($formationId, $moduleId, [
    'title' => 'Lesson 1',
    'content' => 'Lesson content...',
    'video_url' => 'https://...',
    'duration_minutes' => 15
]);
$products->updateLesson($formationId, $moduleId, $lessonId, [...]);
$products->deleteLesson($formationId, $moduleId, $lessonId);
$products->reorderLessons($formationId, $moduleId, [2, 1, 3]);
```

#### Services

```php
$services = $products->services();
$service = $products->service(123);
$new = $products->createService([
    'title' => 'Consulting',
    'description' => '1-on-1 consulting',
    'price' => 15000,
    'duration_minutes' => 60
]);
$products->updateService(123, [...]);
$products->deleteService(123);
```

#### Pricing Tiers

```php
$tiers = $products->pricingTiers($productId);

$tier = $products->createPricingTier($productId, [
    'name' => 'Monthly',
    'price' => 2900,
    'interval' => 'month',
    'interval_count' => 1,
    'trial_days' => 7
]);

$products->updatePricingTier($productId, $tierId, ['price' => 3900]);
$products->deletePricingTier($productId, $tierId);
```

#### Media Management

```php
// Images
$products->uploadImage($productId, '/path/to/image.jpg');
$products->deleteImage($productId, $imageId);

// Videos
$products->uploadVideo($productId, '/path/to/video.mp4');
$products->deleteVideo($productId, $videoId);
```

#### Reviews

```php
$reviews = $products->reviews($productId, ['rating' => 5]);

// Respond to review
$products->respondToReview($productId, $reviewId, 'Thank you for your feedback!');

// Report inappropriate review
$products->reportReview($productId, $reviewId, 'Spam content');
```

### Subscriptions Resource

```php
$subscriptions = $client->subscriptions;

// List subscriptions
$list = $subscriptions->all([
    'status' => 'active',
    'product_id' => 123
]);

// Get subscription
$sub = $subscriptions->get(456);

// Create subscription
$new = $subscriptions->create([
    'user_id' => 123,
    'product_id' => 456,
    'pricing_tier_id' => 789,
    'payment_method_id' => 'pm_xxx'
]);

// Cancel subscription
$subscriptions->cancel($subId);                    // At period end
$subscriptions->cancel($subId, immediately: true); // Immediately

// Pause/Resume
$subscriptions->pause($subId);
$subscriptions->pause($subId, '2024-03-01'); // Resume date
$subscriptions->unpause($subId);
$subscriptions->resume($subId); // Resume cancelled

// Change plan
$subscriptions->changePlan($subId, $newTierId, prorate: true);

// Coupons
$subscriptions->applyCoupon($subId, 'DISCOUNT20');
$subscriptions->removeCoupon($subId);

// Extend subscription
$subscriptions->extend($subId, days: 30);

// Grant free access
$subscriptions->grantAccess(
    userId: 123,
    productId: 456,
    days: 30,
    reason: 'VIP customer'
);

// Revoke access
$subscriptions->revokeAccess($subId, 'Refund requested');

// Check access
$access = $subscriptions->checkAccess($userId, $productId);
// Returns: ['has_access' => true, 'expires_at' => '2024-12-31']

// Invoices
$invoices = $subscriptions->invoices($subId);
$upcoming = $subscriptions->upcomingInvoice($subId);

// Payment management
$subscriptions->retryPayment($subId);
$subscriptions->updatePaymentMethod($subId, 'pm_new_xxx');

// Analytics
$metrics = $subscriptions->metrics();
$churn = $subscriptions->churnAnalysis(['period' => 'month']);
$expiring = $subscriptions->expiringSoon(days: 7);

// History
$history = $subscriptions->history($subId);
```

### Users Resource

```php
$users = $client->users;

// List users
$list = $users->all(['status' => 'active']);

// Search
$results = $users->search('john@example.com');

// Find by email
$user = $users->findByEmail('john@example.com');

// Get user
$user = $users->get(123);

// Create user (invite)
$new = $users->create([
    'email' => 'new@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
    'send_invitation' => true
]);

// Update user
$users->updateUser(123, ['first_name' => 'Jane']);

// Delete user
$users->delete(123);

// User's subscriptions
$subs = $users->subscriptions($userId);
$active = $users->activeSubscriptions($userId);

// Purchase history
$purchases = $users->purchases($userId);

// Payments & Invoices
$payments = $users->payments($userId);
$invoices = $users->invoices($userId);

// Progress tracking
$progress = $users->progress($userId);
$progress = $users->progress($userId, $productId); // For specific product
$users->resetProgress($userId, $productId);

// Activity & Engagement
$activity = $users->activity($userId, ['limit' => 50]);
$engagement = $users->engagement($userId);

// Account management
$users->suspend($userId, 'Violation of terms');
$users->unsuspend($userId);
$users->ban($userId, 'Repeated violations');
$users->unban($userId);

// Email verification
$users->sendPasswordReset($userId);
$users->verifyEmail($userId);
$users->resendVerification($userId);

// Notes
$users->addNote($userId, 'Called about subscription');
$notes = $users->notes($userId);
$users->deleteNote($userId, $noteId);

// Tags
$users->addTag($userId, 'vip');
$users->removeTag($userId, 'trial');
$tags = $users->tags($userId);

// Import/Export
$users->import('/path/to/users.csv', ['skip_existing' => true]);
$export = $users->export('csv', ['segment_id' => 123]);

// Segments
$segments = $users->segments();
$segmentUsers = $users->usersInSegment($segmentId);
$segment = $users->createSegment([
    'name' => 'High Value',
    'rules' => [
        ['field' => 'ltv', 'operator' => '>', 'value' => 1000]
    ]
]);
$users->updateSegment($segmentId, [...]);
$users->deleteSegment($segmentId);

// Customer lifetime value
$ltv = $users->lifetimeValue($userId);
```

### Dashboard Resource

```php
$dashboard = $client->dashboard;

// Overview
$overview = $dashboard->overview();

// Summary
$summary = $dashboard->summary([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31'
]);

// KPIs
$kpis = $dashboard->kpis();

// Revenue
$revenue = $dashboard->revenue();
$byProduct = $dashboard->revenueByProduct();
$byPeriod = $dashboard->revenueByPeriod('month');

// Subscribers
$subscribers = $dashboard->subscribers();
$newSubs = $dashboard->newSubscribers(['period' => 'week']);
$growth = $dashboard->subscriberGrowth();

// Products
$performance = $dashboard->productPerformance();
$top = $dashboard->topProducts(limit: 10);

// Activity
$activity = $dashboard->activity(limit: 20);

// Notifications
$notifications = $dashboard->notifications();
$dashboard->markNotificationRead($notificationId);
$dashboard->markAllNotificationsRead();

// Metrics
$funnel = $dashboard->conversionFunnel();
$churn = $dashboard->churnRate();
$ltv = $dashboard->ltv();
$mrr = $dashboard->mrr();
$arr = $dashboard->arr();

// Engagement & Satisfaction
$engagement = $dashboard->engagement();
$satisfaction = $dashboard->satisfaction();

// Goals
$goals = $dashboard->goals();
$dashboard->updateGoal($goalId, ['target' => 10000]);

// Comparison
$comparison = $dashboard->comparison('month'); // vs previous period

// Export
$export = $dashboard->export('csv', ['metrics' => ['revenue', 'subscribers']]);
```

### Analytics Resource

```php
$analytics = $client->analytics;

// Overview
$overview = $analytics->overview([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31'
]);

// Revenue
$revenue = $analytics->revenue();
$rangeRevenue = $analytics->revenueByDateRange(
    startDate: '2024-01-01',
    endDate: '2024-03-31',
    granularity: 'day' // day, week, month
);

// Products
$productAnalytics = $analytics->products();
$singleProduct = $analytics->product($productId);

// Users
$userAnalytics = $analytics->users();
$acquisition = $analytics->acquisition();
$retention = $analytics->retention();

// Cohorts
$cohorts = $analytics->cohorts([
    'cohort_type' => 'signup_month',
    'metric' => 'retention'
]);

// Engagement
$engagement = $analytics->engagement();
$contentEngagement = $analytics->contentEngagement();
$completionRates = $analytics->completionRates();

// Funnels
$funnel = $analytics->funnel('checkout', [
    'start_date' => '2024-01-01'
]);

// Conversions
$conversions = $analytics->conversions();

// Traffic & Geography
$trafficSources = $analytics->trafficSources();
$geographic = $analytics->geographic();
$devices = $analytics->devices();

// Real-time
$realtime = $analytics->realtime();

// Time analysis
$timeAnalysis = $analytics->timeAnalysis(); // Peak hours, days

// Financial
$refunds = $analytics->refunds();
$disputes = $analytics->disputes();

// Custom Reports
$report = $analytics->createReport([
    'name' => 'Monthly Performance',
    'metrics' => ['revenue', 'new_subscribers', 'churn'],
    'dimensions' => ['product', 'plan'],
    'filters' => ['status' => 'active']
]);

$savedReports = $analytics->reports();
$report = $analytics->getReport($reportId);
$analytics->deleteReport($reportId);

// Scheduled Reports
$scheduled = $analytics->scheduleReport([
    'report_id' => 123,
    'frequency' => 'weekly',
    'recipients' => ['team@company.com']
]);
$scheduledList = $analytics->scheduledReports();
$analytics->deleteScheduledReport($scheduleId);

// Export
$export = $analytics->export('revenue', 'csv', [
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31'
]);

// Compare periods
$comparison = $analytics->compare(
    period1Start: '2024-01-01',
    period1End: '2024-03-31',
    period2Start: '2023-01-01',
    period2End: '2023-03-31',
    metrics: ['revenue', 'subscribers']
);

// Benchmarks
$benchmarks = $analytics->benchmarks();

// AI Insights
$insights = $analytics->insights();
$anomalies = $analytics->anomalies();
$predictions = $analytics->predictions('revenue', ['horizon' => 30]);
```

### Payments Resource

```php
$payments = $client->payments;

// List payments
$list = $payments->all(['status' => 'succeeded']);

// Get payment
$payment = $payments->get($paymentId);
$payment = $payments->findByTransaction('txn_xxx');

// Payment intents
$intent = $payments->createIntent([
    'amount' => 9900,
    'currency' => 'eur',
    'customer_id' => 123
]);
$payments->confirm($intentId);
$payments->cancel($intentId);
$payments->capture($intentId);
$payments->capture($intentId, amount: 5000); // Partial capture

// Refunds
$refund = $payments->refund($paymentId);
$partialRefund = $payments->partialRefund($paymentId, 5000, 'Customer request');
$refunds = $payments->refunds(['status' => 'pending']);
$refund = $payments->getRefund($refundId);

// Invoices
$invoices = $payments->invoices();
$invoice = $payments->invoice($invoiceId);
$newInvoice = $payments->createInvoice([
    'customer_id' => 123,
    'items' => [
        ['description' => 'Consulting', 'amount' => 15000]
    ]
]);
$payments->sendInvoice($invoiceId);
$payments->markInvoicePaid($invoiceId);
$payments->voidInvoice($invoiceId);
$pdf = $payments->downloadInvoice($invoiceId);

// Payouts
$payouts = $payments->payouts();
$payout = $payments->payout($payoutId);
$upcoming = $payments->upcomingPayout();
$schedule = $payments->payoutSchedule();

// Balance
$balance = $payments->balance();
$history = $payments->balanceHistory();

// Disputes
$disputes = $payments->disputes();
$dispute = $payments->dispute($disputeId);
$payments->submitDisputeEvidence($disputeId, [
    'customer_communication' => '...',
    'receipt' => '...'
]);
$payments->acceptDispute($disputeId);

// Coupons
$coupons = $payments->coupons();
$coupon = $payments->coupon($couponId);
$newCoupon = $payments->createCoupon([
    'code' => 'SUMMER20',
    'discount_type' => 'percent',
    'discount_value' => 20,
    'max_uses' => 100,
    'expires_at' => '2024-08-31'
]);
$payments->updateCoupon($couponId, ['max_uses' => 200]);
$payments->deleteCoupon($couponId);
$validation = $payments->validateCoupon('SUMMER20', $productId);

// Reports
$stats = $payments->statistics(['period' => 'month']);
$report = $payments->transactionReport('2024-01-01', '2024-12-31');
$export = $payments->exportTransactions('csv', ['status' => 'succeeded']);
```

### Stripe Connect Resource

```php
$stripe = $client->stripeConnect;

// Account status
$status = $stripe->status();
// Returns: charges_enabled, payouts_enabled, details_submitted

// Check if fully onboarded
if ($stripe->isFullyOnboarded()) {
    // Ready to accept payments
}

// Onboarding
$onboarding = $stripe->onboardingUrl(
    returnUrl: 'https://myapp.com/stripe/return',
    refreshUrl: 'https://myapp.com/stripe/refresh'
);
// Redirect user to $onboarding['url']

$stripe->completeOnboarding();

// Dashboard access
$dashboardLink = $stripe->dashboardLink();
$loginLink = $stripe->loginLink(); // Express accounts

// Account details
$account = $stripe->account();
$stripe->updateAccount([
    'business_type' => 'individual',
    'business_profile' => [
        'name' => 'My Business'
    ]
]);

// Capabilities
$capabilities = $stripe->capabilities();

// Verification
$requirements = $stripe->verificationRequirements();
$stripe->uploadDocument('/path/to/id.jpg', 'identity_document');

// Bank accounts
$bankAccounts = $stripe->bankAccounts();
$stripe->addBankAccount([
    'country' => 'FR',
    'currency' => 'eur',
    'account_holder_name' => 'John Doe',
    'account_holder_type' => 'individual',
    'iban' => 'FR...'
]);
$stripe->setDefaultBankAccount($bankAccountId);
$stripe->deleteBankAccount($bankAccountId);

// Payout settings
$payoutSettings = $stripe->payoutSettings();
$stripe->updatePayoutSettings([
    'schedule' => ['interval' => 'weekly', 'weekly_anchor' => 'monday']
]);

// Balance
$balance = $stripe->balance();
$transactions = $stripe->balanceTransactions(['limit' => 100]);

// Instant payout
$payout = $stripe->instantPayout(amount: 10000, currency: 'eur');

// Transfer schedule
$schedule = $stripe->transferSchedule();
$stripe->updateTransferSchedule([
    'interval' => 'daily'
]);

// Application fees
$feeRate = $stripe->applicationFeeRate();

// Tax settings
$taxSettings = $stripe->taxSettings();
$stripe->updateTaxSettings([
    'collect_tax' => true,
    'tax_rates' => [...]
]);

// Disconnect
$stripe->disconnect();
```

### API Keys Resource

```php
$apiKeys = $client->apiKeys;

// List keys
$keys = $apiKeys->all();

// Get key
$key = $apiKeys->get($keyId);

// Create key
$newKey = $apiKeys->create([
    'name' => 'Production API Key',
    'permissions' => [
        'products:read',
        'products:write',
        'subscriptions:read'
    ],
    'expires_at' => '2025-12-31',
    'rate_limit' => 1000, // requests per minute
    'allowed_ips' => ['192.168.1.1'],
    'allowed_domains' => ['myapp.com']
]);
// IMPORTANT: Save $newKey['secret'] - only shown once!

// Update key
$apiKeys->updateKey($keyId, ['name' => 'New Name']);

// Delete key
$apiKeys->delete($keyId);

// Regenerate secret
$regenerated = $apiKeys->regenerate($keyId);
// Save $regenerated['secret']

// Verify key
$verification = $apiKeys->verify('api_key_here');

// Usage statistics
$usage = $apiKeys->usage($keyId, [
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31'
]);

// Available permissions
$permissions = $apiKeys->permissions();

// Update permissions
$apiKeys->updatePermissions($keyId, ['products:read', 'products:write']);

// Enable/Disable
$apiKeys->enable($keyId);
$apiKeys->disable($keyId);

// Request logs
$logs = $apiKeys->logs($keyId, [
    'status' => 'error',
    'limit' => 100
]);
```

### Webhooks Resource

```php
$webhooks = $client->webhooks;

// List webhooks
$list = $webhooks->all();

// Get webhook
$webhook = $webhooks->get($webhookId);

// Create webhook
$newWebhook = $webhooks->create([
    'url' => 'https://myapp.com/webhooks/sensei',
    'events' => [
        'subscription.created',
        'subscription.cancelled',
        'subscription.renewed',
        'payment.succeeded',
        'payment.failed',
        'user.created'
    ],
    'description' => 'Main webhook endpoint',
    'enabled' => true
]);
// Save $newWebhook['secret'] for signature verification

// Update webhook
$webhooks->updateWebhook($webhookId, [
    'url' => 'https://newurl.com/webhooks'
]);

// Delete webhook
$webhooks->delete($webhookId);

// Enable/Disable
$webhooks->enable($webhookId);
$webhooks->disable($webhookId);

// Regenerate secret
$newSecret = $webhooks->regenerateSecret($webhookId);

// Test webhook
$webhooks->test($webhookId, 'subscription.created');

// Available events
$events = $webhooks->eventTypes();

// Delivery attempts
$deliveries = $webhooks->deliveries($webhookId);
$delivery = $webhooks->delivery($webhookId, $deliveryId);
$webhooks->retryDelivery($webhookId, $deliveryId);

// Logs
$logs = $webhooks->logs($webhookId, ['status' => 'failed']);

// Statistics
$stats = $webhooks->statistics($webhookId);

// Subscribe/Unsubscribe to events
$webhooks->subscribe($webhookId, ['refund.created']);
$webhooks->unsubscribe($webhookId, ['user.created']);
$subscribedEvents = $webhooks->subscribedEvents($webhookId);
```

#### Webhook Signature Verification

```php
// In your webhook handler
use Sensei\PartnerSDK\Resources\Webhooks;

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_SENSEI_SIGNATURE'];
$secret = 'whsec_your_webhook_secret';

if (!Webhooks::verifySignature($payload, $signature, $secret)) {
    http_response_code(401);
    exit('Invalid signature');
}

$event = Webhooks::parsePayload($payload);

switch ($event['type']) {
    case 'subscription.created':
        handleNewSubscription($event['data']);
        break;
    case 'payment.succeeded':
        handlePaymentSuccess($event['data']);
        break;
    // ...
}

http_response_code(200);
```

### Compliance Resource

```php
$compliance = $client->compliance;

// GDPR Status
$status = $compliance->gdprStatus();

// Data Processing Agreements
$dpas = $compliance->dpaList();
$currentDpa = $compliance->getCurrentDpa();
$compliance->signDpa($dpaId, [
    'signer_name' => 'John Doe',
    'signer_email' => 'john@company.com',
    'signer_title' => 'CEO',
    'company_name' => 'My Company SAS'
]);
$pdf = $compliance->downloadDpa($dpaId);

// Data Export (GDPR Right of Access)
$requests = $compliance->dataExportRequests();
$export = $compliance->requestDataExport($userId);
$downloadUrl = $compliance->getDataExportUrl($requestId);

// Data Deletion (GDPR Right to Erasure)
$deletionRequests = $compliance->deletionRequests();
$deletion = $compliance->requestDeletion($userId, 'User requested via support');

// Consent Management
$consents = $compliance->consents(['user_id' => 123]);
$compliance->recordConsent(
    userId: 123,
    consentType: 'marketing_emails',
    granted: true
);
$consentTypes = $compliance->consentTypes();

// Data Retention
$retentionSettings = $compliance->retentionSettings();
$compliance->updateRetentionSettings([
    'user_data_retention_days' => 365,
    'payment_data_retention_days' => 2555 // 7 years
]);

// Tax Compliance
$taxSettings = $compliance->taxSettings();
$compliance->updateTaxSettings([
    'collect_tax' => true,
    'tax_calculation' => 'automatic'
]);

$taxRates = $compliance->taxRates();
$rate = $compliance->createTaxRate([
    'name' => 'France VAT',
    'percentage' => 20,
    'country' => 'FR',
    'inclusive' => false
]);
$compliance->updateTaxRate($rateId, ['percentage' => 21]);
$compliance->deleteTaxRate($rateId);

// VAT
$vatInfo = $compliance->vatInfo();
$compliance->updateVatInfo([
    'vat_number' => 'FR12345678901',
    'business_name' => 'My Company SAS',
    'address' => '123 Rue Example, Paris'
]);
$validation = $compliance->validateVatNumber('FR12345678901', 'FR');

// Tax Reports
$reports = $compliance->taxReports();
$report = $compliance->generateTaxReport(
    startDate: '2024-01-01',
    endDate: '2024-03-31',
    type: 'summary' // summary, detailed
);
$pdf = $compliance->downloadTaxReport($reportId, 'pdf');

// Legal Documents
$documents = $compliance->legalDocuments();
$tos = $compliance->termsOfService();
$privacy = $compliance->privacyPolicy();
$compliance->updateLegalDocument('terms', [
    'content' => '...',
    'version' => '2.0'
]);
$compliance->acceptTerms('terms', '2.0');

// Audit Logs
$auditLogs = $compliance->auditLogs([
    'action' => 'user.delete',
    'start_date' => '2024-01-01'
]);
$log = $compliance->auditLog($logId);
$export = $compliance->exportAuditLogs(
    startDate: '2024-01-01',
    endDate: '2024-12-31',
    format: 'csv'
);

// Security & Certifications
$certifications = $compliance->certifications();
$securityStatus = $compliance->securityStatus();

// Compliance Check
$check = $compliance->runComplianceCheck();
$checklist = $compliance->checklist();
$compliance->completeChecklistItem($itemId, [
    'evidence' => 'Implemented on 2024-01-15'
]);
```

### Profile Resource

```php
$profile = $client->profile;

// Get profile
$myProfile = $profile->get();

// Update profile
$profile->updateProfile([
    'display_name' => 'My Company',
    'bio' => 'We help developers learn...',
    'website' => 'https://mycompany.com'
]);

// Business info
$business = $profile->business();
$profile->updateBusiness([
    'legal_name' => 'My Company SAS',
    'registration_number' => 'XXX',
    'vat_number' => 'FR...',
    'address' => [
        'line1' => '123 Rue Example',
        'city' => 'Paris',
        'postal_code' => '75001',
        'country' => 'FR'
    ]
]);

// Contact info
$contact = $profile->contact();
$profile->updateContact([
    'email' => 'contact@company.com',
    'phone' => '+33123456789',
    'support_email' => 'support@company.com'
]);

// Media
$profile->uploadLogo('/path/to/logo.png');
$profile->deleteLogo();
$profile->uploadBanner('/path/to/banner.jpg');
$profile->deleteBanner();

// Verification
$status = $profile->verificationStatus();
$profile->requestVerification();
$profile->uploadVerificationDocument('/path/to/id.jpg', 'identity');

// Social links
$social = $profile->socialLinks();
$profile->updateSocialLinks([
    'twitter' => 'https://twitter.com/company',
    'linkedin' => 'https://linkedin.com/company/xxx',
    'youtube' => 'https://youtube.com/@company'
]);

// Public profile
$publicUrl = $profile->publicUrl();
$profile->updateSlug('my-company');
$available = $profile->checkSlugAvailability('new-slug');

// Stats & Achievements
$stats = $profile->statistics();
$achievements = $profile->achievements();
$tier = $profile->tier();

// Branding
$branding = $profile->branding();
$profile->updateBranding([
    'primary_color' => '#3B82F6',
    'secondary_color' => '#1E40AF',
    'logo_url' => '...',
    'favicon_url' => '...'
]);

// SEO
$seo = $profile->seo();
$profile->updateSeo([
    'meta_title' => 'My Company - Learn Programming',
    'meta_description' => '...',
    'og_image' => '...'
]);
```

### Settings Resource

```php
$settings = $client->settings;

// Get all settings
$all = $settings->all();

// Get/Set individual settings
$value = $settings->get('email_notifications');
$settings->set('email_notifications', true);

// Bulk update
$settings->updateBulk([
    'email_notifications' => true,
    'sms_notifications' => false,
    'timezone' => 'Europe/Paris'
]);

// Reset
$settings->reset('email_notifications');
$settings->resetAll();

// Notifications
$notifications = $settings->notifications();
$settings->updateNotifications([
    'new_subscriber' => true,
    'payment_received' => true,
    'subscription_cancelled' => true
]);

$emailNotifications = $settings->emailNotifications();
$settings->updateEmailNotifications([
    'digest_frequency' => 'daily',
    'include_revenue' => true
]);

// Security
$security = $settings->security();
$settings->updateSecurity([
    'session_timeout' => 3600,
    'ip_whitelist' => ['192.168.1.1']
]);

// Two-factor authentication
$setup = $settings->enable2fa();
// Show $setup['qr_code'] to user
$settings->verify2fa($code); // User enters code from authenticator
$settings->disable2fa($code);

// Backup codes
$backupCodes = $settings->getBackupCodes();
$newCodes = $settings->regenerateBackupCodes();

// Sessions
$sessions = $settings->sessions();
$settings->revokeSession($sessionId);
$settings->revokeAllSessions();

// Payment settings
$payment = $settings->payment();
$settings->updatePayment([
    'default_currency' => 'EUR',
    'auto_invoice' => true
]);

// Payout settings
$payout = $settings->payout();
$settings->updatePayout([
    'schedule' => 'weekly',
    'minimum_amount' => 10000
]);

// Currency & Localization
$currency = $settings->currency();
$settings->updateCurrency([
    'default' => 'EUR',
    'display_format' => 'symbol' // symbol, code
]);
$supported = $settings->supportedCurrencies();

$localization = $settings->localization();
$settings->updateLocalization([
    'language' => 'fr',
    'timezone' => 'Europe/Paris',
    'date_format' => 'DD/MM/YYYY'
]);

// Email settings
$email = $settings->email();
$settings->updateEmail([
    'from_name' => 'My Company',
    'from_email' => 'noreply@company.com',
    'reply_to' => 'support@company.com'
]);

// Email templates
$templates = $settings->emailTemplates();
$settings->updateEmailTemplate('welcome', [
    'subject' => 'Welcome to {{company_name}}!',
    'body' => '...'
]);
$preview = $settings->previewEmailTemplate('welcome', [
    'user_name' => 'John'
]);
$settings->sendTestEmail('welcome', 'test@example.com');

// Integrations
$integrations = $settings->integrations();
$settings->updateIntegration('mailchimp', [
    'api_key' => 'xxx',
    'list_id' => 'yyy'
]);
$test = $settings->testIntegration('mailchimp');
```

### Guilds Resource

```php
$guilds = $client->guilds;

// List guilds
$list = $guilds->all(['status' => 'active']);

// Get guild
$guild = $guilds->get(123);

// Create guild
$new = $guilds->create([
    'name' => 'Premium Community',
    'description' => 'Exclusive community for premium members',
    'type' => 'private',
    'max_members' => 500
]);

// Update guild
$guilds->updateGuild(123, ['name' => 'VIP Community']);

// Delete guild
$guilds->delete(123);

// Members
$members = $guilds->members($guildId, ['role' => 'moderator']);
$guilds->addMember($guildId, $userId, 'member');
$guilds->removeMember($guildId, $userId);
$guilds->updateMemberRole($guildId, $userId, 'moderator');
$guilds->banMember($guildId, $userId, 'Spam activity');
$guilds->unbanMember($guildId, $userId);
$banned = $guilds->bannedMembers($guildId);

// Invitations
$invite = $guilds->createInvite($guildId, ['max_uses' => 10, 'expires_in' => 86400]);
$guilds->revokeInvite($guildId, $inviteCode);
$invites = $guilds->invites($guildId);

// Channels
$channels = $guilds->channels($guildId);
$guilds->createChannel($guildId, ['name' => 'general', 'type' => 'text']);
$guilds->updateChannel($guildId, $channelId, ['name' => 'announcements']);
$guilds->deleteChannel($guildId, $channelId);

// Categories
$categories = $guilds->categories($guildId);
$guilds->createCategory($guildId, ['name' => 'Discussion']);
$guilds->updateCategory($guildId, $categoryId, ['name' => 'General Discussion']);
$guilds->deleteCategory($guildId, $categoryId);

// Roles
$roles = $guilds->roles($guildId);
$guilds->createRole($guildId, ['name' => 'VIP', 'permissions' => ['read', 'write']]);
$guilds->updateRole($guildId, $roleId, ['permissions' => ['read', 'write', 'moderate']]);
$guilds->deleteRole($guildId, $roleId);
$guilds->assignRole($guildId, $userId, $roleId);
$guilds->removeRole($guildId, $userId, $roleId);

// Settings
$settings = $guilds->settings($guildId);
$guilds->updateSettings($guildId, ['allow_join_requests' => true]);

// Statistics
$stats = $guilds->statistics($guildId);
$activity = $guilds->activityStats($guildId, ['period' => 'month']);
```

### Messages Resource

```php
$messages = $client->messages;

// Conversations
$conversations = $messages->conversations(['status' => 'active']);
$conversation = $messages->conversation($conversationId);
$messages->startConversation($userId, $recipientId, 'Hello!');
$messages->archiveConversation($conversationId);
$messages->unarchiveConversation($conversationId);
$messages->deleteConversation($conversationId);
$messages->markAsRead($conversationId);
$messages->markAsUnread($conversationId);

// Messages
$msgs = $messages->messages($conversationId, ['per_page' => 50]);
$messages->send($conversationId, 'Your message content here');
$messages->reply($conversationId, $messageId, 'Reply content');
$messages->edit($conversationId, $messageId, 'Updated content');
$messages->deleteMessage($conversationId, $messageId);

// Attachments
$messages->sendWithAttachment($conversationId, 'Check this file', '/path/to/file.pdf');

// Group conversations
$group = $messages->createGroup([
    'name' => 'Project Team',
    'participants' => [1, 2, 3, 4]
]);
$messages->addParticipant($conversationId, $userId);
$messages->removeParticipant($conversationId, $userId);
$messages->leaveGroup($conversationId);

// Search
$results = $messages->search('keyword', ['conversation_id' => $conversationId]);

// Statistics
$stats = $messages->statistics();
$unread = $messages->unreadCount();

// Settings
$prefs = $messages->preferences();
$messages->updatePreferences(['email_notifications' => true]);

// Block
$messages->blockUser($userId);
$messages->unblockUser($userId);
$blocked = $messages->blockedUsers();
```

### Gamification Resource

```php
$gamification = $client->gamification;

// XP & Levels
$xp = $gamification->xp($userId);
$gamification->awardXp($userId, 100, 'Completed course');
$gamification->deductXp($userId, 50, 'Penalty');
$level = $gamification->level($userId);
$progress = $gamification->levelProgress($userId);

// Leaderboard
$leaderboard = $gamification->leaderboard(['period' => 'week', 'limit' => 100]);
$rank = $gamification->userRank($userId);
$topPerformers = $gamification->topPerformers(10);

// Badges
$badges = $gamification->badges();
$userBadges = $gamification->userBadges($userId);
$gamification->awardBadge($userId, $badgeId, 'First course completion');
$gamification->revokeBadge($userId, $badgeId);
$gamification->createBadge([
    'name' => 'Early Adopter',
    'description' => 'Joined in the first month',
    'icon' => 'star',
    'criteria' => ['type' => 'signup_date', 'value' => '2024-01-31']
]);
$gamification->updateBadge($badgeId, ['name' => 'Founding Member']);
$gamification->deleteBadge($badgeId);

// Achievements
$achievements = $gamification->achievements();
$userAchievements = $gamification->userAchievements($userId);
$gamification->unlockAchievement($userId, $achievementId);
$gamification->createAchievement([
    'name' => 'Course Master',
    'description' => 'Complete 10 courses',
    'criteria' => ['courses_completed' => 10],
    'xp_reward' => 500
]);
$gamification->updateAchievement($achievementId, ['xp_reward' => 750]);
$gamification->deleteAchievement($achievementId);

// Streaks
$streak = $gamification->streak($userId);
$gamification->recordActivity($userId, 'login');
$history = $gamification->streakHistory($userId);

// Challenges
$challenges = $gamification->challenges(['status' => 'active']);
$gamification->createChallenge([
    'name' => 'Summer Learning Sprint',
    'description' => 'Complete 5 courses in July',
    'start_date' => '2024-07-01',
    'end_date' => '2024-07-31',
    'goal' => ['courses_completed' => 5],
    'reward_xp' => 1000
]);
$gamification->joinChallenge($userId, $challengeId);
$progress = $gamification->challengeProgress($userId, $challengeId);
$winners = $gamification->challengeWinners($challengeId);

// Statistics
$stats = $gamification->statistics();
$userStats = $gamification->userStats($userId);

// Settings
$settings = $gamification->settings();
$gamification->updateSettings(['xp_multiplier' => 1.5]);
```

### Events Resource

```php
$events = $client->events;

// List events
$list = $events->all(['status' => 'upcoming']);
$upcoming = $events->upcoming();
$past = $events->past();

// Get event
$event = $events->get(123);

// Create event
$new = $events->create([
    'title' => 'Live Masterclass',
    'description' => 'Learn advanced techniques',
    'type' => 'webinar',
    'start_date' => '2024-03-15 10:00:00',
    'end_date' => '2024-03-15 12:00:00',
    'timezone' => 'Europe/Paris',
    'max_attendees' => 100,
    'price' => 4900
]);

// Update event
$events->updateEvent(123, ['title' => 'Updated Masterclass Title']);

// Delete event
$events->delete(123);

// Cancel event
$events->cancel(123, 'Scheduling conflict');

// Reschedule
$events->reschedule(123, '2024-03-20 10:00:00', '2024-03-20 12:00:00');

// Duplicate
$events->duplicate(123);

// Registration
$events->register($eventId, $userId);
$events->unregister($eventId, $userId);
$registrations = $events->registrations($eventId);
$events->confirmRegistration($eventId, $userId);
$events->cancelRegistration($eventId, $userId);
$waitlist = $events->waitlist($eventId);
$events->promoteFromWaitlist($eventId, $userId);

// Attendance
$events->checkIn($eventId, $userId);
$events->checkOut($eventId, $userId);
$attendance = $events->attendance($eventId);

// Streaming
$streamInfo = $events->streamInfo($eventId);
$events->startStream($eventId);
$events->endStream($eventId);

// Recordings
$recordings = $events->recordings($eventId);
$events->uploadRecording($eventId, '/path/to/recording.mp4');
$events->deleteRecording($eventId, $recordingId);

// Reminders
$events->sendReminder($eventId);
$events->scheduleReminder($eventId, '2024-03-14 10:00:00');

// Statistics
$stats = $events->statistics();
$eventStats = $events->eventStatistics($eventId);
```

### Certificates Resource

```php
$certificates = $client->certificates;

// List certificates
$list = $certificates->all(['status' => 'active']);

// Get certificate
$cert = $certificates->get(123);

// Create template
$template = $certificates->createTemplate([
    'name' => 'Course Completion',
    'design' => 'modern',
    'fields' => ['recipient_name', 'course_name', 'completion_date'],
    'background_color' => '#ffffff'
]);

// Update template
$certificates->updateTemplate($templateId, ['name' => 'Updated Template']);

// Delete template
$certificates->deleteTemplate($templateId);

// List templates
$templates = $certificates->templates();

// Issue certificate
$issued = $certificates->issue([
    'template_id' => $templateId,
    'user_id' => $userId,
    'product_id' => $productId,
    'issued_date' => '2024-01-15',
    'custom_fields' => ['grade' => 'A+']
]);

// Revoke certificate
$certificates->revoke($certificateId, 'Course not completed');

// Verify certificate
$valid = $certificates->verify($certificateCode);

// Download
$pdf = $certificates->download($certificateId);

// User's certificates
$userCerts = $certificates->userCertificates($userId);

// Product certificates
$productCerts = $certificates->productCertificates($productId);

// Bulk issue
$certificates->issueBulk($templateId, [
    ['user_id' => 1, 'custom_fields' => ['grade' => 'A']],
    ['user_id' => 2, 'custom_fields' => ['grade' => 'B+']],
]);

// Statistics
$stats = $certificates->statistics();
```

### Notifications Resource

```php
$notifications = $client->notifications;

// List notifications
$list = $notifications->all(['type' => 'push']);
$unread = $notifications->unread();

// Get notification
$notification = $notifications->get(123);

// Send notification
$notifications->send([
    'type' => 'push',
    'user_id' => $userId,
    'title' => 'New Course Available',
    'body' => 'Check out our latest course!',
    'action_url' => '/courses/123'
]);

// Bulk send
$notifications->sendBulk([
    'type' => 'email',
    'user_ids' => [1, 2, 3],
    'template' => 'new_course',
    'data' => ['course_name' => 'Advanced PHP']
]);

// Send to segment
$notifications->sendToSegment($segmentId, [
    'type' => 'push',
    'title' => 'Special Offer',
    'body' => '50% off this weekend!'
]);

// Mark as read
$notifications->markAsRead($notificationId);
$notifications->markAllAsRead();

// Delete
$notifications->delete($notificationId);

// Templates
$templates = $notifications->templates();
$notifications->createTemplate([
    'name' => 'welcome_email',
    'type' => 'email',
    'subject' => 'Welcome {{name}}!',
    'body' => 'Thank you for joining...'
]);
$notifications->updateTemplate($templateId, ['subject' => 'Welcome to {{company}}!']);
$notifications->deleteTemplate($templateId);
$preview = $notifications->previewTemplate($templateId, ['name' => 'John']);

// Email
$notifications->sendEmail($userId, 'welcome', ['name' => 'John']);

// SMS
$notifications->sendSms($userId, 'Your verification code: 123456');

// Push
$notifications->sendPush($userId, 'New message', 'You have a new message');

// Device tokens
$notifications->registerDevice($userId, $token, 'ios');
$notifications->unregisterDevice($userId, $token);
$devices = $notifications->userDevices($userId);

// Preferences
$prefs = $notifications->preferences($userId);
$notifications->updatePreferences($userId, [
    'email_marketing' => false,
    'push_enabled' => true
]);

// Statistics
$stats = $notifications->statistics();
$deliveryStats = $notifications->deliveryStats(['period' => 'week']);
```

### Affiliates Resource

```php
$affiliates = $client->affiliates;

// List affiliates
$list = $affiliates->all(['status' => 'active']);

// Get affiliate
$affiliate = $affiliates->get(123);

// Create affiliate
$new = $affiliates->create([
    'user_id' => $userId,
    'commission_rate' => 20,
    'payment_method' => 'paypal',
    'paypal_email' => 'affiliate@example.com'
]);

// Update affiliate
$affiliates->updateAffiliate(123, ['commission_rate' => 25]);

// Delete affiliate
$affiliates->delete(123);

// Approve/Reject
$affiliates->approve($affiliateId);
$affiliates->reject($affiliateId, 'Does not meet criteria');

// Suspend/Activate
$affiliates->suspend($affiliateId, 'Suspicious activity');
$affiliates->activate($affiliateId);

// Referral links
$links = $affiliates->links($affiliateId);
$affiliates->createLink($affiliateId, [
    'name' => 'Summer Campaign',
    'campaign' => 'summer2024',
    'product_id' => 123
]);
$affiliates->deleteLink($affiliateId, $linkId);

// Referrals
$referrals = $affiliates->referrals($affiliateId, ['status' => 'converted']);

// Commissions
$commissions = $affiliates->commissions($affiliateId, ['period' => 'month']);
$pending = $affiliates->pendingCommissions($affiliateId);
$affiliates->approveCommission($commissionId);
$affiliates->rejectCommission($commissionId, 'Fraudulent referral');

// Payouts
$payouts = $affiliates->payouts($affiliateId);
$affiliates->requestPayout($affiliateId, 10000); // Amount in cents
$affiliates->processPayout($payoutId);
$affiliates->cancelPayout($payoutId);

// Tiers
$tiers = $affiliates->tiers();
$affiliates->createTier([
    'name' => 'Gold',
    'commission_rate' => 30,
    'min_sales' => 50
]);
$affiliates->updateTier($tierId, ['commission_rate' => 35]);
$affiliates->deleteTier($tierId);
$affiliates->assignTier($affiliateId, $tierId);

// Statistics
$stats = $affiliates->statistics();
$affiliateStats = $affiliates->affiliateStats($affiliateId);
$topAffiliates = $affiliates->topPerformers(10);

// Settings
$settings = $affiliates->settings();
$affiliates->updateSettings(['cookie_duration' => 30]);
```

### Forums Resource

```php
$forums = $client->forums;

// Categories
$categories = $forums->categories();
$forums->createCategory(['name' => 'General Discussion', 'description' => 'Talk about anything']);
$forums->updateCategory($categoryId, ['name' => 'Community']);
$forums->deleteCategory($categoryId);
$forums->reorderCategories([1, 3, 2]);

// Topics
$topics = $forums->topics(['category_id' => 1, 'status' => 'open']);
$topic = $forums->topic($topicId);
$forums->createTopic([
    'category_id' => 1,
    'title' => 'How to get started?',
    'content' => 'I am new here and...',
    'user_id' => $userId
]);
$forums->updateTopic($topicId, ['title' => 'Getting Started Guide']);
$forums->deleteTopic($topicId);

// Topic moderation
$forums->pinTopic($topicId);
$forums->unpinTopic($topicId);
$forums->lockTopic($topicId);
$forums->unlockTopic($topicId);
$forums->moveTopic($topicId, $newCategoryId);

// Posts
$posts = $forums->posts($topicId);
$forums->createPost($topicId, [
    'content' => 'Great question! Here is how...',
    'user_id' => $userId
]);
$forums->updatePost($topicId, $postId, ['content' => 'Updated answer']);
$forums->deletePost($topicId, $postId);

// Post moderation
$forums->approvePost($topicId, $postId);
$forums->rejectPost($topicId, $postId, 'Spam content');
$forums->reportPost($topicId, $postId, 'Inappropriate language');

// Reactions
$forums->react($topicId, $postId, 'like');
$forums->unreact($topicId, $postId);
$reactions = $forums->reactions($topicId, $postId);

// Best answer
$forums->markBestAnswer($topicId, $postId);
$forums->unmarkBestAnswer($topicId);

// Subscriptions
$forums->subscribe($topicId, $userId);
$forums->unsubscribe($topicId, $userId);
$subscriptions = $forums->userSubscriptions($userId);

// Search
$results = $forums->search('keyword', ['category_id' => 1]);

// User activity
$userTopics = $forums->userTopics($userId);
$userPosts = $forums->userPosts($userId);

// Statistics
$stats = $forums->statistics();
$categoryStats = $forums->categoryStats($categoryId);

// Moderation queue
$queue = $forums->moderationQueue();
$reported = $forums->reportedPosts();

// Settings
$settings = $forums->settings();
$forums->updateSettings(['require_approval' => true]);
```

### Media Resource

```php
$media = $client->media;

// List files
$files = $media->all(['type' => 'image']);

// Get file
$file = $media->get(123);

// Upload
$uploaded = $media->upload('/path/to/file.jpg', ['folder_id' => 1]);
$media->uploadMultiple(['/path/to/a.jpg', '/path/to/b.png']);
$media->uploadFromUrl('https://example.com/image.jpg');

// Update
$media->updateFile(123, ['name' => 'renamed-file.jpg', 'alt_text' => 'Description']);

// Delete
$media->delete(123);
$media->deleteMultiple([123, 456, 789]);

// Duplicate
$media->duplicate(123);

// Download URL
$url = $media->downloadUrl(123, 3600); // Expires in 1 hour

// Folders
$folders = $media->folders();
$folder = $media->folder(123);
$media->createFolder('Images', $parentId);
$media->renameFolder(123, 'Photos');
$media->deleteFolder(123, true); // Delete contents too
$media->moveFolder(123, $newParentId);
$contents = $media->folderContents(123);

// File operations
$media->moveFile(123, $folderId);
$media->moveFiles([123, 456], $folderId);
$media->renameFile(123, 'new-name.jpg');

// Image processing
$media->resize(123, 800, 600);
$media->crop(123, 0, 0, 400, 300);
$media->generateThumbnail(123, 150, 150);
$variants = $media->variants(123);

// Video processing
$info = $media->videoInfo(123);
$media->videoThumbnail(123, 10); // At 10 seconds
$media->transcodeVideo(123, 'mp4', '720p');
$status = $media->transcodingStatus(123);

// Search
$results = $media->search('logo', ['type' => 'image']);
$byType = $media->byType('video');

// Tags
$tags = $media->tags(123);
$media->addTag(123, 'hero-image');
$media->removeTag(123, 'draft');
$tagged = $media->filesByTag('marketing');

// Statistics
$storage = $media->storageStats();
$usage = $media->usageByType();

// Settings
$settings = $media->settings();
$media->updateSettings(['max_upload_size' => 104857600]); // 100MB
```

### Reviews Resource

```php
$reviews = $client->reviews;

// List reviews
$list = $reviews->all(['status' => 'approved']);
$productReviews = $reviews->forProduct($productId, ['rating' => 5]);

// Get review
$review = $reviews->get(123);

// Create review (on behalf of user)
$reviews->create($productId, $userId, [
    'rating' => 5,
    'title' => 'Excellent course!',
    'content' => 'This course helped me...'
]);

// Update review
$reviews->updateReview(123, ['rating' => 4]);

// Delete review
$reviews->delete(123);

// Moderation
$pending = $reviews->pending();
$reviews->approve(123);
$reviews->reject(123, 'Inappropriate content');
$reviews->flag(123, 'Suspected fake review');
$flagged = $reviews->flagged();

// Responses
$reviews->respond(123, 'Thank you for your feedback!');
$reviews->updateResponse(123, 'Updated response');
$reviews->deleteResponse(123);

// Helpfulness
$reviews->markHelpful(123, $userId);
$reviews->markNotHelpful(123, $userId);

// Ratings
$rating = $reviews->productRating($productId);
$distribution = $reviews->ratingDistribution($productId);

// User reviews
$userReviews = $reviews->userReviews($userId);
$canReview = $reviews->canReview($productId, $userId);

// Media
$reviews->uploadImage(123, '/path/to/image.jpg');
$reviews->deleteImage(123, $imageId);
$reviews->uploadVideo(123, '/path/to/video.mp4');

// Statistics
$stats = $reviews->statistics();
$productStats = $reviews->productStats($productId);

// Widgets
$recent = $reviews->recentWidget(5);
$topRated = $reviews->topRated(10);

// Import/Export
$reviews->export('csv', ['product_id' => 123]);
$reviews->import('/path/to/reviews.csv');

// Settings
$settings = $reviews->settings();
$reviews->updateSettings(['require_approval' => true, 'min_rating' => 1]);
```

### Coupons Resource

```php
$coupons = $client->coupons;

// List coupons
$list = $coupons->all(['status' => 'active']);
$active = $coupons->active();
$expired = $coupons->expired();

// Get coupon
$coupon = $coupons->get(123);
$coupon = $coupons->getByCode('SUMMER2024');

// Create coupon
$new = $coupons->create([
    'code' => 'SUMMER2024',
    'type' => 'percentage',
    'value' => 20,
    'max_uses' => 100,
    'expires_at' => '2024-08-31',
    'min_purchase' => 5000
]);

// Update coupon
$coupons->updateCoupon(123, ['value' => 25]);

// Delete coupon
$coupons->delete(123);

// Duplicate
$coupons->duplicate(123);

// Activation
$coupons->activate(123);
$coupons->deactivate(123);

// Validation
$valid = $coupons->validate('SUMMER2024', ['product_id' => 456, 'user_id' => 789]);

// Apply/Remove
$coupons->apply('SUMMER2024', $orderId);
$coupons->remove($orderId);

// Calculate discount
$discount = $coupons->calculateDiscount('SUMMER2024', [
    ['product_id' => 1, 'price' => 2900, 'quantity' => 2],
    ['product_id' => 2, 'price' => 4900, 'quantity' => 1]
]);

// Usage
$usage = $coupons->usageHistory(123);
$userUsage = $coupons->userUsage($userId);
$coupons->recordUsage(123, $userId, $orderId, 1500);

// Restrictions
$restrictions = $coupons->restrictions(123);
$coupons->updateRestrictions(123, [
    'min_items' => 2,
    'first_purchase_only' => true
]);
$coupons->addProductRestriction(123, $productId, 'include');
$coupons->removeProductRestriction(123, $productId);
$coupons->addUserRestriction(123, $userId);
$coupons->removeUserRestriction(123, $userId);

// Bulk operations
$coupons->generateBulk([
    'type' => 'percentage',
    'value' => 10,
    'max_uses' => 1
], 100, 'PROMO');
$coupons->activateBulk([1, 2, 3]);
$coupons->deactivateBulk([4, 5, 6]);
$coupons->deleteBulk([7, 8, 9]);

// User coupons
$available = $coupons->availableForUser($userId);
$coupons->assignToUser(123, $userId);
$coupons->assignToUsers(123, [1, 2, 3]);

// Statistics
$stats = $coupons->statistics();
$performance = $coupons->performance(123);
$impact = $coupons->revenueImpact(123);
$top = $coupons->topPerforming(10);

// Import/Export
$coupons->export('csv');
$coupons->import('/path/to/coupons.csv');
```

---

## Error Handling

### Exception Hierarchy

```
SenseiPartnerException (base)
├── AuthenticationException (401, 403)
├── ValidationException (422)
├── NotFoundException (404)
├── RateLimitException (429)
└── ServerException (500, 502, 503, 504)
```

### Handling Errors

```php
use Sensei\PartnerSDK\Exceptions\{
    SenseiPartnerException,
    AuthenticationException,
    ValidationException,
    NotFoundException,
    RateLimitException,
    ServerException
};

try {
    $product = $client->products->create($data);
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
    // ['title' => ['The title field is required.']]

    $allMessages = $e->getAllMessages();
    // ['The title field is required.', ...]

    $firstMessage = $e->getFirstMessage();
    // 'The title field is required.'

} catch (AuthenticationException $e) {
    // Invalid API key or token
    // Redirect to login or refresh token

} catch (NotFoundException $e) {
    // Resource not found (404)

} catch (RateLimitException $e) {
    // Rate limited
    $retryAfter = $e->getRetryAfter(); // seconds
    sleep($retryAfter);
    // Retry request

} catch (ServerException $e) {
    // Server error (5xx)
    // Log and show user-friendly message

} catch (SenseiPartnerException $e) {
    // Catch-all for other API errors
    $message = $e->getMessage();
    $code = $e->getCode();
    $errors = $e->getErrors();
}
```

### Automatic Retry on Rate Limit

The SDK automatically retries on 429 errors if `retry_on_rate_limit` is enabled:

```php
$config = new Configuration(
    apiKey: 'sk_live_xxx',
    retryOnRateLimit: true, // default: true
    maxRetries: 3
);
```

---

## Pagination

### Using PaginatedResponse

```php
// Get paginated results
$products = $client->products->all(['per_page' => 20]);

// Access items
$items = $products->items();
$first = $products->first();
$last = $products->last();

// Check state
$products->isEmpty();
$products->isNotEmpty();
$products->count(); // Items on current page
$products->total(); // Total items across all pages

// Pagination info
$products->currentPage();
$products->totalPages();
$products->perPage();
$products->hasMorePages();
$products->onFirstPage();
$products->onLastPage();

// Navigate
$nextPage = $products->nextPage();
$prevPage = $products->previousPage();
$page5 = $products->getPage(5);

// Iterate current page
foreach ($products as $product) {
    echo $product['title'];
}

// Iterate ALL pages automatically
foreach ($products->all() as $product) {
    // Automatically fetches next pages
    processProduct($product);
}

// Transform items
$titles = $products->map(fn($p) => $p['title']);
$active = $products->filter(fn($p) => $p['status'] === 'active');

// Get raw data
$array = $products->toArray();
$meta = $products->meta();
$links = $products->links();
```

### Pagination Parameters

```php
$products = $client->products->all([
    'page' => 1,
    'per_page' => 25, // 1-100
    'sort' => 'created_at',
    'order' => 'desc',
    'filter' => [
        'status' => 'published',
        'category_id' => 5
    ]
]);
```

---

## Webhooks

### Event Types

| Event | Description |
|-------|-------------|
| `subscription.created` | New subscription created |
| `subscription.updated` | Subscription modified |
| `subscription.cancelled` | Subscription cancelled |
| `subscription.renewed` | Subscription renewed |
| `subscription.paused` | Subscription paused |
| `subscription.resumed` | Subscription resumed |
| `subscription.expired` | Subscription expired |
| `payment.succeeded` | Payment completed |
| `payment.failed` | Payment failed |
| `payment.refunded` | Payment refunded |
| `invoice.created` | Invoice generated |
| `invoice.paid` | Invoice paid |
| `user.created` | New user registered |
| `user.updated` | User profile updated |
| `user.deleted` | User deleted |
| `product.created` | New product created |
| `product.updated` | Product modified |
| `product.deleted` | Product deleted |
| `review.created` | New review posted |
| `payout.created` | Payout initiated |
| `payout.paid` | Payout completed |
| `dispute.created` | Chargeback initiated |
| `dispute.resolved` | Dispute resolved |

### Webhook Payload Structure

```json
{
    "id": "evt_123456",
    "type": "subscription.created",
    "created_at": "2024-01-15T10:30:00Z",
    "data": {
        "id": 456,
        "user_id": 123,
        "product_id": 789,
        "status": "active",
        "current_period_start": "2024-01-15",
        "current_period_end": "2024-02-15"
    },
    "previous_attributes": null
}
```

### Laravel Webhook Handler

```php
// routes/web.php
Route::post('/webhooks/sensei', [WebhookController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// app/Http/Controllers/WebhookController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sensei\PartnerSDK\Resources\Webhooks;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Sensei-Signature');
        $secret = config('sensei-partner.webhook_secret');

        if (!Webhooks::verifySignature($payload, $signature, $secret)) {
            return response('Invalid signature', 401);
        }

        $event = Webhooks::parsePayload($payload);

        match ($event['type']) {
            'subscription.created' => $this->handleSubscriptionCreated($event['data']),
            'subscription.cancelled' => $this->handleSubscriptionCancelled($event['data']),
            'payment.succeeded' => $this->handlePaymentSucceeded($event['data']),
            'payment.failed' => $this->handlePaymentFailed($event['data']),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleSubscriptionCreated(array $data): void
    {
        // Grant access to user
    }

    private function handleSubscriptionCancelled(array $data): void
    {
        // Send cancellation email
    }

    private function handlePaymentSucceeded(array $data): void
    {
        // Update records, send receipt
    }

    private function handlePaymentFailed(array $data): void
    {
        // Notify user, retry logic
    }
}
```

---

## Testing

### Using Test Mode

```php
// Use test API keys
$config = new Configuration(
    apiKey: 'sk_test_your_test_key',
    baseUrl: 'https://api.senseitemple.com' // Same URL, test mode
);

$client = new PartnerClient($config);

// Verify test mode
if ($config->isTestMode()) {
    // Safe to create test data
}
```

### Mocking the Client

```php
use Mockery;
use Sensei\PartnerSDK\PartnerClient;
use Sensei\PartnerSDK\Resources\Products;

// Mock the client
$client = Mockery::mock(PartnerClient::class);
$products = Mockery::mock(Products::class);

$client->shouldReceive('__get')
    ->with('products')
    ->andReturn($products);

$products->shouldReceive('all')
    ->andReturn([
        'data' => [
            ['id' => 1, 'title' => 'Test Product']
        ],
        'meta' => ['total' => 1]
    ]);
```

### Test Fixtures

```php
// tests/Fixtures/products.json
{
    "data": [
        {
            "id": 1,
            "title": "Test Course",
            "status": "published",
            "price": 9900
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 1
    }
}
```

---

## Best Practices

### 1. Store API Keys Securely

```php
// DO: Use environment variables
$config = new Configuration(
    apiKey: $_ENV['SENSEI_API_KEY']
);

// DON'T: Hardcode keys
$config = new Configuration(
    apiKey: 'sk_live_xxx' // Never do this!
);
```

### 2. Handle Errors Gracefully

```php
try {
    $result = $client->subscriptions->create($data);
} catch (ValidationException $e) {
    // Return user-friendly errors
    return response()->json([
        'errors' => $e->getErrors()
    ], 422);
} catch (SenseiPartnerException $e) {
    // Log for debugging
    Log::error('Sensei API error', [
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);

    // Show generic message to user
    return response()->json([
        'message' => 'Unable to process request'
    ], 500);
}
```

### 3. Use Pagination for Large Datasets

```php
// DON'T: Fetch all at once
$allProducts = $client->products->all(['per_page' => 1000]);

// DO: Paginate or stream
$products = $client->products->all(['per_page' => 50]);
foreach ($products->all() as $product) {
    // Process one at a time
}
```

### 4. Cache Frequently Accessed Data

```php
// Cache dashboard stats
$stats = Cache::remember('partner.dashboard', 300, function () use ($client) {
    return $client->dashboard->overview();
});

// Cache product list
$products = Cache::remember('partner.products', 60, function () use ($client) {
    return $client->products->all()->items();
});
```

### 5. Use Webhooks for Real-Time Updates

```php
// DON'T: Poll for changes
while (true) {
    $subscriptions = $client->subscriptions->all();
    // Check for changes...
    sleep(60);
}

// DO: Use webhooks
// Configure webhook endpoint and handle events
```

### 6. Implement Idempotency

```php
// Use idempotency keys for critical operations
$subscription = $client->subscriptions->create([
    'user_id' => 123,
    'product_id' => 456,
    'idempotency_key' => 'unique-request-id-' . $userId . '-' . $productId
]);
```

---

## Troubleshooting

### Common Issues

#### "API key is invalid"

- Verify the key is correct and not expired
- Check you're using the right key type (live vs test)
- Ensure the key has required permissions

#### "Rate limit exceeded"

- Implement exponential backoff
- Cache responses where appropriate
- Reduce request frequency
- Contact support for limit increase

#### "SSL certificate problem"

```php
// Development only - never in production!
$config = new Configuration(
    apiKey: 'sk_test_xxx',
    verifySSL: false
);
```

#### "Connection timeout"

```php
// Increase timeout
$config = new Configuration(
    apiKey: 'sk_live_xxx',
    timeout: 60,
    connectTimeout: 30
);
```

#### "Webhook signature invalid"

- Verify you're using the correct webhook secret
- Ensure the raw payload is used (not parsed JSON)
- Check the signature header name (`X-Sensei-Signature`)

### Debug Mode

```php
// Enable Guzzle debugging
$config = new Configuration(
    apiKey: 'sk_test_xxx',
    httpOptions: [
        'debug' => true
    ]
);
```

### Getting Help

- Documentation: https://docs.senseitemple.com
- API Status: https://status.senseitemple.com
- Support: support@senseitemple.com
- GitHub Issues: https://github.com/sensei/partner-sdk-php/issues

---

## Changelog

### v1.1.0 (2024-12-04)

- **Added 11 new resources** (total 23 resources):
  - Guilds: Community/guild management with members, channels, roles
  - Messages: Direct messages and group conversations
  - Gamification: XP, levels, badges, achievements, streaks, challenges
  - Events: Live events, webinars, registration, attendance
  - Certificates: Certificate templates, issuance, verification
  - Notifications: Push, email, SMS notifications with templates
  - Affiliates: Affiliate program, referrals, commissions, payouts
  - Forums: Discussion forums with categories, topics, posts, moderation
  - Media: Media library with folders, image/video processing, tags
  - Reviews: Product reviews, ratings, moderation, responses
  - Coupons: Discount coupons, validation, restrictions, bulk operations
- Full documentation with code examples for all resources
- Enhanced IDE autocompletion with PHPDoc annotations

### v1.0.0 (2024-01-15)

- Initial release
- Full API coverage for Partner endpoints (12 resources)
- Laravel integration with auto-discovery
- Comprehensive error handling
- Pagination support
- Webhook signature verification
