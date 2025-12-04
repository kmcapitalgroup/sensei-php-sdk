# Sensei Partner SDK for PHP

Official PHP SDK for the Sensei Partner API. Build integrations with Sensei's complete gamification and user engagement platform.


## Requirements

- PHP 8.1 or higher
- Guzzle HTTP client 7.0+
- Laravel 10+ (optional, for framework integration)

## Installation

```bash
composer require sensei/partner-sdk
```

## Quick Start

### Basic Usage (Without Laravel)

```php
use Sensei\PartnerSDK\Configuration;
use Sensei\PartnerSDK\PartnerClient;

// Create configuration
$config = new Configuration(
    apiKey: 'sk_live_your_api_key_here'
);

// Create client
$client = new PartnerClient($config);

// Get dashboard overview
$dashboard = $client->dashboard->overview();

// List products
$products = $client->products->all();

// Create a subscription
$subscription = $client->subscriptions->create([
    'user_id' => 123,
    'product_id' => 456,
    'pricing_tier_id' => 789,
]);
```

### Laravel Integration

The SDK includes full Laravel support with auto-discovery.

#### Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=sensei-partner-config
```

Set your credentials in `.env`:

```env
SENSEI_PARTNER_API_KEY=sk_live_your_api_key_here
SENSEI_PARTNER_BASE_URL=https://api.senseitemple.com
```

#### Using the Facade

```php
use Sensei\PartnerSDK\Laravel\Facades\SenseiPartner;

// Get dashboard stats
$stats = SenseiPartner::dashboard()->overview();

// List all products
$products = SenseiPartner::products()->all();

// Get analytics
$revenue = SenseiPartner::analytics()->revenue([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
]);
```

#### Using Dependency Injection

```php
use Sensei\PartnerSDK\PartnerClient;

class DashboardController extends Controller
{
    public function __construct(private PartnerClient $senseiPartner)
    {
    }

    public function index()
    {
        return $this->senseiPartner->dashboard->overview();
    }
}
```

## Available Resources

### Products

Manage formations, services, and digital products.

```php
// List products with pagination
$products = $client->products->all(['per_page' => 20]);

// Get a specific product
$product = $client->products->get(123);

// Create a formation (course)
$formation = $client->products->createFormation([
    'title' => 'My Course',
    'description' => 'Course description',
    'price' => 9900, // cents
]);

// Add modules and lessons
$module = $client->products->createModule($formationId, [
    'title' => 'Module 1',
    'description' => 'First module',
]);

$lesson = $client->products->createLesson($formationId, $moduleId, [
    'title' => 'Lesson 1',
    'content' => 'Lesson content...',
]);

// Manage pricing tiers
$tiers = $client->products->pricingTiers($productId);
$tier = $client->products->createPricingTier($productId, [
    'name' => 'Premium',
    'price' => 4900,
    'interval' => 'month',
]);
```

### Subscriptions

Manage customer subscriptions.

```php
// List subscriptions
$subscriptions = $client->subscriptions->all();

// Create subscription
$subscription = $client->subscriptions->create([
    'user_id' => 123,
    'product_id' => 456,
    'pricing_tier_id' => 789,
]);

// Cancel subscription
$client->subscriptions->cancel($subscriptionId);

// Pause/Resume
$client->subscriptions->pause($subscriptionId, '2024-02-01');
$client->subscriptions->resume($subscriptionId);

// Change plan
$client->subscriptions->changePlan($subscriptionId, $newPricingTierId);

// Apply coupon
$client->subscriptions->applyCoupon($subscriptionId, 'DISCOUNT20');

// Check access
$access = $client->subscriptions->checkAccess($userId, $productId);
```

### Users/Customers

Manage your customers and students.

```php
// List users
$users = $client->users->all();

// Search users
$results = $client->users->search('john@example.com');

// Get user details
$user = $client->users->get($userId);

// Get user's subscriptions
$subscriptions = $client->users->subscriptions($userId);

// Get user's progress
$progress = $client->users->progress($userId, $productId);

