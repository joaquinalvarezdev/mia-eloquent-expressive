<?php namespace Mobileia\Expressive\Database\Middleware;

/**
 * Description of CacheInterMiddleware
 *
 * @author matiascamiletti
 */
class CacheInterMiddleware extends \Mobileia\Expressive\Middleware\MiaBaseMiddleware
{
    /**
     *
     * @var string
     */
    protected $cacheKey = '';
    
    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
    }
    
    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // Verificar si existe en cache
        $item = \Mobileia\Expressive\Database\Repository\CacheInterRepository::get($this->cacheKey);
        if($item !== null){
            return new \Mobileia\Expressive\Diactoros\MiaJsonResponse($item);
        }
        // Seguimos el flujo
        return $handler->handle($request);
    }
}
