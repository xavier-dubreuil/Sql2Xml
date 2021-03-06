<?php

class sql2xml
{
    protected $srcDOM;
    protected $destDOM;

    protected $dbfields;

    protected $objects;
    protected $values;

    protected $dbhandler;

    public function __construct()
    {
        $this->srcDOM  = new DOMDocument();
        $this->destDOM = new DOMDocument('1.0', 'UTF-8');

        $this->objects = array();
        $this->values = array();
    }
    
    public function transform($config, $filepath)
    {
        $this->dbfields = $config['fields'];
        
        $this->dbhandler = new Database($config['database'], $config['links']);

        $this->srcDOM->load($filepath);
        $this->generateTag($this->srcDOM->documentElement, $this->destDOM);
        return utf8_encode($this->destDOM->saveXML());
    }

    protected function generateExceptionXML($e)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $rootTag = $dom->createElement('error');
        $rootTag->appendChild($dom->createElement('code', $e->getCode()));
        $rootTag->appendChild($dom->createElement('message', $e->getMessage()));
        $rootTag->appendChild($dom->createElement('file', $e->getFile()));
        $rootTag->appendChild($dom->createElement('line', $e->getLine()));
        $dom->appendChild($rootTag);
        return $dom;
    }

    protected function generateTag($src, &$par)
    {
        $func = $src->tagName.'ActionTag';
        if (method_exists($this, $func)) {
            call_user_func_array(array($this,$func), array($src, $par));
        }
    }

    protected function parseChildsTags($src, &$tag)
    {
        foreach ($src->childNodes as $child) {
            if ($child instanceOf DOMElement) {
                $this->generateTag($child, $tag);
            }
        }
    }

    protected function tagActionTag($src, &$par)
    {
        $name = $src->attributes->getNamedItem('name')->value;
        $tag = $this->destDOM->createElement($name);
        $this->parseChildsTags($src, $tag);
        $par->appendChild($tag);
    }

    public function valueActionTag($src, &$par)
    {
        $par->appendChild($this->destDOM->createTextNode($this->getValue($src->attributes->getNamedItem('value')->value)));
    }

    public function attributeActionTag($src, &$par)
    {
        $att = $this->destDOM->createAttribute($src->attributes->getNamedItem('name')->value);
        $att->value = $this->getValue($src->attributes->getNamedItem('value')->value);
        $par->appendChild($att);
    }

    public function dbloopActionTag($src, &$par)
    {
        $name = $src->attributes->getNamedItem('name')->value;
        $tables = explode(';', $src->attributes->getNamedItem('tables')->value);
        $fields = array();
        foreach ($tables as $table) {
            if (!isset($this->dbfields[$table]) || !count($this->dbfields[$table])) {
                throw new Exception('No fields for table '.$table.'. Check database configuration', 1);
            }
            foreach ($this->dbfields[$table] as $field) {
                $fields[] = $table.'.'.$field;
            }
        }
        $filterlist = explode(';', $src->attributes->getNamedItem('filters')->value);
        $filters = array();
        foreach ($filterlist as $fl) {
            $tmp = explode('=', $fl);
            if (count($tmp) == 2) {
                $filters[$tmp[0]] = $this->getValue($tmp[1]);
            }
        }
        $request = $this->dbhandler->generateSelectQuery($fields, $filters);
        $query  = $this->dbhandler->query($request);
        while ($item = $query->fetch()) {
            $this->objects[$name] = $item;
            $this->parseChildsTags($src, $par);
            $this->objects[$name] = null;
            unset($this->objects[$name]);
        }
        $query = null;
        $request = null;
        unset($query, $request);
    }

    public function vloopActionTag($src, &$par)
    {
        $name      = $this->getValue($src->attributes->getNamedItem('name')->value);
        $value     = $this->getValue($src->attributes->getNamedItem('value')->value);
        $separator = $this->getValue($src->attributes->getNamedItem('separator')->value);

        $values = explode($separator, $this->getValue($value));
        foreach ($values as $item) {
            $this->values[$name] = $item;
            $this->parseChildsTags($src, $par);
            $this->values[$name] = null;
            unset($this->values[$name]);
        }
        $values = null;
    }

    public function ifActionTag($src, &$par)
    {
        $condition = $this->getValue($src->attributes->getNamedItem('condition')->value);
        $operator  = $this->getValue($src->attributes->getNamedItem('operator')->value);
        $value     = $this->getValue($src->attributes->getNamedItem('value')->value);
        $flg = false;
        if ($operator == '>' && $condition > $value) {
            $flg = true;
        } else if ($operator == '>=' && $condition >= $value) {
            $flg = true;
        } else if ($operator == '<' && $condition < $value) {
            $flg = true;
        } else if ($operator == '<=' && $condition <= $value) {
            $flg = true;
        } else if ($operator == '=' && $condition == $value) {
            $flg = true;
        } else if ($operator == '!=' && $condition != $value) {
            $flg = true;
        } else if ($operator == 'null' && is_null($condition)) {
            $flg = true;
        } else if ($operator == '!null' && !is_null($condition)) {
            $flg = true;
        }
        if ($flg) {
            $this->parseChildsTags($src, $par);
        }
    }

    protected function getValue($str)
    {
        $str = trim($str);
        $val = null;
        $pos = strpos($str, '(');
        $len = strlen($str);


        if ($pos > 0 && $str[$len-1] == ')') {
            // Getting function name
            $func = trim(substr($str, 0, $pos));
            $funa = explode('::', $func);
            $args = substr($str, $pos+1, $len-$pos-2);
            $arga = array();
            for ($a = 0, $p = 0, $f = 0; $a < strlen($args); $a++) {
                $p += $args[$a] != '(' ? $args[$a] == ')' ? -1 : 0 : 1;
                if  ($args[$a] == ',' && $p == 0) {
                    $arga[] = $this->getValue(substr($args, $f, $a-$f));
                    $f = $a+1;
                }
                if  ($a == (strlen($args)-1) && $p == 0) {
                    $arga[] = $this->getValue(substr($args, $f));
                }
            }
            if (count($funa) == 1 && function_exists($func) ||
                count($funa) == 2 && method_exists($funa[0], $funa[1])) {  
                $val = call_user_func_array($func, $arga);
            } else if (method_exists($this, $func)) {
                $val = call_user_func_array(array($this,$func), $arga);
            }
        } else {
            $val = $str;
        }
        return $val;
    }

    protected function dbvalue($object, $field)
    {
        if (isset($this->objects[$object][$field])) {
            return $this->objects[$object][$field];
        }
        return null;
    }

    protected function vvalue($object)
    {
        if (isset($this->values[$object])) {
            return $this->values[$object];
        }
        return null;
    }

    protected function concat()
    {
        $args = func_get_args();
        $res = "";
        foreach ($args as $arg) {
            $res .= $arg;
        }
        return $res;
    }

    protected function param($params)
    {
        global $param;

        $array = $param;
        foreach (explode('.', $params) as $item) {
            if (isset($array[$item])) {
                $array = $array[$item];
            }
        }
        if (is_string($array)) {
            return $array;
        }
        return null;
    }

    protected function rvalue($type, $param)
    {
        if ($type == 'GET') {
            if (isset($_GET[$param])) {
                return $_GET[$param];
            }
        } else if ($type == 'POST') {
            if (isset($_POST[$param])) {
                return $_POST[$param];
            }
        } else if ($type == 'REQUEST') {
            if (isset($_REQUEST[$param])) {
                return $_REQUEST[$param];
            }
        } else if ($type == 'SESSION') {
            if (isset($_SESSION[$param])) {
                return $_SESSION[$param];
            }
        }
        return null;
    }
    
    function contain($field, $contain)
    {
        if (strpos($field, $contain) !== false) {
            return true;
        }
        return false;
    }
    
    function boolTo($value, $type)
    {
        if ($value === true) {
            if ($type == 'string') {
                return 'true';
            }
            return 1;
        }
        if ($type == 'string') {
            return 'false';
        }
        return 0;
    }
}

?>
