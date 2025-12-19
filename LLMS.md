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
├── Manage guilds/communities
│   ├── Create guild → guilds.create()
│   ├── List guilds → guilds.all()
│   ├── Add member → guilds.addMember()
│   ├── Create channel → guilds.createChannel()
│   ├── Manage roles → guilds.createRole(), guilds.updateMemberRole()
│   └── Guild invites → guilds.createInvite()
│
├── Manage alliances (guild federations)
│   ├── Create alliance → alliances.create()
│   ├── Invite guild → alliances.invite()
│   ├── Apply to join → alliances.apply()
│   ├── Treasury → alliances.treasury(), alliances.contribute()
│   └── Alliance wars → alliances.declareWar(), alliances.warStatus()
│
├── Messaging system
│   ├── Start conversation → messages.startConversation()
│   ├── Send DM → messages.sendInConversation()
│   ├── Channel messages → messages.sendInChannel()
│   ├── Threads → messages.createThread(), messages.sendInThread()
│   └── Announcements → messages.sendAnnouncement()
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

## Guilds (Communities)

Guilds are communities where users can interact, learn, and collaborate.

### guilds.create()

**Purpose**: Create a new guild/community.

```php
// Method signature
$guild = $client->guilds->create(array $data): array

// Parameters
$data = [
    'name' => string,              // REQUIRED: Guild name
    'description' => string,       // OPTIONAL: Guild description
    'is_public' => bool,           // OPTIONAL: Public visibility (default: true)
    'max_members' => int,          // OPTIONAL: Maximum member count
];

// Response structure
[
    'id' => 1,
    'name' => 'My Community',
    'slug' => 'my-community',
    'description' => '...',
    'member_count' => 1,
    'owner_id' => 123,
]
```

### guilds.addMember()

**Purpose**: Add a user to a guild.

```php
// Method signature
$result = $client->guilds->addMember(int $guildId, int $userId, array $data = []): array

// Parameters
$data = [
    'role_id' => int,  // OPTIONAL: Initial role ID
];

// Response
['message' => 'Member added successfully']
```

### guilds.createChannel()

**Purpose**: Create a communication channel in a guild.

```php
// Method signature
$channel = $client->guilds->createChannel(int $guildId, array $data): array

// Parameters
$data = [
    'name' => string,        // REQUIRED: Channel name
    'type' => string,        // OPTIONAL: 'text' or 'voice' (default: 'text')
    'category_id' => int,    // OPTIONAL: Parent category ID
    'is_private' => bool,    // OPTIONAL: Restrict to certain roles
];

// Response
[
    'id' => 1,
    'name' => 'general',
    'type' => 'text',
    'guild_id' => 123,
]
```

### guilds.createRole()

**Purpose**: Create a role with permissions.

```php
// Method signature
$role = $client->guilds->createRole(int $guildId, array $data): array

// Parameters
$data = [
    'name' => string,           // REQUIRED: Role name
    'color' => string,          // OPTIONAL: Hex color (#FF0000)
    'permissions' => array,     // OPTIONAL: Permission flags
    'is_mentionable' => bool,   // OPTIONAL: Can be @mentioned
];

// Response
[
    'id' => 1,
    'name' => 'Moderator',
    'color' => '#3498db',
    'permissions' => ['manage_messages', 'kick_members'],
]
```

### guilds.updateMemberRole()

**Purpose**: Assign a role to a member.

```php
// Method signature
$result = $client->guilds->updateMemberRole(int $guildId, int $userId, int $roleId): array
```

### guilds.createInvite()

**Purpose**: Generate an invite link for the guild.

```php
// Method signature
$invite = $client->guilds->createInvite(int $guildId, array $data = []): array

// Parameters
$data = [
    'max_uses' => int,          // OPTIONAL: Maximum uses (0 = unlimited)
    'expires_in' => int,        // OPTIONAL: Expiration in hours
];

// Response
[
    'code' => 'abc123xyz',
    'url' => 'https://sensei.com/invite/abc123xyz',
    'max_uses' => 100,
    'uses' => 0,
    'expires_at' => '2025-02-01T00:00:00Z',
]
```

### Guild Member Moderation

