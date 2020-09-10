<?php
namespace Okurun\Retry\ExceptionHandler;

interface ExceptionHandlerInterface
{
    function handle(\Exception $e, int $retryCount, array $args);
}