<?php

namespace Gyg\Zipper\ThriftEventHandler;

use Gyg\Zipper\TraceHelper;
use TProcessorEventHandler;
use Zipkin\AnnotationType;

class ThriftProcessorEventHandler extends TProcessorEventHandler
{
	protected $traceHelper;
	protected $serviceName;

	public function __construct($serviceName, TraceHelper $traceHelper)
	{
		$this->traceHelper = $traceHelper;
		$this->serviceName = $serviceName;
	}

	public function getHandlerContext($fn_name)
	{
		return $this->traceHelper->createNextSpan($fn_name);
	}

	public function preRead($handler_context, $fn_name, $args)
	{
		$this->traceHelper->annotateServerReceive($handler_context, $this->serviceName);
	}

	public function postWrite($handler_context, $fn_name, $result)
	{
		$this->traceHelper->annotateServerSend($handler_context, $this->serviceName);
	}

	public function handlerError($handler_context, $fn_name, $exception)
	{
		$this->traceHelper->annotateBinary($handler_context, $this->serviceName, 'error', $exception->getMessage(), AnnotationType::STRING);
	}

	public function handlerException($handler_context, $fn_name, $exception)
	{
		$this->traceHelper->annotateBinary($handler_context, $this->serviceName, 'exception', $exception->getMessage(), AnnotationType::STRING);
	}
}