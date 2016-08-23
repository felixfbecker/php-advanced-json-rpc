<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

/**
 * Serves as type hint for arguments
 */
class Argument
{
    /**
     * @var string
     */
    public $aProperty;

    public function __construct(string $aProperty = null)
    {
        $this->aProperty = $aProperty;
    }
}
