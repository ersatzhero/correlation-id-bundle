<?php

namespace ersatzhero\CorrelationIdBundle\Messenger;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class Middleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        #[Autowire(param: 'correlationId.attributeName')] private readonly string $attributeName)
    {
    }


    /**
     * @inheritDoc
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $correlationIdStamp = $envelope->last(CorrelationIdStamp::class);

        if ($correlationIdStamp == null) {
            $correlationId = $this->requestStack->getCurrentRequest()->attributes->get($this->attributeName);
            $envelope->with(new CorrelationIdStamp($correlationId));
        } else {
            $correlationId = $correlationIdStamp->correlationId;
        }

        $oldCorrelationId = $this->requestStack->getCurrentRequest()->attributes->get($this->attributeName);

        $this->requestStack->getCurrentRequest()->attributes->set($this->attributeName, $correlationId);
        try {
            $handled = $stack->handle($envelope, $stack);
        } finally {
            $this->requestStack->getCurrentRequest()->attributes->set($this->attributeName, $oldCorrelationId);
        }

        return $handled;
    }
}
