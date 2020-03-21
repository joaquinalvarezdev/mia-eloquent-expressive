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
    /**
     *
     * @var array
     */
    protected $withParams = [];
    
    public function __construct($cacheKey, $withParams = [])
    {
        $this->cacheKey = $cacheKey;
        $this->withParams = $withParams;
    }
    
    public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // Procesar parametros
        $this->processParams($request);
        // Verificar si existe en cache
        $item = \Mobileia\Expressive\Database\Repository\CacheInterRepository::get($this->cacheKey);
        if($item !== null){
            return new \Mobileia\Expressive\Diactoros\MiaJsonResponse($item);
        }
        // Seguimos el flujo
        return $handler->handle($request);
    }
    
    protected function processParams(\Psr\Http\Message\ServerRequestInterface $request)
    {
        if(count($this->withParams) == 0){
            return;
        }
        
        // Recorremos cada parametro
        foreach($this->withParams as $keyParam){
            // Obtenemos valor del parametro
            $paramVal = $this->getParam($request, $keyParam, '');
            // Agregamos este parametro a la key
            $this->cacheKey .= '_' . $paramVal;
        }
    }
}
