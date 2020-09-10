<?php
namespace Okurun;

use Okurun\Retry\ExceptionHandler\ExceptionHandlerInterface;
use Okurun\Retry\Exception\RetryLimitOverException;

class Retry
{
    private $exceptionHandler;
    private $retryLimit;

    public function __construct(ExceptionHandlerInterface $exceptionHandler, int $retryLimit = 2)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->retryLimit = $retryLimit;
    }

    public function execute(callable $func, array $args = [])
    {
        $retryCount = 0;
        while ($retryCount <= $this->retryLimit) {
            try {
                return call_user_func_array($func, $args);
            } catch (\Throwable $e) {
                $this->exceptionHandler->handle($e, $retryCount++, $args);
            }
        }
        throw new RetryLimitOverException('The number of retries has exceeded the upper limit.');
    }
}