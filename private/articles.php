<?php

/* Функции для работа со статьями */

require_once("common/base_sql.php"); //файл для работы с базой даных
$table_name = "articles";



/**
 * Получить ассоциативный массив с данными записи с идентификатором $id
 * @param $id идентификатор записи
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL некорретного sql запроса
 * @return массив [индентификатор, имя страницы, имя, содержание, публикование]
 */
function article_get_by_id($id)
{
    global $table_name;
    
	if (!is_numeric($id) || !isset($id))
        return EINVAL;
    
    $query = "SELECT * FROM ". $table_name .  " WHERE id = " . $id;
    $result = db_query($query);
    
    if ($result == FALSE)  // если бд вернула 0 строк 
        return ESQL;
    else 
        return $result[0];
}	

/**
 * Получить ассоциативный массив с данными записи с ключом $key
 * @param $key ключ записи
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL некорретного sql запроса
 * @return массив [индентификатор, имя страницы, имя, содержание, публикование]
 */
function article_get_by_key($key)
{
    global $table_name;
    
    if (!isset($key)) {
        dbg_err("Incorrect key"); 
        return EINVAL;
    }
    
    $query = "SELECT * FROM ". $table_name .  " WHERE `key` like \"" . $key .'"';
    $result = db_query($query);
    
    if ($result == FALSE)  // если бд вернула 0 строк 
        return ESQL;
    else 
        return $result[0];
}   



/**
 * добавляет статью в бд
 * @param $array_params - массив с данными
 * @param [page_title] - title страницы
 * @param [name] - название стать
 * @param [contents] - содержимое статьи
 * @param [public] - публикация статьи
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return id в случае успешного добавления
 */
function article_add_new($array_params)
{
    global $table_name;
    $data = array();

    /*  выбираем только нужные поля */
    $fields = array('page_title', 'key', 'name', 'public', 'contents');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
                
    if(empty($data["key"])) { 
        dbg_err("Not set key");    
        return EINVAL;    
    }
    if(empty($data["page_title"])) { 
        dbg_err("Not set page title");    
        return EINVAL;    
    }
    dump($data);
    if(empty($data["name"])) {
	    dbg_err("Not set name");    
	    return EINVAL;
    }
    
    if(empty($data["contents"])) {
	    dbg_err("Not set contents");    
	    return EINVAL;    
    }
    
    if(!isset($data["public"])) {
        dbg_err("Not set public");    
        return EINVAL;    
    }
     else 
        return db_insert($table_name, $data); 
    }




/**
 * редактирует статью с идентификатором $id
 * @param $id идентификатор редактируемой записи
 * @param $array_params - массив с данными для редактироания
 * @param [page_title] - title страницы
 * @param [name] - название стать
 * @param [contents] - содержимое статьи
 * @param [public] - публикация статьи
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return 0 в случае успешного редактирования
 */
function article_edit($id, $array_params)
{
    global $table_name;

    /*  выбираем только нужные поля */
    $fields = array('page_title', 'key', 'name', 'public', 'contents');
    foreach ($array_params as $key => $value)
        if (in_array($key, $fields))
            $data[$key] = $value;
            
    if (!is_numeric($id) || !isset($id)) {
        dbg_err("Incorrect id"); 
        return EINVAL;
    }
    
    if(empty($data["key"])) { 
        dbg_err("Not set key");    
        return EINVAL;    
    }
    if(empty($data["page_title"])) { 
        dbg_err("Not set page title");    
        return EINVAL;    
    }
    
    if(empty($data["name"])) {
        dbg_err("Not set name");    
        return EINVAL;
    }
    
    if(empty($data["contents"])) {
        dbg_err("Not set contents");    
        return EINVAL;    
    }
    
   if(!isset($data["public"])) {
        dbg_err("Not set public");    
        return EINVAL;    
    }
    
    if (article_get_by_id($id) <= 0) {
        dbg_err("Article not found"); 
        return EINVAL;
    }
    
    return db_update($table_name, $id, $data);
}


/**
 * удаляет запись с идентификатором $id
 * @param $id идентификатор записи которую нужно удалить
 * @return EINVAL в случае ошибки входных параметров
 * @return ESQL в случае некорретного sql запроса
 * @return 1 в случае успешного удаления записи
 */     

function article_del($id)
{
    global $table_name;
    
	if (!is_numeric($id) || !isset($id)) {
        dbg_err("Incorrect id"); 
        return EINVAL;
    }
      
    if (article_get_by_id($id) <= 0) {
        exit;
    }

    $query = "DELETE FROM ". $table_name . " WHERE id = " . $id;
    return db_query($query);
}

/**
 * Получает все статьи бд
 * @return ESQL в случае некорретного sql запроса
 *         массив в случае успешного выполнения запроса
 */

function article_get_list()
{
	global $table_name;
    
	$query = "SELECT * FROM ". $table_name ;
	return db_query($query);
}

?>