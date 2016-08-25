<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc;

/**
 * Base message
 */
abstract class Message
{
    /**
     * A String specifying the version of the JSON-RPC protocol. MUST be exactly "2.0".
     *
     * @var string
     */
    public $jsonrpc;

    public function __toString(): string
    {
        return json_encode($this);
    }
}
