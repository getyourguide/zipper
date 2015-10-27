<?php

namespace Gyg\Zipper\ThriftEventHandler;

use RuntimeException;
use TClientEventHandler;

class TraceClientEventHandler extends ErrorTraceClientEventHandler
{
	public function preSend($fn_name, $args, $sequence_id)
	{
		$this->handlerContext[$fn_name . ':' . $sequence_id] = $this->traceHelper->createNextSpan($fn_name);
		$this->traceHelper->annotateClientSend($this->handlerContext[$fn_name . ':' . $sequence_id], $this->serviceName);
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
}