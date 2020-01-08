<?php
declare(strict_types=1);

function uuid_v4(): string
{
    return uuid_create(UUID_TYPE_RANDOM);
}
