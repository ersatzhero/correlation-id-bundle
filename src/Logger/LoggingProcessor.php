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
        #[Autowire(param: 'correlation_id.attributeName')] private readonly string $attributeName,
        #[Autowire(param: 'correlation_id.logAttributeName')] private readonly string $logAttributeName)
    {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        if ($this->requestStack->getCurrentRequest() == null) {
          return $record;
        }
        $record->extra[$this->logAttributeName] = $this->requestStack->getCurrentRequest()->attributes->get($this->attributeName);
        return $record;
    }
}