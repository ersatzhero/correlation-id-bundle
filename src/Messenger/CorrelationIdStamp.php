<?php namespace Ersatzhero\CorrelationIdBundle\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

class CorrelationIdStamp implements StampInterface
{
    public function __construct(public readonly string $correlationId)
    {
    }
}