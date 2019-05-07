<?php
declare(strict_types=1);
/**
 * 一些dependence 的处理类
 * @package  App\Common\Handlers
 */
namespace App\Common\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * 处理api 错误的类，会返回一个500错误
 */
final class phpError {

  /**@var bool 是否展示错误*/
  protected $displayError = false;

  /**@var LoggerInterface 满足PSR-3 的log 对象*/
  protected $logger = null;

  /**
   * 构造函数
   * @param  LoggerInterface $logger 用于记录错误的日志对象
   * @param  bool $displayError 是否将错误显示出来，此选项一般在测试环境设置为true
   */
  public function __construct(LoggerInterface $logger , $displayError=false) {
      $this->logger= $logger;
      $this->displayError= empty($displayError) ? false : true;
  }

  /**
   * Invoke error handler
   *
   * @param ServerRequestInterface $request   The most recent Request object
   * @param ResponseInterface      $response  The most recent Response object
   * @param \Throwable             $error     The caught Throwable object
   *
   * @return ResponseInterface
   */
  public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $error){
    $err_msg = $this->renderJsonErrorMessage($error);
    $this->logger->critical($err_msg);
    if ($this->displayError) {
      $result = $err_msg;
    }
    else {
      $result = 'Opps!Something went wrong';
    }
    return $response
             ->withStatus(500)
             ->withHeader('Content-type','text/html')
             ->write($result);
  }

  /**
   * Render JSON error
   *
   * @param \Throwable $error
   *
   * @return string
   */
  protected function renderJsonErrorMessage(\Throwable $error) {
    $json = [
        'message' => 'php Error',
    ];

    do {
        $json['error'][] = [
          'type' => get_class($error),
          'code' => $error->getCode(),
          'message' => $error->getMessage(),
          'file' => $error->getFile(),
          'line' => $error->getLine(),
          'trace' => explode("\n", $error->getTraceAsString()),
        ];
    } while ($error = $error->getPrevious());

    return json_encode($json);
  }
}