```php
// Ban a member
$client->guilds->banMember(int $guildId, int $userId, string $reason = ''): array

// Unban a member
$client->guilds->unbanMember(int $guildId, int $userId): array

// Mute a member
$client->guilds->muteMember(int $guildId, int $userId, int $durationMinutes): array

// Unmute a member
$client->guilds->unmuteMember(int $guildId, int $userId): array

// Get banned members
$banned = $client->guilds->bannedMembers(int $guildId): array
```

---

## Alliances (Guild Federations)

Alliances are federations of guilds that cooperate, share resources, and compete.

### alliances.create()

**Purpose**: Create a new alliance between guilds.

```php
// Method signature
$alliance = $client->alliances->create(array $data): array

// Parameters
$data = [
    'name' => string,              // REQUIRED: Alliance name
    'slug' => string,              // OPTIONAL: URL-friendly slug
    'description' => string,       // OPTIONAL: Alliance description
    'founder_guild_id' => int,     // REQUIRED: Guild creating the alliance
    'max_guilds' => int,           // OPTIONAL: Max member guilds (default: 10)
    'requires_approval' => bool,   // OPTIONAL: Require approval to join (default: true)
    'min_guild_level' => int,      // OPTIONAL: Minimum guild level to join (default: 1)
];

// Response
[
    'message' => 'Alliance created successfully',
    'alliance' => [
        'id' => 1,
        'name' => 'The Federation',
        'slug' => 'the-federation',
        'founder_guild_id' => 123,
        'member_count' => 1,
        'total_xp' => 0,
    ],
]
```

### alliances.invite()

**Purpose**: Invite a guild to join the alliance.

```php
// Method signature
$result = $client->alliances->invite(int|string $alliance, int $guildId): array

// Response
[
    'message' => 'Guild invited successfully',
    'membership' => [
        'id' => 1,
        'guild_id' => 456,
        'role' => 'member',
        'is_approved' => false,  // Pending if requires_approval=true
    ],
]
```

### alliances.apply()

**Purpose**: Apply to join an alliance.

```php
// Method signature
$result = $client->alliances->apply(int|string $alliance, int $guildId, ?string $message = null): array

// Response
[
    'message' => 'Application submitted successfully',
    'membership' => [...],
]
```

### Alliance Membership Management

```php
// Accept a guild's application
$client->alliances->acceptMember(int|string $alliance, int $membershipId): array

// Reject an application
$client->alliances->rejectMember(int|string $alliance, int $membershipId): array

// Leave the alliance (guild owner action)
$client->alliances->leave(int|string $alliance, int $guildId): array

// Kick a guild from alliance (leader action)
$client->alliances->kick(int|string $alliance, int $membershipId): array

// Set guild role: 'member', 'officer', or 'leader'
$client->alliances->setRole(int|string $alliance, int $membershipId, string $role): array

// Transfer leadership to another guild
$client->alliances->transferLeadership(int|string $alliance, int $guildId): array
```

### alliances.treasury()

**Purpose**: Manage the shared alliance treasury.

```php
// Get treasury balance
$treasury = $client->alliances->treasury(int|string $alliance): array
// Response: ['treasury' => ['balance' => 50000, 'contribution_rate' => 5.0, ...]]

// Make a contribution
$result = $client->alliances->contribute(
    int|string $alliance,
    int $guildId,
    float $amount,
    ?string $description = null
): array

// Propose an expense (may require vote if amount > threshold)
$result = $client->alliances->proposeExpense(
    int|string $alliance,
    float $amount,
    string $description
): array

// Vote on an expense proposal
$result = $client->alliances->voteOnExpense(
    int|string $alliance,
    int $transactionId,
    int $guildId,
    bool $inFavor
): array

// Get transaction history
$transactions = $client->alliances->transactions(int|string $alliance): PaginatedResponse

// Get pending votes
$pending = $client->alliances->pendingVotes(int|string $alliance): array
```

### Alliance Wars

**Purpose**: Compete against other alliances in wars.

```php
// Declare war on another alliance
$war = $client->alliances->declareWar(
    int|string $alliance,      // Your alliance (challenger)
    int $defenderAllianceId    // Target alliance
): array
// Response: ['message' => 'War declared', 'war' => [...]]

// Accept a war declaration (defender)
$result = $client->alliances->acceptWar(int|string $alliance, int $warId): array

// Decline a war declaration
$result = $client->alliances->declineWar(int|string $alliance, int $warId): array

// Get current war status
$status = $client->alliances->warStatus(int|string $alliance): array
// Response: ['war' => [...], 'statistics' => [...]]

// Get war history
$history = $client->alliances->warHistory(int|string $alliance): PaginatedResponse

// Get war contribution leaderboard
$leaderboard = $client->alliances->warLeaderboard(int|string $alliance, int $warId): array
```

