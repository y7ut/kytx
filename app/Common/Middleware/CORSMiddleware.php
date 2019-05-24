<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/24
 * Time: 14:01
 */

namespace App\Common\Middleware;


final class CORSMiddleware {
    /**
     * 调用
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next) {
        // 获取来源
        $origin = $request->getHeader('Origin');
        $response = $next($request, $response);

        // 如果存在referer 则允许其跨域
        if (count($origin)>0) {
            $response = $response
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Origin', $origin[0])
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization,ContentType,Referer,X-HTTP-Method-Override')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }
        return $response;
    }
}