// Manage user tags
$client->users->addTag($userId, 'vip');
$client->users->removeTag($userId, 'trial');

// Export users
$export = $client->users->export('csv', ['segment_id' => 123]);
```

### Dashboard & Analytics

Access statistics and generate reports.

```php
// Dashboard overview
$overview = $client->dashboard->overview();

// Revenue stats
$revenue = $client->dashboard->revenue();
$mrr = $client->dashboard->mrr();
$arr = $client->dashboard->arr();

// Subscriber metrics
$subscribers = $client->dashboard->subscribers();
$growth = $client->dashboard->subscriberGrowth();

// Analytics
$analytics = $client->analytics->overview([
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
]);

// Cohort analysis
$cohorts = $client->analytics->cohorts();

// Retention
$retention = $client->analytics->retention();

// Create custom report
$report = $client->analytics->createReport([
    'name' => 'Monthly Revenue Report',
    'metrics' => ['revenue', 'subscribers', 'churn'],
    'granularity' => 'month',
]);
```

### Payments

Manage payments, refunds, and invoices.

```php
// List payments
$payments = $client->payments->all();

// Get payment
$payment = $client->payments->get($paymentId);

// Create refund
$refund = $client->payments->refund($paymentId);

// Partial refund
$refund = $client->payments->partialRefund($paymentId, 1000, 'Customer request');

// Invoices
$invoices = $client->payments->invoices();
$invoice = $client->payments->invoice($invoiceId);
$client->payments->sendInvoice($invoiceId);

// Coupons
$coupons = $client->payments->coupons();
$coupon = $client->payments->createCoupon([
    'code' => 'SUMMER20',
    'discount_percent' => 20,
    'expires_at' => '2024-08-31',
]);

// Balance
$balance = $client->payments->balance();
```

### Stripe Connect

Manage your Stripe Connect integration.

```php
// Get account status
$status = $client->stripeConnect->status();

// Get onboarding URL
$onboarding = $client->stripeConnect->onboardingUrl(
    returnUrl: 'https://myapp.com/stripe/return',
    refreshUrl: 'https://myapp.com/stripe/refresh'
);

// Check if fully onboarded
if ($client->stripeConnect->isFullyOnboarded()) {
    // Ready to accept payments
}

// Get balance
$balance = $client->stripeConnect->balance();

// Bank accounts
$accounts = $client->stripeConnect->bankAccounts();
$client->stripeConnect->addBankAccount([
    'account_number' => '...',
    'routing_number' => '...',
]);
```

### SSO / OAuth 2.0

Enable Single Sign-On for seamless authentication between your platform and Sensei Temple.
Implements OAuth 2.0 with mandatory PKCE (RFC 7636) for enhanced security.

```php
// =====================================
// SSO Settings Management
// =====================================

// Get current SSO settings and stats
$response = $client->sso->getSettings();
// $response['settings'] - SSO configuration
// $response['stats'] - Usage statistics

// Enable SSO for your tenant
// If no credentials exist, they will be auto-generated
// WARNING: client_secret is only shown ONCE when first enabled!
$result = $client->sso->enable();
if (isset($result['settings']['client_secret'])) {
    // Save this immediately - it won't be shown again!
    $clientSecret = $result['settings']['client_secret'];
}

// Disable SSO
$client->sso->disable();

// Toggle SSO
$client->sso->toggle(true);  // or false

// Rotate client secret only (keeps client_id)
// WARNING: client_secret is only shown ONCE!
$result = $client->sso->regenerateSecret();
$newSecret = $result['settings']['client_secret']; // Save immediately!

// Manage redirect URIs
$client->sso->addRedirectUri('https://newapp.com/callback');
$client->sso->removeRedirectUri('https://oldapp.com/callback');
$client->sso->setRedirectUris([
    'https://app1.com/callback',
    'https://app2.com/callback',
]);

// Get SSO statistics
$stats = $client->sso->getStats();
// $stats['stats']['total_connections']
// $stats['stats']['active_users']
// $stats['stats']['last_30_days']

