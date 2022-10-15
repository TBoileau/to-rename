<?php

declare(strict_types=1);

namespace App\UseCase\RegisterNewsletter;

use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Model\CreateContact;

final class RegisterNewsletter implements RegisterNewsletterInterface
{
    public function __construct(private readonly ContactsApi $contactsApi, private readonly int $sendinBlueListId)
    {
    }

    public function register(string $email): void
    {
        $this->contactsApi->createContact(
            (new CreateContact())
                ->setEmail($email)
                ->setListIds([$this->sendinBlueListId])
        );
    }
}
