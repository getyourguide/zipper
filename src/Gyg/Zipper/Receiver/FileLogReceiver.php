<?php

namespace Gyg\Zipper\Receiver;


use Zipkin\Span;

class FileLogReceiver implements ReceiverInterface
{
	const FORMAT_SHORT = 'short';
	const FORMAT_LONG = 'long';

	private $format;
	private $filePath;

	public function __construct($filePath, $format = self::FORMAT_SHORT)
	{
		$this->format = $format;
		$this->filePath = $filePath;
	}

	public function log($category, Span $span)
	{
		if ($this->format === self::FORMAT_SHORT) {
			$format = '[%s] [%s] [%s] [%s] [%s] [%s|%s|%s]';
			$timestamp = date('Y-m-d H:i:s');
			foreach ($span->annotations as $annotation) {
				$logline = sprintf(
					$format,
					$timestamp,
					$category,
					$annotation->host->service_name,
					$annotation->value,
					$span->name,
					$span->trace_id,
					$span->id,
					$span->parent_id
				);
				file_put_contents($this->filePath, $logline . PHP_EOL, FILE_APPEND);
			}
			foreach ($span->binary_annotations as $binaryAnnotation) {
				$logline = sprintf(
					$format,
					$timestamp,
					$category,
					$binaryAnnotation->host->service_name,
					$binaryAnnotation->key . ':' . $binaryAnnotation->value,
					$span->name,
					$span->trace_id,
					$span->id,
					$span->parent_id
				);
				file_put_contents($this->filePath, $logline . PHP_EOL, FILE_APPEND);
			}
		} else {
			$logline = sprintf('[%s] [%s] %s', date('Y-m-d H:i:s'), $category, json_encode($span));
			file_put_contents($this->filePath, $logline . PHP_EOL, FILE_APPEND);
		}
	}
}