**War Flow**:
```
1. Alliance A calls declareWar(A, B) → War status: 'pending'
2. Alliance B calls acceptWar(B, warId) → War status: 'active'
   OR Alliance B calls declineWar(B, warId) → War status: 'declined'
3. During war: Members complete quests/raids to earn points
4. After duration: Winner determined by total points
```

---

## Messages (Communication)

### messages.startConversation()

**Purpose**: Start a direct message conversation.

```php
// Method signature
$conversation = $client->messages->startConversation(
    array $participantIds,
    ?string $initialMessage = null
): array

// Parameters
$participantIds = [123, 456];  // User IDs
$initialMessage = 'Hello!';    // Optional first message

// Response
[
    'id' => 1,
    'participants' => [...],
    'last_message' => [...],
    'created_at' => '2025-01-01T00:00:00Z',
]
```

### messages.sendInConversation()

**Purpose**: Send a message in a DM conversation.

```php
// Method signature
$message = $client->messages->sendInConversation(int $conversationId, array $data): array

// Parameters
$data = [
    'content' => string,        // REQUIRED: Message text
    'attachments' => array,     // OPTIONAL: Attachment IDs
];

// Response
[
    'id' => 1,
    'content' => 'Hello!',
    'sender_id' => 123,
    'conversation_id' => 1,
    'created_at' => '2025-01-01T00:00:00Z',
]
```

### messages.sendInChannel()

**Purpose**: Send a message in a guild channel.

```php
// Method signature
$message = $client->messages->sendInChannel(int $guildId, int $channelId, array $data): array

// Parameters
$data = [
    'content' => string,        // REQUIRED: Message text
    'attachments' => array,     // OPTIONAL: Attachment IDs
    'mentions' => array,        // OPTIONAL: User IDs to mention
];
```

### messages.createThread()

**Purpose**: Create a thread from a message.

```php
// Method signature
$thread = $client->messages->createThread(int $messageId, array $data): array

// Parameters
$data = [
    'name' => string,  // REQUIRED: Thread name
];
```

### messages.sendInThread()

**Purpose**: Reply in a thread.

```php
// Method signature
$message = $client->messages->sendInThread(int $threadId, array $data): array
```

### messages.sendAnnouncement()

**Purpose**: Send an announcement to all guild members.

```php
// Method signature
$announcement = $client->messages->sendAnnouncement(int $guildId, array $data): array

// Parameters
$data = [
    'title' => string,          // REQUIRED: Announcement title
    'content' => string,        // REQUIRED: Announcement body
    'is_pinned' => bool,        // OPTIONAL: Pin announcement
    'notify_all' => bool,       // OPTIONAL: Send push notifications
];
```

### Message Actions

```php
// Edit a message (sender only)
$client->messages->edit(int $messageId, string $content): array

// Delete a message
$client->messages->delete(int $messageId): array

// Pin a message
$client->messages->pin(int $messageId): array

// Unpin a message
$client->messages->unpin(int $messageId): array

// Add reaction
$client->messages->addReaction(int $messageId, string $emoji): array

// Remove reaction
$client->messages->removeReaction(int $messageId, string $emoji): array

// Report message (moderation)
$client->messages->report(int $messageId, string $reason): array

// Search messages
$results = $client->messages->search(string $query, array $params = []): PaginatedResponse

// Get unread count
$unread = $client->messages->unreadCount(): array
```

### Conversation Management

```php
// List conversations
$conversations = $client->messages->conversations(): PaginatedResponse

// Get conversation messages
$messages = $client->messages->conversationMessages(int $conversationId): PaginatedResponse

// Mark as read
$client->messages->markConversationRead(int $conversationId): array

// Leave conversation
$client->messages->leaveConversation(int $conversationId): array

// Add participants to group conversation
$client->messages->addParticipants(int $conversationId, array $userIds): array

// Remove participant
$client->messages->removeParticipant(int $conversationId, int $userId): array
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
| **Guilds** | `$client->guilds` | Community/guild management, members, roles, channels |
| **Alliances** | `$client->alliances` | Guild federations, treasury, wars |
| **Messages** | `$client->messages` | DMs, channel messages, threads, announcements |
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
