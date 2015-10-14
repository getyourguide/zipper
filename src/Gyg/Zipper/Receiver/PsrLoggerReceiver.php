<?php

namespace Gyg\Zipper\Receiver;


use Zipkin\Span;

class PsrLoggerReceiver implements ReceiverInterface
{
	private $logger;
	private $logLevel;

	public function __construct($logger, $logLevel)
	{
		$this->logger = $logger;
		$this->logLevel = $logLevel;
	}

	public function log($category, Span $span)
	{
		$context = json_decode(json_encode($span), true);
		$this->logger->log($this->logLevel, $category, $context);
	}
}