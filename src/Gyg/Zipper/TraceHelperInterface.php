<?php

namespace Gyg\Zipper;


use Zipkin\Span;

interface TraceHelperInterface
{
	public function setCurrentSpan(Span $span);

	public function getCurrentSpan();

	public function createNextSpan($name, $debug = false);

	public function createRootSpan($name, $traceId = null, $debug = false);

	public function createChildSpan($name, Span $parentSpan, $debug = false);

	public function annotateClientSend(Span $span, $serviceName);

	public function annotateClientReceive(Span $span, $serviceName);

	public function annotateServerSend(Span $span, $serviceName);

	public function annotateServerReceive(Span $span, $serviceName);

	public function annotateWireSend(Span $span, $serviceName);

	public function annotateWireReceive(Span $span, $serviceName);

	public function annotateBinary(Span $span, $serviceName, $key, $value, $type);

	public function log(Span $span);
}