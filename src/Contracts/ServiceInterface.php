<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Contracts;

use AndiSiahaan\Digiflazz\Contracts\ClientInterface;

/**
 * Base interface for all service classes.
 */
interface ServiceInterface
{
    /**
     * Get the underlying client instance.
     */
    public function getClient(): ClientInterface;
}
