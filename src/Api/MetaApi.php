<?php

declare(strict_types=1);

namespace Xvilo\GoFile\Api;

final class MetaApi extends AbstractApi
{
    public function getServer(): array
    {
        return json_decode(
            json: $this->get('/getServer'),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }
}