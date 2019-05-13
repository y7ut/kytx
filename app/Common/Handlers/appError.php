<?php

declare(strict_types=1);
/**
 * 一些dependence 的处理类
 */

namespace App\Common\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * 处理app 错误的类，会返回一个500错误
 */
final class appError
{
    /** @var bool 是否展示错误 */
    private $displayError = false;

    /** @var LoggerInterface 满足PSR-3 的log 对象 */
    private $logger = null;

    /**
     * 构造函数
     *
     * @param LoggerInterface $logger       用于记录错误的日志对象
     * @param bool            $displayError 是否将错误显示出来，此选项一般在测试环境设置为true
     */
    public function __construct(LoggerInterface $logger, $displayError = false)
    {
        $this->logger = $logger;
        $this->displayError = empty($displayError) ? false : true;
    }

    /**
     * Invoke error handler
     *
     * @param ServerRequestInterface $request   The most recent Request object
     * @param ResponseInterface      $response  The most recent Response object
     * @param \Exception             $exception The caught Exception object
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Exception $exception)
    {
        $err_msg = $this->renderJsonErrorMessage($exception);
        $this->logger->critical($err_msg);
        if ($this->displayError) {
            $result = $err_msg;
        } else {
            $result = 'Opps!Something went wrong';
        }

        return $response
             ->withStatus(500)
             ->withHeader('Content-type', 'application/json')
             ->write($result);
    }

    /**
     * Render JSON error
     *
     * @param \Exception $exception
     *
     * @return string
     */
    private function renderJsonErrorMessage(\Exception $exception)
    {
        $error = [
          'message' => 'Application Error',
    ];

        $error['exception'] = [];

        do {
            $error['exception'][] = [
         'type' => get_class($exception),
         'code' => $exception->getCode(),
         'message' => $exception->getMessage(),
         'file' => $exception->getFile(),
         'line' => $exception->getLine(),
         'trace' => explode("\n", $exception->getTraceAsString()),
      ];
        } while ($exception = $exception->getPrevious());

        return json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
