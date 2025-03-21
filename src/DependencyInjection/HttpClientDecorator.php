<?php

namespace ersatzhero\CorrelationIdBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

#[AsDecorator(decorates: HttpClientInterface::class)]
class HttpClientDecorator implements HttpClientInterface
{

    /**
     * Decorates {@link HttpClientInterface} to set a correlation id from the kernel request to each request of the {@link HttpClientInterface}.
     */
    public function __construct(
        #[AutowireDecorated] private readonly HttpClientInterface $httpClient,
        private readonly RequestStack $requestStack,
        #[Autowire(param: 'correlationId.headerName')] private readonly string $headerName,
        #[Autowire(param: 'correlationId.attributeName')] private readonly string $attributeName)
    {
    }


    /**
     * @inheritDoc
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $correlationId = $this->requestStack->getCurrentRequest()->attributes->get($this->attributeName);
        $options['headers'][] = $this->headerName.': '. $correlationId;
        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * @inheritDoc
     */
    public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->httpClient->stream($responses, $timeout);
    }

    /**
     * @inheritDoc
     */
    public function withOptions(array $options): static
    {
        $this->httpClient->withOptions($options);
        return $this;
    }
}
