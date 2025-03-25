<?php namespace Ersatzhero\CorrelationIdBundle\Logger;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
class LoggingProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        #[Autowire(param: 'correlationId.attributeName')] private readonly string $attributeName,
        #[Autowire(param: 'correlationId.logAttributeName')] private readonly string $logAttributeName)
    {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra[$this->logAttributeName] = $this->requestStack->getCurrentRequest()->attributes->get($this->attributeName);
        return $record;
    }
}