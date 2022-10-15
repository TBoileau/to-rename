<?php

declare(strict_types=1);

namespace App\SendinBlue;

use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Api\EmailCampaignsApi;
use SendinBlue\Client\Configuration;

final class SendinBlueFactory
{
    private Configuration $config;

    public function __construct(string $sendinBlueApiKey)
    {
        $this->config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $sendinBlueApiKey);
    }

    public function createEmailCampaignsApi(): EmailCampaignsApi
    {
        return new EmailCampaignsApi(config: $this->config);
    }

    public function createContactsApi(): ContactsApi
    {
        return new ContactsApi(config: $this->config);
    }
}
