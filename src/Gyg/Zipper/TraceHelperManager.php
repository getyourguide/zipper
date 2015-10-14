<?php

namespace Gyg\Zipper;


class TraceHelperManager
{
	protected $traceHelper;

	public function setTraceHelper(TraceHelperInterface $traceHelper)
	{
		$this->traceHelper = $traceHelper;
	}

	public function getTraceHelper()
	{
		if ($this->traceHelper === null) {
			throw new TraceHelperManagerException();
		}

		return $this->traceHelper;
	}
}