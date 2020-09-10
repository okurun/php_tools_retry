<?php
require_once __DIR__ . '/TestException.php';

use PHPUnit\Framework\TestCase;
use Okurun\Retry;
use Okurun\Retry\ExceptionHandler\ExceptionHandler;
use Okurun\Retry\Exception\RetryLimitOverException;

class RetryTest extends TestCase
{
    /**
     * @test
     */
    public function success()
    {
        $a = 5;
        $retry = new Retry(new ExceptionHandler());

        $result = $retry->execute(function ($a) {
            return $a * 2;
        }, [$a]);

        $this->assertSame(10, $result);
    }

    /**
     * @test
     */
    public function failed()
    {
        $execCount = 0;
        $retry = new Retry(new ExceptionHandler());

        try {
            $retry->execute(function () use (&$execCount) {
                $execCount++;
                throw new \Exception('failed');
            });
        } catch (RetryLimitOverException $e) {
            $this->assertSame(3, $execCount);
            return;
        }

        $this->fail('Should not pass here');
    }

    /**
     * @test
     */
    public function failed_once_but_succeeded_next()
    {
        $execCount = 0;
        $a = 5;
        $retry = new Retry(new ExceptionHandler());

        $result = $retry->execute(function ($a) use (&$execCount) {
            if ($execCount++ === 0) {
                throw new \Exception('failed');
            }

            return $a * 2;
        }, [$a]);

        $this->assertSame(10, $result);
        $this->assertSame(2, $execCount);
    }

    /**
     * @test
     */
    public function failed_change_the_number_of_retries()
    {
        $execCount = 0;
        $retry = new Retry(new ExceptionHandler(), 5);

        try {
            $retry->execute(function () use (&$execCount) {
                $execCount++;
                throw new \Exception('failed');
            });
        } catch (RetryLimitOverException $e) {
            $this->assertSame(6, $execCount);
            return;
        }

        $this->fail('Should not pass here');
    }

    /**
     * @test
     */
    public function failed_not_ignore_exception()
    {
        $execCount = 0;
        $retry = new Retry(new ExceptionHandler(TestException::class), 5);

        try {
            $retry->execute(function () use (&$execCount) {
                $execCount++;
                throw new \Exception('failed');
            });
        } catch (\Exception $e) {
            $this->assertSame(1, $execCount);
            return;
        }

        $this->fail('Should not pass here');
    }
}
