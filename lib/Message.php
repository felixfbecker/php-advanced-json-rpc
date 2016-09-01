<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc;

use JsonSerializable;

/**
 * Base message
 */
abstract class Message implements JsonSerializable
{
    /**
     * A String specifying the version of the JSON-RPC protocol. MUST be exactly "2.0".
     *
     * @var string
     */
    public $jsonrpc = '2.0';

    /**
     * Returns the appropiate Message subclass
     *
     * @param string $msg
     * @return Message
     */
    public static function parse(string $msg): Message
    {
        $decoded = json_decode($msg);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ResponseError(json_last_error_msg(), ErrorCode::PARSE_ERROR);
        }
        if (Notification::isNotification($decoded)) {
            $obj = new Notification($decoded->method, $decoded->params ?? null);
        } else if (Request::isRequest($decoded)) {
            $obj = new Request($decoded->id, $decoded->method, $decoded->params ?? null);
        } else if (Response::isResponse($decoded)) {
            $obj = new Response($decoded->id, $decoded->result ?? null, $decoded->error ?? null);
        }
        return $obj;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }

    public function jsonSerialize()
    {
        return ['jsonrpc' => $this->jsonrpc];
    }
}
