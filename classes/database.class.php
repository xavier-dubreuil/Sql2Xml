<?php

class Database
{

    protected $dbhandler;
    protected $links;

    public function __construct($dbinfos, $links = array())
    {
        if (!method_exists($this, $dbinfos['dbdriver'].'Connect')) {
            throw new Exception('Unknow database driver: '.$dbinfos['dbdriver']);
        }
        $db->dsn = call_user_func(array($this, $dbinfos['dbdriver'].'Connect'), $dbinfos);
        
        $this->links = $links;
    }

    protected function mysqlConnect($dbinfos)
    {
        // Initialise dsn for PGSQL
        $dsn = 'mysql:';
        
        // Getting the host parameter
        if (empty($dbinfos['hostname'])) {
            throw new Exception('Host setting necessary for pgsql connection');
        }
        $dsn .= 'host='.$dbinfos['hostname'].';';
        
        // Getting the port parameter
        $port = empty($dbinfos['hostname']) ? '3306' : $dbinfos['hostname'];
        $dsn .= 'port='.$port.';';
        
        // Getting the database name parameter
        if (empty($dbinfos['database'])) {
            throw new Exception('Dabatase setting necessary for mysql connection');
        }
        $dsn .= 'dbname='.$dbinfos['database'].';';
        
        // Getting the username parameter
        if (empty($dbinfos['username'])) {
            throw new Exception('Username setting necessary for mysql connection');
        }
        $user = $dbinfos['username'];
        $pass = empty($dbinfos['password']) ? '' : $dbinfos['password'];
        
        try {
            $this->dbhandler = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            throw new Exception('Unable to connect to mysql database : '.$dbinfos['database'].' ('.$dbinfos['username'].'@'.$dbinfos['hostname'].':'.$dbinfos['dbport'].')');
        }
    }

    public function query($request)
    {
        $query = $this->dbhandler->query($request);
        return $query;
    }
    
    public function fetchQuery($statement)
    {
        return $query->fetch();
    }


    public function generateSelectQuery($fields, $filter = array())
    {
        $requestFields = array();
        $requestTables = array();
        $requestCloses = array();
        
        $request = 'SELECT ';
        foreach ($fields as $key => $value) {
            array_push($requestTables, array_shift(explode('.', $value)));
            array_push($requestFields, $value.' as "'.$value.'"');
        }
        $request .= implode(', ', $requestFields);
        $request .= ' FROM ';
        $requestTables =  array_values(array_unique($requestTables));
        $request .= implode(', ', $requestTables);
        if (count($requestTables) > 0) {
            for ($a = 0; $a < count($requestTables); $a++) {
                for ($b = ($a+1); $b < count($requestTables); $b++) {
                    if (!empty($this->links[$requestTables[$a]][$requestTables[$b]]) &&
                        !empty($this->links[$requestTables[$b]][$requestTables[$a]])) {
                            $requestCloses[] = $requestTables[$a].'.'.$this->links[$requestTables[$a]][$requestTables[$b]].' = '.$requestTables[$b].'.'.$this->links[$requestTables[$b]][$requestTables[$a]];
                        }
                }
            }
        }
        foreach ($filter as $key => $value) {
            $requestCloses[] = $key.' = '.$this->dbhandler->quote($value);
        }
        if (count($requestCloses) > 0) {
            $request .= ' WHERE ';
            $request .= implode(' AND ', $requestCloses);
        }

        return $request;
    }

}





?>
