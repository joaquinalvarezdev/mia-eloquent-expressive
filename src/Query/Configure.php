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
     * Desactivar orden para manejarlo manualmente
     * @var boolean
     */
    public $deactivateOrder = false;
    
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
    public function run($query)
    {
        // Configuramos los Wheres
        foreach($this->where as $where){
            if(array_key_exists('date', $where)){
                $query->whereRaw('DATE('.$where['key'].') = DATE(\'' . $where['value'] . '\')');
            }else if(array_key_exists('in', $where)){
                $query->whereIn($where['key'], $where['value']);
            }else if(array_key_exists('notin', $where)){
                $query->whereNotIn($where['key'], $where['value']);
            }else if(array_key_exists('like', $where)){
                $query->where($where['key'], 'like', '%'.$where['value'].'%');
            }else if(array_key_exists('between', $where)){
                $query->whereBetween($where['key'], [$where['from'], $where['to']]);
            }else{
                $query->where($where['key'], '=', $where['value']);
            }
        }
        // Configuramos orden
        if($this->hasOrder() && !$this->deactivateOrder){
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
     * Agregar un whereIn a la query
     * @param string $key
     * @param array $value
     */
    public function addWhereIn($key, $value)
    {
        $this->where[] = array('key' => $key, 'value' => $value, 'in' => true);
    }
    /**
     * Agregar un whereIn a la query
     * @param string $key
     * @param array $value
     */
    public function addWhereNotIn($key, $value)
    {
        $this->where[] = array('key' => $key, 'value' => $value, 'notin' => true);
    }
    /**
     * Agregar un whereIn a la query
     * @param string $key
     * @param array $value
     */
    public function addWhereBetween($key, $from, $to)
    {
        $this->where[] = array('key' => $key, 'from' => $from, 'to' => $to, 'between' => true);
    }
    /**
     * Elimina un where del listado
     * @param string $key
     */
    public function removeWhere($key)
    {
        unset($this->where[$key]);
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
     * 
     * @return array
     */
    public function getOrders()
    {
        return $this->order;
    }
    /**
     * 
     * @return array
     */
    public function getWheres()
    {
        return $this->where;
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
            $this->order[] = array('column' => $ord, 'direction' => $asc == 0 ? 'asc' : 'desc');
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
            }else if($count == 3 && $d[1] == 'notin'){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => explode(',', $d[2]));
            }else if($count == 3 && $d[1] == 'like'){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => $d[2]);
            }else if($count == 3 && $d[1] == 'date'){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => $d[2]);
            }else if($count == 4 && $d[1] == 'between'){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'from' => $d[2], 'to' => $d[3]);
            }else if($count == 3){
                $this->where[] = array('key' => $d[0], $d[1] => true, 'value' => $d[2]);
            }else{
                $this->where[] = array('key' => $d[0], 'value' => $d[1]);
            }
        }
        return true;
    }
}