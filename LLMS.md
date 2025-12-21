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
environments:
  staging: https://sensei-backend-staging-dtzzw6.laravel.cloud/api
  production: https://api.sensei.com/api  # Coming soon
current_environment: staging
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
├── Trust Score & Reputation
│   ├── Give trust reaction → trustScore.giveReaction()
│   ├── Get user score → trustScore.getUserBreakdown()
│   ├── Vote on guild → trustScore.voteOnGuild()
│   ├── Vote on alliance → trustScore.voteOnAlliance()
│   ├── Check vote eligibility → trustScore.checkNegativeVoteEligibility()
│   └── Respond to negative vote → trustScore.respondToNegativeVote()
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

**CRITICAL**: The SDK uses two authentication levels. Using the wrong one will result in 401/403 errors.

```
┌─────────────────────────────────────────────────────────────────────┐
│                    AUTHENTICATION DECISION TREE                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Who is performing the action?                                      │
│  ├── PARTNER (admin/backend operations)                             │
│  │   └── Use: API Key (sk_live_xxx)                                │
│  │       Resources: users.signupAndLink, users.loginAndLink,       │
│  │                  products, subscriptions, analytics, dashboard, │
│  │                  webhooks, apiKeys, compliance                   │
│  │                                                                  │
│  └── USER (user-owned actions)                                      │
│      └── Use: Bearer Token (from signupAndLink/loginAndLink)       │
│          Resources: guilds, alliances, messages, trustScore,       │
│                     userStripeConnect                               │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### Pattern 1: Partner API Key (Admin Operations)

**Use for**: Creating users, authenticating users, managing products, viewing analytics, admin operations.

```php
use Sensei\PartnerSDK\PartnerClient;

$partnerClient = PartnerClient::create([
    'api_key' => 'sk_live_xxxxxxxxxxxxxxxx',  // Partner API key
    'base_url' => 'https://sensei-backend-staging-dtzzw6.laravel.cloud/api/v1/your-tenant',
]);

// Partner-level operations
$partnerClient->users->signupAndLink([...]);      // Register NEW user
$partnerClient->users->loginAndLink([...]);       // Login EXISTING user
$partnerClient->products->all();                   // List products
$partnerClient->analytics->revenue();              // View revenue
$partnerClient->subscriptions->create([...]);      // Create subscription
```

### Pattern 2: User Bearer Token (User-Owned Actions)

**Use for**: Actions where the USER is the owner/actor (create guild, send messages, join alliance, etc.)

```php
// Step 1: Partner creates/authenticates user and gets token
// Option A: NEW user registration
$result = $partnerClient->users->signupAndLink([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'SecurePass123!',
]);
$userToken = $result['token'];

// Option B: EXISTING user login
$result = $partnerClient->users->loginAndLink([
    'email' => 'john@example.com',
    'password' => 'SecurePass123!',
]);
$userToken = $result['token'];  // SAVE THIS - it's the user's auth

// Step 2: Create user-context client
$userClient = PartnerClient::create([
    'bearer_token' => $userToken,  // User token, NOT API key
    'base_url' => 'https://api.senseitemple.com/api',
    'tenant' => 'your-tenant-slug',
]);

// Step 3: User can now perform user-owned actions
$guild = $userClient->guilds->create([...]);       // User becomes owner
$userClient->messages->startConversation([...]);   // User sends message
$userClient->alliances->apply([...]);              // User's guild applies
$userClient->trustScore->giveReaction([...]);      // User gives trust
$userClient->userStripeConnect->onboard([...]);    // User becomes seller
```

### Resource Authentication Requirements

| Resource | Auth Type | Description |
|----------|-----------|-------------|
| `users.signupAndLink()` | API Key | Partner creates user |
| `users.loginAndLink()` | API Key | Partner authenticates user |
| `products`, `subscriptions` | API Key | Partner manages catalog |
| `analytics`, `dashboard` | API Key | Partner views stats |
| `webhooks`, `apiKeys` | API Key | Partner configuration |
| **`guilds`** | **User Token** | User creates/manages guilds |
| **`alliances`** | **User Token** | User's guild joins alliances |
| **`messages`** | **User Token** | User sends messages |
| **`trustScore`** | **User Token** | User gives/receives trust |
| **`userStripeConnect`** | **User Token** | User becomes seller |

### Complete Integration Flow

```php
// =====================================================
// PARTNER BACKEND: User registration flow
// =====================================================

