<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc;

/**
 * When a rpc call is made, the Server MUST reply with a Response, except for in the case of Notifications. The Response
 * is expressed as a single JSON Object, with the following members:
 */
class Response extends Message
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
     * @param int|string $id
     * @param mixed $result
     * @param ResponseError $error
     */
    public function __construct($id, $result, ResponseError $error = null)
    {
        $this->result = $result;
        $this->error = $error;
    }
}
