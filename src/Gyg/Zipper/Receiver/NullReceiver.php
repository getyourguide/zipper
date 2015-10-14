<?php

namespace Gyg\Zipper\Receiver;


use Zipkin\Span;

class NullReceiver implements ReceiverInterface
{
	public function log($category, Span $span)
	{
		// Hasta la vista, baby
	}
}