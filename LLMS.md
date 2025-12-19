# Sensei Partner SDK - AI Agent Reference

> Machine-readable documentation for AI agents integrating with the Sensei Partner API.
> This document uses structured formats optimized for LLM comprehension.

## SDK Metadata

```yaml
name: sensei/partner-sdk
language: PHP
version: 1.0.0
min_php: 8.1
dependencies:
  - guzzlehttp/guzzle: ^7.0
installation: composer require sensei/partner-sdk
```

## Quick Decision Tree

```
TASK: What do you want to do?
├── Create/manage users in my tenant
│   ├── Create new user → users.signupAndLink()
│   ├── Login existing user → users.loginAndLink()
│   ├── List users → users.all()
│   └── Get user details → users.get(userId)
│
├── Allow users to sell products/services
│   ├── Start seller onboarding → userStripeConnect.onboard()
│   ├── Check seller status → userStripeConnect.status()
│   ├── Get seller dashboard → userStripeConnect.dashboard()
│   └── Check if can receive payments → userStripeConnect.canReceivePayments()
│
├── Manage subscriptions
│   ├── Create subscription → subscriptions.create()
│   ├── Cancel subscription → subscriptions.cancel()
│   └── Check access → subscriptions.checkAccess()
│
├── Handle payments
│   ├── List payments → payments.all()
│   ├── Create refund → payments.refund()
│   └── Manage coupons → payments.createCoupon()
│
└── Analytics & Dashboard
    ├── Get overview → dashboard.overview()
    ├── Revenue metrics → dashboard.revenue()
    └── Custom reports → analytics.createReport()
```

---

## Authentication Patterns

### Pattern 1: Partner API Key (Server-to-Server)

Use for: Managing tenant resources, creating users, admin operations.

```php
use Sensei\PartnerSDK\PartnerClient;

$client = PartnerClient::create([
    'api_key' => 'sk_live_xxxxxxxxxxxxxxxx',  // Partner API key
    'base_url' => 'https://api.senseitemple.com/api',
]);
```

### Pattern 2: User Bearer Token (User Context)

Use for: User-specific operations like Stripe Connect onboarding.

```php
// Step 1: Create user and get token
$result = $partnerClient->users->signupAndLink([...]);
$userToken = $result['token'];

// Step 2: Create user-context client
$userClient = PartnerClient::create([
    'bearer_token' => $userToken,
    'base_url' => 'https://api.senseitemple.com/api',
    'tenant' => 'your-tenant-slug',
]);

// Step 3: Use user-context client
$status = $userClient->userStripeConnect->status();
```

---

## Core Resources Reference

### users.signupAndLink()

**Purpose**: Create a new user and automatically link them to your tenant.

```php
// Method signature
$result = $client->users->signupAndLink(array $data): array

// Parameters
$data = [
    'name' => string,           // REQUIRED: User's full name
    'email' => string,          // REQUIRED: Unique email address
    'password' => string,       // REQUIRED: Min 8 characters
    'faction_id' => ?int,       // OPTIONAL: Guild ID to auto-join
];

// Response structure
[
    'token' => 'eyJ...',        // JWT for subsequent user requests
    'user' => [
        'id' => 123,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'avatar' => 'https://...',
    ],
    'guild' => [                // Present if faction_id provided
        'id' => 1,
        'name' => 'Community Name',
        'slug' => 'community-slug',
    ],
    'tenant' => [
        'id' => 'tenant-slug',
        'name' => 'Tenant Name',
    ],
]
```

**Common Errors**:
| Code | Error | Solution |
|------|-------|----------|
| 422 | "email has already been taken" | Email exists. Use `loginAndLink()` instead. |
| 422 | "password must be at least 8 characters" | Increase password length. |
| 404 | "Tenant not found" | Check base URL includes correct tenant. |

---

### users.loginAndLink()

**Purpose**: Authenticate existing user and link to your tenant.

