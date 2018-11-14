<?php

namespace App\Core;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

trait LoggerTrait
{
    /** @var LoggerInterface */
    protected $logger;

    /** @required */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    // Copied from Symfony KernelExceptionListener
    protected function logException(\Exception $exception, $message)
    {
        if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
            $this->logger->critical($message, array('exception' => $exception));
        } else {
            $this->logger->error($message, array('exception' => $exception));
        }
    }
}