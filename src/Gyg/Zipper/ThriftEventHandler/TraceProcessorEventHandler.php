<?php

namespace Gyg\Zipper\ThriftEventHandler;

use TProcessorEventHandler;

class TraceProcessorEventHandler extends ErrorTraceProcessorEventHandler
{
	public function preRead($handler_context, $fn_name, $args)
	{
		$this->traceHelper->annotateServerReceive($handler_context, $this->serviceName);
	}

	public function postWrite($handler_context, $fn_name, $result)
	{
		$this->traceHelper->annotateServerSend($handler_context, $this->serviceName);
	}
}