// =====================================
// OAuth 2.0 Flow Implementation
// =====================================

// Step 1: Generate PKCE parameters
$pkce = \Sensei\PartnerSDK\Resources\Sso::generatePkce();
// Store $pkce['code_verifier'] in session for later use!
session(['pkce_verifier' => $pkce['code_verifier']]);

// Step 2: Build authorization URL and redirect user
$authUrl = $client->sso->buildAuthorizationUrl(
    clientId: 'sensei_your_client_id',
    redirectUri: 'https://myapp.com/auth/callback',
    codeChallenge: $pkce['code_challenge'],
    scopes: ['openid', 'profile', 'email'],
    state: bin2hex(random_bytes(16))  // CSRF protection
);
// Redirect user to $authUrl

// Step 3: Handle callback - Exchange code for tokens
$tokens = $client->sso->exchangeCode(
    code: $request->get('code'),
    clientId: 'sensei_your_client_id',
    clientSecret: 'your_client_secret',
    redirectUri: 'https://myapp.com/auth/callback',
    codeVerifier: session('pkce_verifier')
);
// $tokens['access_token']
// $tokens['refresh_token'] (if enabled)
// $tokens['expires_in']

// Step 4: Get user info
$userInfo = $client->sso->getUserInfo($tokens['access_token']);
// $userInfo['sub'] - User ID
// $userInfo['name'] - (if profile scope)
// $userInfo['email'] - (if email scope)

// Refresh token when expired
$newTokens = $client->sso->refreshToken(
    refreshToken: $tokens['refresh_token'],
    clientId: 'sensei_your_client_id',
    clientSecret: 'your_client_secret'
);

// Revoke token (logout)
$client->sso->revokeToken($tokens['access_token'], 'access_token');
$client->sso->revokeToken($tokens['refresh_token'], 'refresh_token');

// Get OpenID Connect discovery URL
$discoveryUrl = $client->sso->getDiscoveryUrl();
// Returns: https://api.senseitemple.com/.well-known/openid-configuration
```

#### SSO Security Best Practices

1. **Always use PKCE** - The SDK enforces PKCE with S256 method (mandatory)
2. **Use state parameter** - Protect against CSRF attacks
3. **Store tokens securely** - Never expose tokens in URLs or logs
4. **Short-lived access tokens** - Default 15 min, max 1 hour
5. **Use HTTPS redirect URIs** - Required in production
6. **Rotate secrets regularly** - Use `regenerateSecret()` periodically

### API Keys

Manage your API keys for integrations.

```php
// List API keys
$keys = $client->apiKeys->all();

// Create new key
$key = $client->apiKeys->create([
    'name' => 'Production Key',
    'permissions' => ['products:read', 'subscriptions:write'],
]);

// Regenerate secret
$newKey = $client->apiKeys->regenerate($keyId);

// Get usage stats
$usage = $client->apiKeys->usage($keyId);

// Disable key
$client->apiKeys->disable($keyId);
```

### Webhooks

Configure webhook endpoints.

```php
// List webhooks
$webhooks = $client->webhooks->all();

// Create webhook
$webhook = $client->webhooks->create([
    'url' => 'https://myapp.com/webhooks/sensei',
    'events' => ['subscription.created', 'subscription.cancelled', 'payment.completed'],
]);

// Test webhook
$client->webhooks->test($webhookId);

// Get available events
$events = $client->webhooks->eventTypes();

// Verify webhook signature
$isValid = \Sensei\PartnerSDK\Resources\Webhooks::verifySignature(
    payload: $request->getContent(),
    signature: $request->header('X-Sensei-Signature'),
    secret: $webhookSecret
);
```

### Compliance (GDPR, Tax)

Handle compliance requirements.

```php
// GDPR
$gdprStatus = $client->compliance->gdprStatus();

// Data export request
$export = $client->compliance->requestDataExport($userId);

// Data deletion request
$deletion = $client->compliance->requestDeletion($userId, 'User requested');

