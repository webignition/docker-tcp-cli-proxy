<?php

declare(strict_types=1);

namespace webignition\TcpCliProxyServer\Services;

use webignition\ErrorHandler\ExceptionExaminer\ExceptionExaminerInterface;

class ExceptionExaminer implements ExceptionExaminerInterface
{
    public function isFatal(\Exception $exception): bool
    {
        if ($this->isRecoverable($exception)) {
            return false;
        }

        if ($this->isIgnored($exception)) {
            return false;
        }

        return true;
    }

    public function isRecoverable(\Exception $exception): bool
    {
        $exceptionMessage = $exception->getMessage();

        if ($this->isStreamSocketAcceptSystemCallException($exceptionMessage)) {
            return true;
        }

        return false;
    }

    public function isIgnored(\Exception $exception): bool
    {
        $exceptionMessage = $exception->getMessage();

        if ($this->isFWriteBrokenPipeException($exceptionMessage)) {
            return true;
        }

        return false;
    }

    private function isStreamSocketAcceptSystemCallException(string $message): bool
    {
        return preg_match('/^stream_socket_accept\(\):.*Interrupted system call$/', $message) > 0;
    }

    private function isFWriteBrokenPipeException(string $message): bool
    {
        return preg_match('/^fwrite\(\):.*Broken pipe$/', $message) > 0;
    }
}
