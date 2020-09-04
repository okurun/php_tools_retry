<?php
namespace "Okurun";

class Retry
{
	private $retryNum;
	private $onException;

	public function __construct(callable $onException, $retryNum = 2)
	{
		$this->onException = $onException;
		$this->retryNum = $retryNum;
	}

	public function execute(callable $func)
	{
		$retry = 0;
		while ($retry <= $this->retryNum) {
			try {
				return call_user_func($func);
			} catch (\Throwable $e) {
				call_user_func_array($this->onException, [$e]);
				++$retry;
			}
		}
	}

}