// Consent management
$client->compliance->recordConsent($userId, 'marketing', true);

// DPA (Data Processing Agreement)
$dpa = $client->compliance->getCurrentDpa();
$client->compliance->signDpa($dpaId, [
    'signer_name' => 'John Doe',
    'signer_email' => 'john@company.com',
    'company_name' => 'My Company',
]);

// Tax settings
$taxSettings = $client->compliance->taxSettings();
$client->compliance->updateTaxSettings([
    'vat_number' => 'FR12345678901',
    'country' => 'FR',
]);

// Validate VAT number
$validation = $client->compliance->validateVatNumber('FR12345678901', 'FR');
```

### Partner Profile & Settings

Manage your partner profile.

```php
// Get profile
$profile = $client->profile->get();

// Update profile
$client->profile->updateProfile([
    'business_name' => 'My Business',
    'description' => 'We provide...',
]);

// Upload logo
$client->profile->uploadLogo('/path/to/logo.png');

// Settings
$settings = $client->settings->all();
$client->settings->set('email_notifications', true);

// Security
$client->settings->enable2fa();
$sessions = $client->settings->sessions();
$client->settings->revokeAllSessions();
```

## Pagination

The SDK uses cursor-based pagination with a `PaginatedResponse` helper:

```php
// Get first page
$products = $client->products->all(['per_page' => 20]);

// Access items
foreach ($products->items() as $product) {
    echo $product['title'];
}

// Check pagination info
echo "Page {$products->currentPage()} of {$products->totalPages()}";
echo "Total: {$products->total()} items";

// Navigate pages
if ($products->hasMorePages()) {
    $nextPage = $products->nextPage();
}

// Iterate through all pages automatically
foreach ($products->all() as $product) {
    // Automatically fetches next pages as needed
    processProduct($product);
}
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use Sensei\PartnerSDK\Exceptions\AuthenticationException;
use Sensei\PartnerSDK\Exceptions\ValidationException;
use Sensei\PartnerSDK\Exceptions\NotFoundException;
use Sensei\PartnerSDK\Exceptions\RateLimitException;
use Sensei\PartnerSDK\Exceptions\ServerException;

try {
    $product = $client->products->get(999999);
} catch (NotFoundException $e) {
    // Product not found (404)
    echo "Product not found: " . $e->getMessage();
} catch (ValidationException $e) {
    // Validation error (422)
    foreach ($e->getErrors() as $field => $messages) {
        echo "{$field}: " . implode(', ', $messages);
    }
} catch (AuthenticationException $e) {
    // Auth error (401/403)
    echo "Authentication failed: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Rate limited (429)
    echo "Rate limited. Retry after: " . $e->getRetryAfter() . " seconds";
} catch (ServerException $e) {
    // Server error (5xx)
    echo "Server error: " . $e->getMessage();
}
```

## Configuration Options

```php
$config = new Configuration(
    apiKey: 'sk_live_xxx',           // Your API key
    bearerToken: null,                // Alternative: Bearer token
    baseUrl: 'https://api.senseitemple.com',
    timeout: 30,                      // Request timeout (seconds)
    connectTimeout: 10,               // Connection timeout (seconds)
    maxRetries: 3,                    // Max retry attempts
    verifySSL: true,                  // SSL verification
    retryOnRateLimit: true,           // Auto-retry on 429
);
```

Or from array:

```php
$client = PartnerClient::create([
    'api_key' => 'sk_live_xxx',
    'base_url' => 'https://api.senseitemple.com',
    'timeout' => 30,
]);
```

## Testing

For testing, use test mode API keys (`sk_test_xxx`):

```php
$config = new Configuration(
    apiKey: 'sk_test_your_test_key'
);

// Check mode
if ($config->isTestMode()) {
    echo "Running in test mode";
}
```

## Support

- Documentation: https://docs.senseitemple.com
- API Reference: https://api.senseitemple.com/docs
- Support: support@senseitemple.com

## License

MIT License - see LICENSE file for details.
