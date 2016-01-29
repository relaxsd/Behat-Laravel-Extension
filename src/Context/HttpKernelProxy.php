<?php namespace Laracasts\Behat\Context;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class HttpKernelProxy.
 * 
 * Proxy object that wraps a Lumen Application and implements HttpKernelInterface like it used to 
 * (The interface, but not the handle() method, was removed from Lumen 5.2)
 */
class HttpKernelProxy implements HttpKernelInterface
{

    /**
     * @var
     */
    private $object;

    function __construct($object) {
        $this->object = $object;
    }

    function __call($method, $args) {
        return call_user_func_array(array($this->object, $method), $args);
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int $type The type of the request
     *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->object->handle($request, $type, $catch);
    }
}