class UserController {
    private PartnerClient $partnerClient;

    public function __construct() {
        // Partner client with API key (singleton)
        $this->partnerClient = PartnerClient::create([
            'api_key' => config('services.sensei.api_key'),
        ]);
    }

    // Called when user signs up on partner's platform
    public function register(Request $request) {
        // 1. Create user in Sensei
        $result = $this->partnerClient->users->signupAndLink([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 2. Store token in session or return to frontend
        session(['sensei_token' => $result['token']]);

        return $result['user'];
    }

    // Called when user wants to create a guild
    public function createGuild(Request $request) {
        // 1. Get user's token from session
        $userToken = session('sensei_token');

        // 2. Create user-context client
        $userClient = PartnerClient::create([
            'bearer_token' => $userToken,
            'tenant' => config('services.sensei.tenant'),
        ]);

        // 3. Create guild - USER is the owner
        $guild = $userClient->guilds->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return $guild;
    }
}
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

## Trust Score (Reputation System)

The Trust Score system builds reputation through weighted voting. Higher-level users have more influence.

### Trust Score Concepts

```
TRUST SCORE SYSTEM:
├── User Trust Score
│   ├── Based on reactions from other users
│   ├── Weighted by voter level (higher level = more weight)
│   ├── Positive reactions: 0 to 5 score
│   └── Negative reactions: -5 to 0 (requires proof of interaction)
│
├── Guild Trust Score
│   └── Aggregate of member votes on guild trustworthiness
│
└── Alliance Trust Score
    └── Aggregate of votes on alliance trustworthiness
```

### trustScore.giveReaction()

**Purpose**: Give a trust reaction (endorsement) to another user.

```php
// Method signature
$result = $client->trustScore->giveReaction(array $data): array

// Parameters for POSITIVE reaction (score >= 0)
$data = [
    'trustee_uuid' => string,       // REQUIRED: Target user's UUID
    'trust_score' => float,         // REQUIRED: 0 to 5
    'reaction_type' => string,      // OPTIONAL: 'trust', 'endorsement', 'recommendation'
    'comment' => string,            // OPTIONAL: Max 500 chars
];

// Parameters for NEGATIVE reaction (score < 0)
$data = [
    'trustee_uuid' => string,       // REQUIRED: Target user's UUID
    'trust_score' => float,         // REQUIRED: -5 to 0
    'negative_category' => string,  // REQUIRED: Category of issue
    'negative_reason' => string,    // REQUIRED: Detailed reason
    'interaction_type' => string,   // REQUIRED: 'service', 'event', 'formation', etc.
    'interaction_id' => int,        // REQUIRED: ID of the interaction as proof
];

// Response
[
    'message' => 'Trust reaction given successfully',
    'data' => [
        'id' => 1,
        'trustee' => ['uuid' => '...', 'username' => '...', 'name' => '...'],
        'trust_score' => 4.5,
        'voter_level' => 15,
        'weight' => 1.5,            // Weight based on voter level
        'reaction_type' => 'endorsement',
        'is_negative' => false,
    ],
    'remaining_reactions' => 4,     // Weekly limit remaining
]
```

**Negative Vote Categories**:
| Category | Description |
|----------|-------------|
| `fraud` | Fraudulent behavior |
| `no_delivery` | Service/product not delivered |
| `poor_quality` | Quality below expectations |
| `communication` | Poor communication |
| `unprofessional` | Unprofessional conduct |
| `other` | Other issues |

### trustScore.getUserBreakdown()

**Purpose**: Get detailed trust score breakdown for a user.

```php
// Method signature
$breakdown = $client->trustScore->getUserBreakdown(string $userUuid): array

// Response
[
    'user' => ['uuid' => '...', 'username' => '...', 'name' => '...'],
    'trust_score_breakdown' => [
        'total_score' => 4.2,                  // Weighted average
        'total_reactions' => 25,
        'positive_reactions' => 23,
        'negative_reactions' => 2,
        'average_score' => 4.1,
        'breakdown_by_level' => [
            ['level_range' => '1-10', 'count' => 10, 'weight' => 1.0],
            ['level_range' => '11-20', 'count' => 12, 'weight' => 1.5],
            ['level_range' => '21+', 'count' => 3, 'weight' => 2.0],
        ],
    ],
]
```

### trustScore.getMyStats()

**Purpose**: Get current user's weekly reaction statistics.

```php
// Method signature
$stats = $client->trustScore->getMyStats(): array

// Response
[
    'weekly_stats' => [
        'reactions_given_this_week' => 3,
        'reactions_remaining' => 7,            // Can give 7 more this week
        'max_weekly_reactions' => 10,
        'week_resets_at' => '2025-01-27T00:00:00Z',
    ],
    'recent_reactions_given' => [
        [
            'trustee' => ['uuid' => '...', 'username' => '...'],
            'trust_score' => 5,
            'reaction_type' => 'recommendation',
            'created_at' => '2025-01-20T10:00:00Z',
        ],
    ],
]
```

### trustScore.checkNegativeVoteEligibility()

**Purpose**: Check if user can give a negative vote to another user.

```php
// Method signature
$eligibility = $client->trustScore->checkNegativeVoteEligibility(string $userUuid): array

// Response (eligible)
[
    'eligible' => true,
    'voter' => ['uuid' => '...', 'username' => '...', 'level' => 15],
    'trustee' => ['uuid' => '...', 'username' => '...'],
    'interaction' => [
        'type' => 'service',
        'id' => 123,
        'date' => '2025-01-15T10:00:00Z',
    ],
    'message' => 'You are eligible to give a negative vote to this user.',
]

// Response (not eligible)
[
    'eligible' => false,
    'reason' => 'No interaction found within the required window',
    'required_level' => 10,
    'current_level' => 5,
    'interaction_window_days' => 30,
]
```

### trustScore.respondToNegativeVote()

**Purpose**: Respond to a negative vote received.

```php
// Method signature
$result = $client->trustScore->respondToNegativeVote(int $reactionId, string $response): array

// Response
[
    'message' => 'Response submitted successfully',
    'data' => [
        'id' => 1,
        'negative_category' => 'communication',
        'negative_reason' => 'Slow response times',
        'trustee_response' => 'I was on vacation and responded within 48h of return.',
        'trustee_responded_at' => '2025-01-21T10:00:00Z',
    ],
]
```

### Guild Trust Score

```php
// Get guild's trust score breakdown
$score = $client->trustScore->getGuildTrustScore(int $guildId): array

// Vote on a guild (positive)
$result = $client->trustScore->voteOnGuild($guildId, [
    'reaction' => 'positive',
]): array

// Vote on a guild (negative - requires reason)
$result = $client->trustScore->voteOnGuild($guildId, [
    'reaction' => 'negative',
    'negative_reason' => 'Inactive moderators, spam not controlled',
    'proof_of_interaction' => 'Was a member for 3 months',
]): array

// Get my vote on a guild
$myVote = $client->trustScore->getMyGuildVote(int $guildId): array

// Get all votes on a guild
$votes = $client->trustScore->getGuildVotes(int $guildId): PaginatedResponse
```

### Alliance Trust Score

```php
// Get alliance's trust score breakdown
$score = $client->trustScore->getAllianceTrustScore(int|string $allianceId): array

// Vote on an alliance (positive)
$result = $client->trustScore->voteOnAlliance($allianceId, [
    'reaction' => 'positive',
]): array

// Vote on an alliance (negative)
$result = $client->trustScore->voteOnAlliance($allianceId, [
    'reaction' => 'negative',
    'negative_reason' => 'Treasury mismanagement, no transparency',
]): array

// Get my vote on an alliance
$myVote = $client->trustScore->getMyAllianceVote(int|string $allianceId): array

// Get all votes on an alliance
$votes = $client->trustScore->getAllianceVotes(int|string $allianceId): PaginatedResponse
```

### Level Weight System

Higher level users have more influence on trust scores:

| Min Level | Weight | Description |
|-----------|--------|-------------|
| 1 | 1.0x | New users |
| 10 | 1.25x | Established users |
| 20 | 1.5x | Experienced users |
| 30 | 1.75x | Senior users |
| 50 | 2.0x | Veterans |

```php
// Get level weights configuration
$weights = $client->trustScore->getLevelWeights(): array
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

### Configuration Options

```php
$client = PartnerClient::create([
    // Authentication (one required)
    'api_key' => 'sk_live_xxx',           // Partner API key
    'bearer_token' => 'user_jwt_token',    // OR user token

    // Endpoints
    'base_url' => 'https://api.senseitemple.com/api',  // Base URL (NO tenant here!)
    'tenant' => 'your-tenant-slug',        // Tenant slug (separate from base_url)

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

### Tenant Configuration (Auto-Detection)

The SDK **automatically detects** the tenant from the base URL if you copy the full URL from the partner dashboard.

```
AUTO-DETECTION: Copy the URL directly from dashboard
┌─────────────────────────────────────────────────────────────────────┐
│ // SDK will auto-extract tenant from URL                            │
│ 'base_url' => 'https://sensei-backend-staging-dtzzw6.laravel.cloud/api/v1/firstclasscitizen'
│                                                                     │
│ // Automatically parsed as:                                         │
│ // base_url → https://sensei-backend-staging-dtzzw6.laravel.cloud/api
│ // tenant   → firstclasscitizen                                     │
└─────────────────────────────────────────────────────────────────────┘

EXPLICIT CONFIGURATION (also supported):
┌─────────────────────────────────────────────────────────────────────┐
│ 'base_url' => 'https://sensei-backend-staging-dtzzw6.laravel.cloud/api'
│ 'tenant' => 'firstclasscitizen'                                     │
└─────────────────────────────────────────────────────────────────────┘
```

**How it works:**
- URL pattern `/api/v1/{tenant}` is detected and parsed automatically
- The tenant is extracted and stored separately
- Compliance routes use `/v1/partners/...` (no tenant)
- User routes use `/v1/{tenant}/...` (with tenant)

**Staging vs Production URLs:**
```php
// STAGING (current)
$client = PartnerClient::create([
    'api_key' => 'sk_live_xxx',
    'base_url' => 'https://sensei-backend-staging-dtzzw6.laravel.cloud/api/v1/firstclasscitizen',
]);

// PRODUCTION (future)
$client = PartnerClient::create([
    'api_key' => 'sk_live_xxx',
    'base_url' => 'https://api.sensei.com/api/v1/firstclasscitizen',
]);
```

### When is Tenant Required?

| Resource | Tenant Required | Reason |
|----------|-----------------|--------|
| `compliance` | NO | Global partner routes (`/v1/partners/compliance/...`) |
| `users` | NO | Global partner routes (`/v1/partners/users/...`) |
| `products` | NO | Global partner routes |
| `subscriptions` | NO | Global partner routes |
| `dashboard` | NO | Global partner routes |
| `analytics` | NO | Global partner routes |
| **`guilds`** | **YES** | Tenant-scoped (`/v1/{tenant}/guilds/...`) |
| **`alliances`** | **YES** | Tenant-scoped (`/v1/{tenant}/alliances/...`) |
| **`messages`** | **YES** | Tenant-scoped |
| **`trustScore`** | **YES** | Tenant-scoped |
| **`userStripeConnect`** | **YES** | Tenant-scoped |

### Two Client Pattern

For most integrations, you'll need two client configurations:

```php
// 1. Partner Client (NO tenant needed) - for admin operations
$partnerClient = PartnerClient::create([
    'api_key' => 'sk_live_xxx',
    'base_url' => 'https://api.senseitemple.com/api',
    // NO tenant - uses global partner routes
]);

// Partner operations (no tenant needed)
$partnerClient->users->signupAndLink([...]);           // → /v1/partners/users/signup-and-link
$partnerClient->compliance->requestDeletion($userId);   // → /v1/partners/compliance/gdpr/delete
$partnerClient->products->all();                        // → /v1/partners/products

// 2. User Client (tenant REQUIRED) - for user-context operations
$userClient = PartnerClient::create([
    'bearer_token' => $userToken,
    'base_url' => 'https://api.senseitemple.com/api',
    'tenant' => 'your-tenant-slug',  // Required for tenant-scoped routes
]);

// User operations (tenant required)
$userClient->guilds->create([...]);         // → /v1/your-tenant-slug/guilds
$userClient->alliances->create([...]);      // → /v1/your-tenant-slug/alliances
$userClient->trustScore->giveReaction([...]); // → /v1/your-tenant-slug/community/trust
```

### Error: Missing Tenant

If you call a tenant-scoped resource without configuring a tenant, you'll get:

```
SenseiPartnerException: This endpoint requires a tenant to be configured.
Please set the "tenant" option when creating the client:
PartnerClient::create(['api_key' => '...', 'tenant' => 'your-tenant-slug'])
```

---

## Available Resources

| Resource | Property | Auth | Tenant | Use Case |
|----------|----------|------|--------|----------|
| Users | `$client->users` | API Key | No | User creation (signupAndLink, loginAndLink) |
| **Guilds** | `$client->guilds` | **User Token** | **Yes** | User creates/manages guilds as owner |
| **Alliances** | `$client->alliances` | **User Token** | **Yes** | User's guild joins federations |
| **Messages** | `$client->messages` | **User Token** | **Yes** | User sends DMs, channel messages |
| **TrustScore** | `$client->trustScore` | **User Token** | **Yes** | User gives/receives reputation |
| **UserStripeConnect** | `$client->userStripeConnect` | **User Token** | **Yes** | User becomes seller |
| Subscriptions | `$client->subscriptions` | API Key | No | Partner manages subscriptions |
| Products | `$client->products` | API Key | No | Partner manages catalog |
| Payments | `$client->payments` | API Key | No | Partner handles payments |
| Dashboard | `$client->dashboard` | API Key | No | Partner views stats |
| Analytics | `$client->analytics` | API Key | No | Partner reports |
| StripeConnect | `$client->stripeConnect` | API Key | No | Partner Stripe config |
| SSO | `$client->sso` | API Key | No | Partner OAuth setup |
| Webhooks | `$client->webhooks` | API Key | No | Partner webhooks |
| API Keys | `$client->apiKeys` | API Key | No | Partner key management |
| Compliance | `$client->compliance` | API Key | No | GDPR, tax, DPA |
| Profile | `$client->profile` | API Key | Partner profile |
| Settings | `$client->settings` | API Key | Partner settings |

---

## Compliance & GDPR

The SDK provides comprehensive GDPR compliance tools through the `compliance` resource.

### Quick Decision Tree

```
GDPR TASK: What do you need to do?
├── User requests their data (Article 15)
│   └── compliance.requestDataExport(userId) → Get download URL
│
├── User requests deletion (Article 17 - Right to be forgotten)
│   └── compliance.requestDeletion(userId, reason)
│
├── Record user consent (Article 7)
│   └── compliance.recordConsent(userId, consentType, granted)
│
├── Manage Data Processing Agreement (DPA)
│   ├── Get current DPA → compliance.getCurrentDpa()
│   ├── Sign DPA → compliance.signDpa(dpaId, signerInfo)
│   └── Download signed → compliance.downloadDpa(dpaId)
│
├── Configure data retention (Article 5)
│   ├── Get settings → compliance.retentionSettings()
│   └── Update settings → compliance.updateRetentionSettings(settings)
│
└── Audit trail (Article 30)
    ├── Get logs → compliance.auditLogs()
    └── Export logs → compliance.exportAuditLogs(startDate, endDate)
```

### GDPR Status Check

```php
// Check overall GDPR compliance status
$status = $client->compliance->gdprStatus();

// Response
[
    'compliant' => true,
    'dpa_signed' => true,
    'retention_configured' => true,
    'consent_types_defined' => true,
    'checklist_completion' => 85,  // percentage
]
```

### Data Export (Article 15 - Right of Access)

```php
// Request data export for a user
$request = $client->compliance->requestDataExport(int $userId);
// Route: POST /v1/partners/compliance/gdpr/export

// Response
[
    'id' => 123,
    'user_id' => 456,
    'status' => 'processing',  // 'pending', 'processing', 'completed', 'failed'
    'requested_at' => '2025-01-20T10:00:00Z',
]

// Get specific export request
$export = $client->compliance->getDataExportRequest(int $requestId);
// Route: GET /v1/partners/compliance/gdpr/exports/{requestId}

// Get download URL when ready
$download = $client->compliance->getDataExportUrl(int $requestId);
// Route: GET /v1/partners/compliance/gdpr/exports/{requestId}/download

// Response (when completed)
[
    'status' => 'completed',
    'download_url' => 'https://...',  // Temporary signed URL
    'expires_at' => '2025-01-21T10:00:00Z',
    'format' => 'json',
]

// List all export requests
$requests = $client->compliance->dataExportRequests(['status' => 'completed']);
// Route: GET /v1/partners/compliance/gdpr/exports
```

### Data Deletion (Article 17 - Right to Erasure)

```php
// Request user data deletion
$result = $client->compliance->requestDeletion(
    int $userId,
    string $reason = 'User requested account deletion'
);
// Route: POST /v1/partners/compliance/gdpr/delete

// Response
[
    'id' => 789,
    'user_id' => 456,
    'reason' => 'User requested account deletion',
    'status' => 'pending',  // 'pending', 'processing', 'completed', 'rejected'
    'scheduled_at' => '2025-01-27T00:00:00Z',  // 7-day grace period
]

// List deletion requests
$requests = $client->compliance->deletionRequests(['status' => 'pending']);
// Route: GET /v1/partners/compliance/gdpr/deletions

// Get specific deletion request
$request = $client->compliance->getDeletionRequest(int $requestId);
// Route: GET /v1/partners/compliance/gdpr/deletions/{requestId}

// Approve deletion request (admin action)
$result = $client->compliance->approveDeletion(int $requestId);
// Route: PATCH /v1/partners/compliance/gdpr/deletions/{requestId}/approve

// Reject deletion request with reason
$result = $client->compliance->rejectDeletion(int $requestId, 'Reason for rejection');
// Route: PATCH /v1/partners/compliance/gdpr/deletions/{requestId}/reject
```

**Important**: Deletion requests have a grace period (typically 7 days) before execution, allowing users to cancel if needed.

### Consent Management (Article 7)

```php
// Get available consent types
$types = $client->compliance->consentTypes();

// Response
[
    ['type' => 'marketing', 'description' => 'Marketing communications', 'required' => false],
    ['type' => 'analytics', 'description' => 'Usage analytics', 'required' => false],
    ['type' => 'terms', 'description' => 'Terms of Service', 'required' => true],
    ['type' => 'privacy', 'description' => 'Privacy Policy', 'required' => true],
]

// Record user consent
$result = $client->compliance->recordConsent(
    int $userId,
    string $consentType,  // 'marketing', 'analytics', etc.
    bool $granted         // true = consented, false = declined
);

// Response
[
    'id' => 123,
    'user_id' => 456,
    'consent_type' => 'marketing',
    'granted' => true,
    'ip_address' => '192.168.1.1',
    'user_agent' => 'Mozilla/5.0...',
    'recorded_at' => '2025-01-20T10:00:00Z',
]

// Get user's consent history
$consents = $client->compliance->consents(['user_id' => 456]);
```

### Data Processing Agreement (DPA)

```php
// Get current DPA document
$dpa = $client->compliance->getCurrentDpa();
// Route: GET /v1/partners/compliance/dpa

// Response
[
    'id' => 1,
    'version' => '2.0',
    'title' => 'Data Processing Agreement',
    'content' => '...',  // Full DPA text
    'effective_date' => '2025-01-01',
    'requires_signature' => true,
]

// Sign the DPA
$result = $client->compliance->signDpa(int $dpaId, [
    'signer_name' => 'John Doe',
    'signer_title' => 'CEO',
    'signer_email' => 'john@company.com',
    'company_name' => 'Acme Inc.',
]);
// Route: POST /v1/partners/compliance/dpa/sign

// Response
[
    'id' => 123,
    'dpa_id' => 1,
    'signed_at' => '2025-01-20T10:00:00Z',
    'signer' => [...],
    'signature_ip' => '192.168.1.1',
]

// Download signed DPA as PDF
$pdf = $client->compliance->downloadDpa(int $dpaId);
// Route: GET /v1/partners/compliance/dpa/download
// Returns: ['download_url' => 'https://...']

// List all DPAs (including historical versions)
$dpas = $client->compliance->dpaList();
// Route: GET /v1/partners/compliance/dpa
```

### Data Retention Policies (Article 5)

```php
// Get all retention policies
$policies = $client->compliance->retentionPolicies();

// Response
[
    'data' => [
        [
            'id' => 1,
            'name' => 'User Data Policy',
            'data_type' => 'user_data',
            'retention_days' => 365,
            'auto_delete' => false,
            'legal_hold' => false,
        ],
    ],
]

// Create retention policy
$policy = $client->compliance->createRetentionPolicy([
    'name' => 'Activity Logs Policy',
    'data_type' => 'activity_logs',
    'retention_days' => 90,
    'auto_delete' => true,
]);

// Get specific policy
$policy = $client->compliance->getRetentionPolicy(int $policyId);

// Update retention policy
$result = $client->compliance->updateRetentionPolicy($policyId, [
    'retention_days' => 30,
    'auto_delete' => true,
]);

// Delete retention policy
$result = $client->compliance->deleteRetentionPolicy(int $policyId);

// Preview policy enforcement (shows what will be affected)
$preview = $client->compliance->previewRetentionPolicy(int $policyId);

// Enforce retention policy (actually delete old data)
$result = $client->compliance->enforceRetentionPolicy(int $policyId);

// Get policy enforcement history
$history = $client->compliance->retentionPolicyHistory(int $policyId);

// Legal hold (prevents data deletion)
$client->compliance->activateLegalHold(int $policyId);    // Activate
$client->compliance->releaseLegalHold(int $policyId);     // Release
```

### Audit Logs (Article 30)

```php
// Get audit logs
$logs = $client->compliance->auditLogs([
    'action' => 'user.deleted',      // Filter by action
    'user_id' => 456,                // Filter by user
    'start_date' => '2025-01-01',
    'end_date' => '2025-01-31',
]);

// Response (paginated)
[
    'data' => [
        [
            'id' => 1,
            'action' => 'user.deleted',
            'actor_id' => 123,
            'target_type' => 'user',
            'target_id' => 456,
            'metadata' => ['reason' => 'User requested deletion'],
            'ip_address' => '192.168.1.1',
            'created_at' => '2025-01-20T10:00:00Z',
        ],
    ],
]

// Export audit logs
$export = $client->compliance->exportAuditLogs(
    string $startDate,   // '2025-01-01'
    string $endDate,     // '2025-01-31'
    string $format       // 'csv' or 'json'
);

// Response
[
    'download_url' => 'https://...',
    'format' => 'csv',
    'record_count' => 1250,
]

// Get specific audit log entry
$log = $client->compliance->auditLog(int $logId);
```

### Compliance Checklist

```php
// Get compliance checklist
$checklist = $client->compliance->checklist();

// Response
[
    [
        'id' => 1,
        'category' => 'gdpr',
        'item' => 'Sign Data Processing Agreement',
        'completed' => true,
        'completed_at' => '2025-01-15T10:00:00Z',
    ],
    [
        'id' => 2,
        'category' => 'gdpr',
        'item' => 'Configure data retention policies',
        'completed' => false,
        'required' => true,
    ],
]

// Mark checklist item as complete
$result = $client->compliance->completeChecklistItem(int $itemId, [
    'evidence' => 'Policy document uploaded to internal wiki',
]);

// Run compliance check (validates current state)
$check = $client->compliance->runComplianceCheck();

// Response
[
    'overall_status' => 'compliant',  // or 'non_compliant', 'partial'
    'issues' => [],                   // List of issues if any
    'recommendations' => [...],
    'last_check' => '2025-01-20T10:00:00Z',
]
```

### Legal Documents

```php
// Get all legal documents
$docs = $client->compliance->legalDocuments();

// Get Terms of Service
$terms = $client->compliance->termsOfService();

// Get Privacy Policy
$privacy = $client->compliance->privacyPolicy();

// Accept terms (record user acceptance)
$result = $client->compliance->acceptTerms(
    string $documentType,  // 'terms' or 'privacy'
    string $version        // Document version accepted
);
```

### GDPR Integration Flow

```php
// Complete GDPR-compliant user registration flow

class GDPRCompliantRegistration {
    private PartnerClient $client;

    public function register(Request $request) {
        // 1. Create user
        $signup = $this->client->users->signupAndLink([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $userId = $signup['user']['id'];

        // 2. Record required consents
        $this->client->compliance->recordConsent($userId, 'terms', true);
        $this->client->compliance->recordConsent($userId, 'privacy', true);

        // 3. Record optional consents (based on user choices)
        if ($request->marketing_consent) {
            $this->client->compliance->recordConsent($userId, 'marketing', true);
        }

        return $signup;
    }

    public function handleDeletionRequest(int $userId) {
        // 1. Request deletion from Sensei
        $this->client->compliance->requestDeletion($userId, 'User requested account deletion');

        // 2. Delete/anonymize local data
        // ... your local cleanup code

        // 3. Log for audit
        Log::info('User deletion requested', ['user_id' => $userId]);
    }

    public function handleExportRequest(int $userId) {
        // 1. Request export from Sensei
        $request = $this->client->compliance->requestDataExport($userId);

        // 2. Poll for completion or use webhook
        return $request;
    }
}
```

### Rate Limiting

The SDK handles rate limiting automatically:

```php
// Configuration
$client = PartnerClient::create([
    'api_key' => 'sk_live_xxx',
    'retry_on_rate_limit' => true,  // Auto-retry on 429 (default: true)
    'max_retries' => 3,             // Max retry attempts
]);

// Manual handling if needed
try {
    $result = $client->users->signupAndLink([...]);
} catch (RateLimitException $e) {
    $retryAfter = $e->getRetryAfter();  // Seconds to wait
    sleep($retryAfter);
    // Retry the request
}
```

**Rate Limits** (default):
| Endpoint Type | Limit |
|--------------|-------|
| Authentication | 10/min per IP |
| Read operations | 120/min per API key |
| Write operations | 60/min per API key |
| Bulk operations | 10/min per API key |

---

## Webhook Events

When configuring webhooks, these events are available:

| Event | Trigger |
|-------|---------|
| `user.created` | New user registered via SDK |
| `user.updated` | User profile changed |
| `user.deleted` | User account deleted (GDPR) |
| `subscription.created` | New subscription started |
| `subscription.cancelled` | Subscription cancelled |
| `subscription.renewed` | Subscription auto-renewed |
| `payment.completed` | Payment succeeded |
| `payment.failed` | Payment failed |
| `refund.created` | Refund issued |
| `compliance.export_ready` | Data export is ready for download |
| `compliance.deletion_completed` | User data deletion completed |
| `compliance.consent_updated` | User consent preference changed |
| `guild.created` | New guild created |
| `guild.member_joined` | User joined a guild |
| `guild.member_left` | User left a guild |
| `alliance.created` | New alliance created |
| `alliance.war_declared` | War declared between alliances |
| `alliance.war_ended` | Alliance war concluded |
| `trust.reaction_received` | User received a trust reaction |
| `trust.negative_vote` | User received a negative trust vote |

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
