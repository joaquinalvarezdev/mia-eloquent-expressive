<?php namespace Mobileia\Expressive\Database\Query;

use \Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of Configure
 *
 * @author matiascamiletti
 */
class Configure 
{
    /**
     * Almacena el orden de los registros
     * @var array
     */
    protected $order = array();
    /**
     * Almacena el numero de pagina a obtener
     * @var int
     */
    protected $page = 1;
    /**
     * Almacena todos los wheres de la query
     * @var array
     */
    protected $where = array();
    /**
     * Almacena el campo de busqueda
     * @var string
     */
    protected $search = '';
    /**
     * Almacena el numero de registros a obtener
     * @var int
     */
    protected $limit = 50;
    
    /**
     * Constructor que permite enviar Handler y Request para obtener los parametros
     * @param \Mobileia\Expressive\Request\MiaRequestHandler $handler
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(
            \Mobileia\Expressive\Request\MiaRequestHandler $handler = null, 
            \Psr\Http\Message\ServerRequestInterface $request = null)
    {
        // Procesamos los parametros enviados en la petición
        if($handler != null && $request != null){
            $this->processParams($handler, $request);
        }
    }
    /**
     * Configura la query con los datos configurados
     * @param DB $query
     */
    public function run(DB $query)
    {
        // Configuramos los Wheres
        foreach($this->where as $where){
            if(array_key_exists('in', $where)){
                $query->whereIn($where['key'], $where['value']);
            }else{
                $query->where($where['key'], '=', $where['value']);
            }
        }
        // Configuramos orden
        if($this->hasOrder()){
            $query->orderBy($this->order[0]['column'], $this->order[0]['direction']);
        }
    }
    /**
     * Agregar un where a la query
     * @param string $key
     * @param mixed $value
     */
    public function addWhere($key, $value)
    {
        $this->where[] = array('key' => $key, 'value' => $value);
    }
    /**
     * Determina si la configuración tiene un orden para la Query
     * @return boolean
     */
    public function hasOrder()
    {
        if(count($this->order) > 0){
            return true;
        }
        return false;
    }
    /**
     * Obtiene numero de pagina
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }
    /**
     * Obtiene el campo de busqueda
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }
    /**
     * Obtiene el limite
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
    /**
     * Procesa los parametros enviados en la petición para incluirlos en la query
     * @param \Mobileia\Expressive\Request\MiaRequestHandler $handler
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    protected function processParams(
            \Mobileia\Expressive\Request\MiaRequestHandler $handler = null, 
            \Psr\Http\Message\ServerRequestInterface $request = null)
    {
        // Procesar orden de la Query
        $ord = $handler->getParam($request, 'ord', '');
        $asc = $handler->getParam($request, 'asc', 1);
        if($ord != ''){
            $this->order[] = array('column' => $ord, 'direction' => $asc == 1 ? 'asc' : 'desc');
        }
        // Procesar numero de pagina
        $this->page = $handler->getParam($request, 'page', 1);
        // Procesar Wheres
        $this->processWhere($handler->getParam($request, 'where', ''));
        // Procesar campo de busqueda
        $this->search = $handler->getParam($request, 'search', '');
        // Procesar campo limite
        $this->limit = $handler->getParam($request, 'limit', 50);
    }
    /**
     * Procesa los wheres enviados en la petición
     * @param string $where
     * @return boolean
     */
    protected function processWhere($where = '')
    {
        if($where == ''){
            return false;
        }
        $data = explode(';', $where);
        foreach($data as $w){
            $d = explode(':', $w);
            $count = count($d);
            if($count <= 1){
                continue;
            }else if($count == 3 && $d[1] == 'in'){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => explode(',', $d[2]));
            }else if($count == 3){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => $d[2]);
            }else{
                $this->where[] = array('key' => $d[0], 'value' => $d[1]);
            }
        }
        return true;
    }
}