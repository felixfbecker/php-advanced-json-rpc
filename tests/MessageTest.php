<?php
declare(strict_types = 1);

namespace AdvancedJsonRpc\Tests;

use StdClass;
use PHPUnit\Framework\TestCase;
use AdvancedJsonRpc\{Message, Request, Response, SuccessResponse, ErrorResponse, Notification};

class MessageTest extends TestCase
{
    public function testParseRequest()
    {
        $msg = Message::parse('{"jsonrpc": "2.0", "method": "subtract", "params": [42, 23], "id": 1}');
        $this->assertInstanceOf(Request::class, $msg);
        $this->assertEquals([
            'jsonrpc' => '2.0',
            'method' => 'subtract',
            'params' => [42, 23],
            'id' => 1
        ], get_object_vars($msg));
    }

    public function testParseNotification()
    {
        $msg = Message::parse('{"jsonrpc": "2.0", "method": "update", "params": [1,2,3,4,5]}');
        $this->assertInstanceOf(Notification::class, $msg);
        $this->assertEquals([
            'jsonrpc' => '2.0',
            'method' => 'update',
            'params' => [1,2,3,4,5]
        ], get_object_vars($msg));
    }

    public function testParseSuccessResponse()
    {
        $msg = Message::parse('{"jsonrpc": "2.0", "result": 19, "id": 1}');
        $this->assertInstanceOf(SuccessResponse::class, $msg);
        $this->assertEquals([
            'jsonrpc' => '2.0',
            'result' => 19,
            'id' => 1
        ], get_object_vars($msg));
    }

    public function testParseErrorResponse()
    {
        $msg = Message::parse('{"jsonrpc": "2.0", "error": {"code": -32602, "message": "invalid params"}, "id": 1}');
        $this->assertInstanceOf(ErrorResponse::class, $msg);
        $this->assertEquals([
            'jsonrpc' => '2.0',
            'error' => [
                'code' => -32602,
                'message' => 'invalid params',
                'data' => null
            ],
            'id' => 1
        ], json_decode(json_encode($msg), true));
    }
}
