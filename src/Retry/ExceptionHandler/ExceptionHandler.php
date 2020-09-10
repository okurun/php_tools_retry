<?php
namespace Okurun\Retry\ExceptionHandler;

class ExceptionHandler implements ExceptionHandlerInterface
{
    private $ignoreExceptionClass;
    private $usleep;

    public function __construct($ignoreExceptionClass = \Throwable::class, $usleep = 1000)
    {
        $this->ignoreExceptionClass = $ignoreExceptionClass;
        $this->usleep = $usleep;
    }

    public function handle(\Throwable $e, int $retryCount, array $args)
    {
        if ($e instanceof $this->ignoreExceptionClass) {
            usleep($this->usleep);
            return;
        }
        throw $e;
    }
}