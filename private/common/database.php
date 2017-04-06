<?php
/**
 * Класс для работы с MySQL базой данных
 *
 */
class Database {
    private $link; // Дескриптор соединения с сервером MySql
    
    /**
     * Открывает соединение с базой данных
     * @return EBASE - в случае ошибки
     *         1 - вслучае успеха
     */
    function connect($arg_list)
    {
        $this->link = mysqli_connect($arg_list['host'],
                                $arg_list['user'],
                                $arg_list['pass'],
                                $arg_list['database'],
                                $arg_list['port']);
        if (!$this->link)
            return EBASE;
    
        mysqli_query($this->link, 'set character set utf8');
        mysqli_query($this->link, 'set names utf8');
        return 1;
    
    }

    function query($query)
    {
        $data = array();
        $row = array();
    
        $result = mysqli_query($this->link, $query);
        if ($result === TRUE)
            return 1;
        if ($result === FALSE)
            return ESQL;
    
        return mysqli_fetch_assoc($result);
    }

    
    /**
     * Выполняет запрос
     *@param $query - запрос
     *@return 1 - если запрос успешно
     *        ESQL - если запрос не выполнен
     *        data - возвращает ассоциативный массив как результат запроса
     */
    function query_list($query)
    {
        $data = array();
        $row = array();
        
        $result = mysqli_query($this->link, $query);
        if($result === TRUE)
            return 0;

        if($result === FALSE)
            return -ESQL;
            
        $id = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $id++;
            if (isset($row['id']))
                $id = $row['id'];

            $data[$id] = $row;
        }

        return $data;
    }
    
    
    /**
     * Добавляет запись в БД
     * @param $table_name - имя таблицы для добавления
     * @param $array - массив данных для добавления
     * @return ESQL - в случае неудач
     * @return $id - возвращает id вставленной записи
     */
    function insert($table_name, $arg_list)
    {
        $query = "INSERT INTO " . $table_name . " SET ";
        $separator = '';
        foreach ($arg_list as $field => $value) {
            if ($field == 'id')
                continue;
            $query .= $separator . '`' .  $field . '`  = "' . $value . '"';
            $separator = ',';
        }
        $result = mysqli_query($this->link, $query);
        if ($result === FALSE)
            return ESQL;
        else
            return mysqli_insert_id($this->link);
    }
    
    
    /**
     * Обновляет данные в БД с указанным id
     * @param $table - имя таблицы для обновления
     * @param $id - id записи для обновления
     * @param $array - массив данных для обновления
     * @return ESQL - в случае неудач
     *         0 - в случае удачного обновления
     */
    function update($table, $id, $arg_list)
    {
        $separator = '';
        $query = "UPDATE " . $table . " SET ";
        foreach ($arg_list as $field     => $value) {
            $query .= $separator . '`' .  $field . '` = "' . $value . '"';
            $separator = ',';
        }
        $query .= " WHERE id = " . $id;
        $update = mysqli_query($this->link, $query);
        dump($query);
        if ($update)
            return 0;
        else
            return ESQL;
    
    }
    
    
    /**
     * Закрывает ранее открытое соединение с базой данных
     * @return EBASE - в случае ошибки
     * @return 1 - в случае успеха
     */
    function close()
    {
        if (!mysqli_close($this->link))
            return EBASE;
        else
            return 1;
    }
}

/* Создает и возвращает объект класса Database */
function db()
{
    static $database = NULL;
    if (!$database) 
        $database = new Database;  
    return $database;
}