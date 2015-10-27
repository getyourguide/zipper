<?php

namespace Gyg\Zipper\ThriftEventHandler;

use Gyg\Zipper\TraceHelper;
use RuntimeException;
use TClientEventHandler;
use Zipkin\AnnotationType;

class ThriftClientEventHandler extends TClientEventHandler
{
	protected $handlerContext = [];
	protected $traceHelper;
	protected $serviceName;

	public function __construct($serviceName, TraceHelper $traceHelper)
	{
		$this->traceHelper = $traceHelper;
		$this->serviceName = $serviceName;
	}

	public function preSend($fn_name, $args, $sequence_id)
	{
		$this->handlerContext[$fn_name . ':' . $sequence_id] = $this->traceHelper->createNextSpan($fn_name);
		$this->traceHelper->annotateClientSend($this->handlerContext[$fn_name . ':' . $sequence_id], $this->serviceName);
	}

	public function sendError($fn_name, $args, $sequence_id, $ex)
	{
		if (isset($this->handlerContext[$fn_name . ':' . $sequence_id])) {
			$this->traceHelper->annotateBinary(
				$this->handlerContext[$fn_name . ':' . $sequence_id],
				$this->serviceName, 'sendError', $ex->getMessage(),
				AnnotationType::STRING
			);
			//cleanup
			unset($this->handlerContext[$fn_name . ':' . $sequence_id]);
		} else {
			throw new RuntimeException('missing context');
		}
	}

	public function postRecv($fn_name, $ex_sequence_id, $result)
	{
		if ($this->handlerContext[$fn_name . ':' . $ex_sequence_id] !== null) {
			$this->traceHelper->annotateClientReceive(
				$this->handlerContext[$fn_name . ':' . $ex_sequence_id],
				$this->serviceName
			);
			//cleanup
			unset($this->handlerContext[$fn_name . ':' . $ex_sequence_id]);
		} else {
			throw new RuntimeException('missing context');
		}
	}

	public function recvException($fn_name, $ex_sequence_id, $exception)
	{
		if ($this->handlerContext[$fn_name . ':' . $ex_sequence_id] !== null) {
			$this->traceHelper->annotateBinary(
				$this->handlerContext[$fn_name . ':' . $ex_sequence_id],
				$this->serviceName, 'recvException', $exception->getMessage(),
				AnnotationType::STRING
			);
			//cleanup
			unset($this->handlerContext[$fn_name . ':' . $ex_sequence_id]);
		} else {
			throw new RuntimeException('missing context');
		}
	}

	public function recvError($fn_name, $ex_sequence_id, $exception)
	{
		if ($this->handlerContext[$fn_name . ':' . $ex_sequence_id] !== null) {
			$this->traceHelper->annotateBinary(
				$this->handlerContext[$fn_name . ':' . $ex_sequence_id],
				$this->serviceName, 'recvError', $exception->getMessage(),
				AnnotationType::STRING
			);
			//cleanup
			unset($this->handlerContext[$fn_name . ':' . $ex_sequence_id]);
		} else {
			throw new RuntimeException('missing context');
		}
	}
}