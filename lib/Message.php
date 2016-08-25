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
            $obj = new Notification;
        } else if (Request::isRequest($decoded)) {
            $obj = new Request;
        } else if (Request::isResponse($decoded)) {
            $obj = new Response;
        }
        foreach (get_object_vars($decoded) as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    public function __toString(): string
    {
        return json_encode($this);
    }
}
