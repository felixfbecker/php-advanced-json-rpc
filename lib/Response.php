<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc;

use JsonSerializable;

/**
 * When a rpc call is made, the Server MUST reply with a Response, except for in the case of Notifications. The Response
 * is expressed as a single JSON Object, with the following members:
 */
class Response extends Message implements JsonSerializable
{
    /**
     * This member is REQUIRED. It MUST be the same as the value of the id member in the Request Object. If there was an
     * error in detecting the id in the Request object (e.g. Parse error/Invalid Request), it MUST be Null.
     *
     * @var int|string
     */
    public $id;

    /**
     * This member is REQUIRED on success. This member MUST NOT exist if there was an error invoking the method. The
     * value of this member is determined by the method invoked on the Server.
     *
     * @var mixed
     */
    public $result;

    /**
     * This member is REQUIRED on error. This member MUST NOT exist if there was no error triggered during invocation.
     * The value for this member MUST be an Object as defined in section 5.1.
     *
     * @var ResponseError|null
     */
    public $error;

    /**
     * A message is considered a Response if it has an ID and either a result or an error
     *
     * @param object $msg A decoded message body
     * @return bool
     */
    public static function isResponse($msg): bool
    {
        return is_object($msg) && isset($msg->id) && (isset($msg->result) || isset($msg->error));
    }

    /**
     * @param int|string $id
     * @param mixed $result
     * @param ResponseError $error
     */
    public function __construct($id, $result, ResponseError $error = null)
    {
        $this->id = $id;
        $this->result = $result;
        $this->error = $error;
    }

    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();
        $json['id'] = $this->id;
        if (isset($this->error)) {
            $json['error'] = $this->error;
        } else {
            $json['result'] = $this->result;
        }
        return $json;
    }
}
