<?php

namespace Gyg\Zipper\Receiver;


use Zipkin\Span;

interface ReceiverInterface
{
	public function log($category, Span $span);
}