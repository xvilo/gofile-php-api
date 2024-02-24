<?php

declare(strict_types=1);

namespace Xvilo\GoFile\Api;

final class AccountApi extends AbstractApi
{
    public function getDetails(): array
    {
        return json_decode(
            json: $this->get('/getAccountDetails'),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }
}