```php
// Method signature
$result = $client->users->loginAndLink(array $data): array

// Parameters
$data = [
    'email' => string,          // REQUIRED: User's email
    'password' => string,       // REQUIRED: User's password
];

// Response: Same structure as signupAndLink()
```

---

### userStripeConnect.onboard()

**Purpose**: Start Stripe Connect onboarding for a user to become a seller.

**Prerequisites**: Must use a user token (from signupAndLink/loginAndLink).

```php
// Method signature
$result = $userClient->userStripeConnect->onboard(array $data = []): array

// Parameters
$data = [
    'country' => string,         // OPTIONAL: 2-letter ISO code (FR, US, GB)
    'business_type' => string,   // OPTIONAL: 'individual' or 'company'
    'company_name' => string,    // REQUIRED if business_type='company'
];

// Response structure
[
    'data' => [
        'message' => 'Stripe Connect account created',
        'account_id' => 'acct_1234567890',
        'onboarding_url' => 'https://connect.stripe.com/setup/s/...',
    ]
]

// Next step: Redirect user to onboarding_url
```

---

### userStripeConnect.status()

**Purpose**: Check if user has completed Stripe Connect onboarding.

```php
// Method signature
$result = $userClient->userStripeConnect->status(): array

// Response structure
[
    'data' => [
        'connected' => true,           // Has Stripe account linked
        'onboarded' => true,           // Completed verification
        'account_id' => 'acct_xxx',
        'status' => [...],             // Capability details
        'requirements' => [...],       // Pending verification items
    ]
]

// Decision logic
if ($result['data']['onboarded']) {
    // User can receive payments - show seller features
} elseif ($result['data']['connected']) {
    // User started but didn't finish - show "Continue Setup" button
} else {
    // User hasn't started - show "Become a Seller" button
}
```

---

### userStripeConnect.canReceivePayments()

**Purpose**: Simple boolean check for seller readiness.

```php
// Method signature
$canReceive = $userClient->userStripeConnect->canReceivePayments(): bool

// Usage
if ($userClient->userStripeConnect->canReceivePayments()) {
    // Show: "Create Paid Service", "Set Prices", etc.
} else {
    // Show: "Connect Stripe to Start Earning"
}
```

---

### subscriptions.create()

**Purpose**: Create a new subscription for a user.

```php
// Method signature
$subscription = $client->subscriptions->create(array $data): array

// Parameters
$data = [
    'user_id' => int,           // REQUIRED: Target user ID
    'product_id' => int,        // REQUIRED: Product to subscribe to
    'pricing_tier_id' => int,   // REQUIRED: Specific pricing tier
];

// Response structure
[
    'id' => 123,
    'status' => 'active',
    'user_id' => 456,
    'product_id' => 789,
    'current_period_start' => '2025-01-01T00:00:00Z',
    'current_period_end' => '2025-02-01T00:00:00Z',
]
```

---

## Error Handling Reference

```php
use Sensei\PartnerSDK\Exceptions\{
    AuthenticationException,  // 401, 403
    ValidationException,      // 422
    NotFoundException,        // 404
    RateLimitException,       // 429
    ServerException,          // 500, 502, 503, 504
    SenseiPartnerException,   // Base class for all
};

try {
    $result = $client->users->signupAndLink([...]);
} catch (ValidationException $e) {
    // Get field-level errors
    $errors = $e->getErrors();
    // Example: ['email' => ['The email has already been taken.']]

} catch (RateLimitException $e) {
    // Get retry delay in seconds
    $retryAfter = $e->getRetryAfter();
    sleep($retryAfter);
    // Retry the request

} catch (AuthenticationException $e) {
    // Invalid or expired API key/token
    // Solution: Check credentials, regenerate if needed

} catch (NotFoundException $e) {
    // Resource doesn't exist
    // Solution: Verify IDs, check tenant configuration
}
```

---

## Common Integration Flows

### Flow 1: User Registration with Seller Capability

