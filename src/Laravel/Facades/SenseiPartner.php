<?php

declare(strict_types=1);

namespace Sensei\PartnerSDK\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Sensei\PartnerSDK\PartnerClient;
use Sensei\PartnerSDK\Resources\ApiKeys;
use Sensei\PartnerSDK\Resources\Analytics;
use Sensei\PartnerSDK\Resources\Compliance;
use Sensei\PartnerSDK\Resources\Dashboard;
use Sensei\PartnerSDK\Resources\Payments;
use Sensei\PartnerSDK\Resources\Products;
use Sensei\PartnerSDK\Resources\Profile;
use Sensei\PartnerSDK\Resources\Settings;
use Sensei\PartnerSDK\Resources\Sso;
use Sensei\PartnerSDK\Resources\StripeConnect;
use Sensei\PartnerSDK\Resources\Subscriptions;
use Sensei\PartnerSDK\Resources\Users;
use Sensei\PartnerSDK\Resources\Webhooks;

/**
 * @method static ApiKeys apiKeys()
 * @method static Analytics analytics()
 * @method static Compliance compliance()
 * @method static Dashboard dashboard()
 * @method static Payments payments()
 * @method static Products products()
 * @method static Profile profile()
 * @method static Settings settings()
 * @method static Sso sso()
 * @method static StripeConnect stripeConnect()
 * @method static Subscriptions subscriptions()
 * @method static Users users()
 * @method static Webhooks webhooks()
 * @method static array get(string $endpoint, array $query = [])
 * @method static array post(string $endpoint, array $data = [])
 * @method static array put(string $endpoint, array $data = [])
 * @method static array patch(string $endpoint, array $data = [])
 * @method static array delete(string $endpoint, array $data = [])
 * @method static bool isAuthenticated()
 * @method static PartnerClient withApiKey(string $apiKey)
 * @method static PartnerClient withBearerToken(string $token)
 *
 * @see \Sensei\PartnerSDK\PartnerClient
 */
class SenseiPartner extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sensei-partner';
    }
}
