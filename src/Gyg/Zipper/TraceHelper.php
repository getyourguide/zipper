<?php

namespace Gyg\Zipper;


use Gyg\Zipper\Receiver\ReceiverInterface;
use Zipkin\Annotation;
use Zipkin\BinaryAnnotation;
use Zipkin\Endpoint;
use Zipkin\Span;
use Zipkin\zipkinCore_CONSTANTS;


class TraceHelper implements TraceHelperInterface
{
	private $autoLog;

	private $logCategory;

	private $receivers = [];

	private $currentSpan;


	public function __construct(array $receivers, $autoLog = true, $logCategory = 'Zipkin')
	{
		foreach ($receivers as $receiver) {
			$this->addReceiver($receiver);
		}

		$this->autoLog = $autoLog;
		$this->logCategory = $logCategory;
	}

	protected function addReceiver(ReceiverInterface $receiver)
	{
		$this->receivers[] = $receiver;
	}

	public function setCurrentSpan(Span $span)
	{
		return $this->currentSpan = $span;
	}

	public function getCurrentSpan()
	{
		return $this->currentSpan;
	}

	public function createNextSpan($name, $debug = false)
	{
		if ($this->currentSpan === null) {
			$this->currentSpan = $this->createRootSpan($name, null, $debug);
		} else {
			$this->currentSpan = $this->createChildSpan($name, $this->currentSpan, $debug);
		}

		return $this->currentSpan;
	}


	public function createRootSpan($name, $traceId = null, $debug = false)
	{
		$span = new Span();

		if ($traceId === null) {
			$span->trace_id = $this->getRandomId();
		} else {
			$span->trace_id = $traceId;
		}

		$span->id = $span->trace_id;
		$span->name = $name;
		$span->debug = $debug;
		$span->annotations = [];
		$span->binary_annotations = [];

		return $span;
	}

	public function createChildSpan($name, Span $parentSpan, $debug = false)
	{
		$span = new Span();
		$span->trace_id = $parentSpan->trace_id;
		$span->id = $this->getRandomId();
		$span->parent_id = $parentSpan->id;
		$span->name = $name;
		$span->debug = $debug;
		$span->annotations = [];
		$span->binary_annotations = [];

		return $span;
	}
	
	public function annotateClientSend(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::CLIENT_SEND);
	}

	public function annotateClientReceive(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::CLIENT_RECV);
	}

	public function annotateServerSend(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::SERVER_SEND);
	}

	public function annotateServerReceive(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::SERVER_RECV);
	}

	public function annotateWireSend(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::WIRE_SEND);
	}

	public function annotateWireReceive(Span $span, $serviceName)
	{
		$this->annotate($span, $serviceName, zipkinCore_CONSTANTS::WIRE_RECV);
	}

	public function annotateBinary(Span $span, $serviceName, $key, $value, $type)
	{
		$binaryAnnotation = new BinaryAnnotation();
		$binaryAnnotation->key = $key;
		$binaryAnnotation->value = $value;
		$binaryAnnotation->annotation_type = $type;
		$binaryAnnotation->host = $this->getEndpoint($serviceName);
		$span->binary_annotations[] = $binaryAnnotation;

		if ($this->autoLog) {
			$this->log($span);
			//reset annotation areas to avoid dupe logging
			$span->annotations = [];
			$span->binary_annotations = [];
		}
	}

	private function annotate(Span $span, $serviceName, $value)
	{
		$annotation = new Annotation();
		$annotation->timestamp = round(microtime(true) * 1000 * 1000);
		$annotation->value = $value;
		$annotation->host = $this->getEndpoint($serviceName);
		$span->annotations[] = $annotation;

		if ($this->autoLog) {
			$this->log($span);
			//reset annotation areas to avoid dupe logging
			$span->annotations = [];
			$span->binary_annotations = [];
		}
	}

	public function log(Span $span)
	{
		if (empty($this->receivers)) {
			throw new TraceHelperException('one or more receiver must be specified');
		}
		foreach ($this->receivers as $receiver) {
			$receiver->log($this->logCategory, $span);
		}
	}


	private function getEndpoint($serviceName)
	{
		$host = new Endpoint();

		if (empty($_SERVER['SERVER_ADDR'])) {
			$ip = gethostbyname(gethostname());
		} else {
			$ip = $_SERVER['SERVER_ADDR'];
		}
		$host->ipv4 = ip2long($ip);

		if (!empty($_SERVER['SERVER_PORT'])) {
			$host->port = $_SERVER['SERVER_PORT'];
		}

		$host->service_name = $serviceName;

		return $host;
	}

	private static function getRandomId()
	{
		//9223372036854775807 max allowed value (64bit) the below default mt_getmaxrand() returns well less than that
		return dechex(mt_rand());
	}

}