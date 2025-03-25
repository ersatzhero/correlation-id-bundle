<?php namespace Ersatzhero\CorrelationIdBundle\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Uid\Uuid;

class KernelEventListener
{
    public function __construct(
        #[Autowire(param: 'correlation_id.headerName')] private readonly string $headerName,
        #[Autowire(param: 'correlation_id.attributeName')] private readonly string $attributeName)
    {
    }

    /**
     * Reads the correlation ID from the incoming request.
     * If no ID exists, one is generated.
     *
     * @param RequestEvent $event
     * @return void
     */
    #[AsEventListener(event: 'kernel.request', priority: 100)]
    public function onKernelRequest(RequestEvent $event): void {
        $request = $event->getRequest();
        if ($request->headers->has($this->headerName)) {
            $correlationId = $request->headers->get($this->headerName);
        } else {
            $correlationId = Uuid::v7()->toString();
        }
        $request->attributes->set($this->attributeName, $correlationId);
    }

    /**
     * Sets the correlation ID in the response.
     *
     * @param ResponseEvent $event
     * @return void
     */
    #[AsEventListener(event: 'kernel.response', priority: 100)]
    public function onKernelResponse(ResponseEvent $event): void {
        $event->getResponse()->headers->set($this->headerName, $event->getRequest()->attributes->get($this->attributeName));
    }
}
