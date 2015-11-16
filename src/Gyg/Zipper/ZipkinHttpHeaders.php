<?php

namespace Gyg\Zipper;


use Zipkin\Span;

class ZipkinHttpHeaders {

    const TRACE_ID = 'X-B3-TraceId';
    const PARENT_SPAN_ID = 'X-B3-ParentSpanId';
    const SPAN_ID = 'X-B3-SpanId';
    const SAMPLED = 'X-B3-Sampled';
    const FLAGS = 'X-B3-Flags';

    private $traceId;
    private $parentSpanId;
    private $spanId;
    private $sampled;
    private $flags;


    public function __construct($traceId = null, $spanId = null, $parentSpanId = null, $sampled = true, $flags = null)
    {
        $this->traceId = $traceId;
        $this->spanId = $spanId;
        $this->parentSpanId = $parentSpanId;
        $this->sampled = $sampled;
        $this->flags = $flags;
    }

    public function getHeadersArray()
    {
        $headers = [];
        $headers[self::TRACE_ID] = $this->traceId;
        $headers[self::SPAN_ID] = $this->spanId;
        if ($this->parentSpanId !== null) {
            $headers[self::PARENT_SPAN_ID] = $this->parentSpanId;
        }
        if ($this->flags !== null) {
            $headers[self::$flags] = $this->flags;
        }
        $headers[self::PARENT_SPAN_ID] = $this->sampled ? 1 : 0;

        return $headers;
    }

    public function getSpan()
    {
        $span = new Span();
        $span->trace_id = $this->traceId;
        $span->id = $this->spanId;
        $span->parent_id = $this->parentSpanId;
        //what about flags etc...

        return $span;
    }

    public function populateByHeaders()
    {
        $this->populateByHeadersArray(getallheaders());
    }

    public function populateByHeadersArray(array $headers)
    {
        foreach ($headers as $name => $value) {
            switch ($name) {
                case self::TRACE_ID:
                    $this->traceId = $value;
                    break;
                case self::PARENT_SPAN_ID:
                    $this->parentSpanId = $value;
                    break;
                case self::SPAN_ID:
                    $this->spanId = $value;
                    break;
                case self::SAMPLED:
                    $this->sampled = ($value === '1');
                    break;
                case self::FLAGS:
                    $this->flags = (int)$value;
                    break;
                default:
                    //nop
            }
        }
    }

    public function isValid()
    {
        return $this->traceId !== null && $this->spanId !== null;
    }

    public function getTraceId()
    {
        return $this->traceId;
    }

    public function setTraceId($traceId)
    {
        return $this->traceId = $traceId;
    }

    public function getParentSpanId()
    {
        return $this->parentSpanId;
    }

    public function setParentSpanId($parentSpanId)
    {
        return $this->parentSpanId = $parentSpanId;
    }

    public function getSpanId()
    {
        return $this->spanId;
    }

    public function setSpanId($spanId)
    {
        return $this->spanId = $spanId;
    }

    public function getSampled()
    {
        return $this->sampled;
    }

    public function setSampled($sampled)
    {
        return $this->sampled = $sampled;
    }

    public function getFlags()
    {
        return $this->flags;
    }

    public function setFlags($flags)
    {
        return $this->flags = $flags;
    }

} 