```php
// 1. Create user in your tenant
$partnerClient = PartnerClient::create(['api_key' => 'sk_live_xxx']);

$signup = $partnerClient->users->signupAndLink([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'password' => 'SecurePass123!',
]);

// 2. Switch to user context for seller onboarding
$userClient = PartnerClient::create([
    'bearer_token' => $signup['token'],
    'base_url' => 'https://api.senseitemple.com/api',
    'tenant' => 'your-tenant',
]);

// 3. Start seller onboarding
$onboarding = $userClient->userStripeConnect->onboard([
    'country' => 'FR',
    'business_type' => 'individual',
]);

// 4. Return onboarding URL to frontend
return ['redirect' => $onboarding['data']['onboarding_url']];
```

### Flow 2: Check Seller Status After Stripe Redirect

```php
// User returns from Stripe onboarding
$status = $userClient->userStripeConnect->status();

if ($status['data']['onboarded']) {
    // SUCCESS: User is fully verified
    $balance = $userClient->userStripeConnect->balance();
    return [
        'status' => 'verified',
        'balance' => $balance['data'],
    ];
} else {
    // INCOMPLETE: Get remaining requirements
    return [
        'status' => 'pending',
        'requirements' => $status['data']['requirements'],
        'refresh_url' => $userClient->userStripeConnect->refresh()['data']['onboarding_url'],
    ];
}
```

---

## Configuration Reference

```php
$client = PartnerClient::create([
    // Authentication (one required)
    'api_key' => 'sk_live_xxx',           // Partner API key
    'bearer_token' => 'user_jwt_token',    // OR user token

    // Endpoints
    'base_url' => 'https://api.senseitemple.com/api',
    'tenant' => 'your-tenant-slug',        // Required for user operations

    // Timeouts (seconds)
    'timeout' => 30,                       // Request timeout
    'connect_timeout' => 10,               // Connection timeout

    // Retry behavior
    'max_retries' => 3,                    // Max retry attempts
    'retry_on_rate_limit' => true,         // Auto-retry on 429

    // SSL
    'verify_ssl' => true,                  // SSL verification
]);
```

---

## Available Resources

| Resource | Property | Use Case |
|----------|----------|----------|
| Users | `$client->users` | User management, signup, login |
| User Stripe Connect | `$client->userStripeConnect` | Seller onboarding (requires user token) |
| Subscriptions | `$client->subscriptions` | Subscription CRUD |
| Products | `$client->products` | Product/formation management |
| Payments | `$client->payments` | Payment, refund, invoice handling |
| Dashboard | `$client->dashboard` | Stats and metrics |
| Analytics | `$client->analytics` | Reports and cohorts |
| Stripe Connect | `$client->stripeConnect` | Partner-level Stripe config |
| SSO | `$client->sso` | OAuth 2.0 / PKCE authentication |
| Webhooks | `$client->webhooks` | Webhook configuration |
| API Keys | `$client->apiKeys` | API key management |
| Compliance | `$client->compliance` | GDPR, tax, DPA |
| Profile | `$client->profile` | Partner profile |
| Settings | `$client->settings` | Partner settings |

---

## Webhook Events

When configuring webhooks, these events are available:

| Event | Trigger |
|-------|---------|
| `user.created` | New user registered via SDK |
| `user.updated` | User profile changed |
| `subscription.created` | New subscription started |
| `subscription.cancelled` | Subscription cancelled |
| `subscription.renewed` | Subscription auto-renewed |
| `payment.completed` | Payment succeeded |
| `payment.failed` | Payment failed |
| `refund.created` | Refund issued |

---

## Testing

```php
// Use test mode API key
$client = PartnerClient::create([
    'api_key' => 'sk_test_xxxxxxxx',  // Note: sk_test_ prefix
]);

// Verify test mode
if ($client->getConfig()->isTestMode()) {
    // Safe for testing, no real transactions
}
```
