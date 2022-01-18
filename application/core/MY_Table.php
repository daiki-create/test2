<?php
/**
 * MY_Table Class
 *
 * @package     Hairlogy
 * @category    Core Model
 * @author      yuki.hatano@gmail.com
 */

class MY_Table {

    protected $_CI;
    protected $_class;

    protected $_uri;
    protected $_login;

    protected $_db;
    protected $_table;
    protected $_records     = array();
    protected $_pagination  = FALSE;
    protected $_page_config = NULL;
    protected $_offset      = 0;
    protected $_limit       = NULL;
    protected $_total       = 0;
    protected $_has_prev    = FALSE;
    protected $_has_next    = FALSE;
    protected $_distinct    = FALSE;
    protected $_i;

    protected $_error       = array();

    // -------------------------------------------------------------------------------------------------------------------------------

    public function __construct()
    {
        $this->_CI =& get_instance();
        $this->_login = isset($this->_CI->_login) ? $this->_CI->_login : NULL;
        $this->_i = 0;
        $this->_class = get_class($this);
        $this->_table = strtolower(substr($this->_class, 0, strrpos($this->_class, '_')));

        $this->_uri   = $this->_CI->uri->uri_string();

        log_debug('MY_Table Class Initialized.');
        log_debug("{$this->_class} Class Initialized.");
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function get_error()
    {
        return $this->_error;
    }
    public function get_error_code()
    {
        return isset($this->_error['code']) ? $this->_error['code'] : NULL;
    }
    public function get_error_message()
    {
        return isset($this->_error['message']) ? $this->_error['message'] : NULL;
    }
    public function reset_error()
    {
        $this->_error = array();
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * Connecting DB
     *
     * @param   string|object   $db     # 'config/database.php'設定ファイルの $db['***'] DB指定文字列、または、DBコネクションObject
     * @param   bool            $force  # TRUE: 常に、新しい接続を行います。 FALSE: 既に接続済みであれば再利用します。
     */
    public function initialize($db='master', $force=FALSE)
    {

        //if ($force !== TRUE && $this->_db instanceof CI_DB) {
        if ($force !== TRUE && $db instanceof CI_DB) {
            $this->_db = $db;
            log_debug("DB Connection reused. [{$this->_db->conn_id->thread_id}] [{$this->_table}]");
        }
        else {

            if (is_null($db)) {
                $this->_db = $this->_CI->load->database('master', TRUE);
            }
            elseif (is_string($db) && strlen($db)) {
                $this->_db = $this->_CI->load->database($db, TRUE);
            }
            elseif ($db instanceof CI_DB) {
                $this->_db = $db;
            }
            else {
                $message = 'Load Database Error. Not $db instanceof CI_DB. [' . get_class($this) . ']';
                log_error($message);
                show_error($message);
                return FALSE;
            }
        }

        // Database Connection Error
        if ($this->_db->conn_id === FALSE) {
            $message = 'Load Database Error. [' . get_class($this) . ']';
            log_error($message);
            show_error($message);
            return FALSE;
        }

        log_info("Database Connection ID: {$this->_db->conn_id->thread_id} [{$this->_table}]");
        return $this->_db;

    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function init_pagination($offset, $limit='10', $base_url='', $uri_segment=4)
    {
        log_debug("init_pagination({$offset}, {$limit}, {$base_url}, {$uri_segment}) run.");
        $this->_offset = $offset;
        $this->_limit  = $limit;

        if (empty($base_url))
        {
            $_module  = strtolower(rtrim($this->_CI->router->fetch_directory(), '/'));
            $_class   = $this->_CI->router->fetch_class();
            $_action  = $this->_CI->router->fetch_method();
            $base_url = "/{$_module}/{$_class}/{$_action}";
        }

        $this->_pagination = TRUE;
        $this->_db->start_cache();

        $this->_CI->load->config('pagination');
        $this->_page_config = config_item('pagination');
        $this->_page_config['base_url']    = strpos($base_url, '/') > 0 ? "/{$base_url}" : $base_url;
        $this->_page_config['uri_segment'] = $uri_segment;
        $this->_page_config['per_page']    = $limit;
        $this->_page_config['use_page_numbers'] = FALSE;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function load_pagination()
    {
        log_debug("load_pagination() run.");

        if ($this->_pagination !== FALSE)
        {
            log_debug("total_rows = {$this->_total}");
            $this->_page_config['total_rows'] = $this->_total;
            //log_info($this->_page_config);
            $this->_CI->load->library('pagination', $this->_page_config);
            $count   = count($this->_records);
            $numbers = ($this->_offset + 1) . " - " . ($count + $this->_offset) . " of {$this->_total}";
            $pagination = array(
                'total'     => $this->_total,
                'offset'    => $this->_offset,
                'limit'     => $this->_limit,
                'has_prev'  => $this->_has_prev,
                'has_next'  => $this->_has_next,
                'link'      => $this->_CI->pagination->create_links(),
                'numbers'   => $numbers,
                'count'     => $count,
            );
            $this->_pagination  = FALSE;
            $this->_total       = 0;
            $this->_offset      = 0;
            $this->_limit       = 0;
            $this->_has_prev    = FALSE;
            $this->_has_next    = FALSE;
            return $pagination;
        }

        return '';
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * 特定カラムの値を取得
     *
     * @param   string  $column
     */
    public function find($where=NULL, $group_by=NULL, $order_by=NULL, $limit=NULL, $offset=NULL)
    {
        log_debug("MY_Table.find(where, group_by, order_by, {$limit}, {$offset}) run.");
        $this->_db->from($this->_table);

        if (is_array($where) && count($where) > 0)
        {
            $this->_db->where($where);
        }

        if (is_array($group_by) && count($group_by) > 0)
        {
            $this->_db->group_by($group_by);
        }

        if (is_numeric($limit))
        {
            $this->_limit = $limit;
        }

        if (is_numeric($offset))
        {
            $this->_offset = $offset;
        }

        if ($this->_pagination !== FALSE)
        {
            $this->_db->stop_cache();
            $this->_total = $this->_db->count_all_results($this->_table);
            log_debug($this->_db->last_query());
            log_debug("Offset: {$this->_offset}");
            log_debug("Limit: {$this->_limit}");
            log_debug("Total: {$this->_total}");

            if ($this->_offset > 0) {
                $this->_has_prev = TRUE;
            }
            else {
                $this->_has_prev = FALSE;
            }

            if ($this->_total > ($this->_offset + $this->_limit)) {
                $this->_has_next = TRUE;
            }
            else {
                $this->_has_next = FALSE;
            }

            $this->distinct($this->_distinct);
        }

        if (is_array($order_by) && count($order_by) > 0)
        {
            foreach ($order_by as $_order_by)
                $this->_db->order_by($_order_by);
        }
        elseif (is_string($order_by))
        {
            $this->_db->order_by($order_by);
        }

        if ( ! empty($this->_limit))
        {
            $this->_db->limit($this->_limit, $this->_offset);
        }
        if ($query = $this->_db->get()) {
            log_info($this->_db->last_query());
            $this->_records = $query->result_array();
        }

        $this->_i = 0;

        $this->_db->flush_cache();
        return $this->_records;

    }

    // -------------------------------------------------------------------------------------------------------------------------------
    /**
     * DISTINCT
     */
    public function distinct($distinct=TRUE)
    {
        $this->_distinct = is_bool($distinct) ? $distinct : TRUE;
        $this->_db->distinct($this->_distinct);
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * 特定カラムの値を取得
     *
     * @param   string  $column
     */
    public function get($column)
    {
        log_debug("MY_Table.get({$column}) run.");
        if (isset($this->_records[$this->_i][$column]))
        {
            return $this->_records[$this->_i][$column];
        }

        return NULL;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * 1レコード取得、取得後行番号をインクリメントします。
     *  次回に、本メソッドをコールすると次レコードが取得できます。
     *
     * @param   string  $column
     */
    public function get_row()
    {
        if (isset($this->_records[$this->_i]))
        {
            log_info("MY_Table.get_row() run. [{$this->_class}]");
            $row = $this->_records[$this->_i];
            $this->_i++;
            return $row;
        }
        return NULL;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * 検索結果の全レコードを取得します。
     *
     */
    public function get_records()
    {
        return $this->_records;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * LOCK TABLES
     */
    public function lock_tables($tables)
    {
        if (is_array($tables))
        {
            $sql = 'LOCK TABLES ';

            foreach ($tables as $table => $lock_type)
            {
                if (strtolower($lock_type) == 'read' OR strtolower($lock_type) == 'write')
                {
                    $table = $this->_db->protect_identifiers($table);
                    $sql .= "{$table} {$lock_type},";
                }
                else
                {
                    log_error('Invalid TABLE LOCK TYPE.');
                    return FALSE;
                }


            }
            $sql = substr($sql, 0, -1);
            log_debug($sql);
            return $this->_db->simple_query($sql);
        }

        return FALSE;
    }

    /**
     * UNLOCK TABLES
     */
    public function unlock_tables()
    {
        log_debug('UNLOCK TABLES');
        return $this->_db->simple_query('UNLOCK TABLES');
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * INSERT ... ON DUPLICATE KEY UPDATE
     */
    public function insert_update($data, $cond)
    {
        $insert = $this->insert_string($data);
        $valstr = array();
        foreach ($data as $key => $val)
        {
            if ( ! isset($cond[$key]))
                $valstr[] = $this->protect_identifiers($key).' = '.$this->escape($val);
        }

        $sql = "{$insert} ON DUPLICATE KEY UPDATE ".implode(', ', $valstr);
        log_debug($sql);

        if ($this->query($sql))
        {
            if ($id = $this->_db->insert_id())
            {
                return $id;
            }
            else
            {
                $this->_db->select('*');
                $this->_db->from($this->_table);
                $this->_db->where($cond);

                if ($query = $this->_db->get())
                {
                    $row = $query->row_array();

                    if (isset($row['id']))
                    {
                        return $row['id'];
                    }
                    else
                    {
                        $ret = [];

                        foreach ($cond as $key => $val)
                        {
                            $ret[$key] = $row[$key];
                        }

                        return $ret;
                    }
                }
            }

            return TRUE;
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * INSERT
     */
    public function insert($data, $duplicate_keys=NULL)
    {
        if ($this->_db->insert($this->_table, $data))
        {
            log_info('--- SQL ------------------------------------------------------------');
            log_info($this->_db->last_query());
            log_info('--------------------------------------------------------------------');
            $rows = $this->_db->affected_rows();
            log_info("[ {$rows} ] rows affected.");
            log_info('--------------------------------------------------------------------');
            if ($last_insert_id = $this->_db->insert_id())
            {
                log_info("last_insert_id: {$last_insert_id}");
                return $last_insert_id;
            }
            return TRUE;
        }
        log_info('--- SQL ------------------------------------------------------------');
        log_info($this->_db->last_query());
        log_info('--------------------------------------------------------------------');
        $rows = $this->_db->affected_rows();
        log_info("[ {$rows} ] rows affected.");
        log_info('--------------------------------------------------------------------');
        
        $this->_error = $this->_db->error();
        
        if ($this->_error['code'] == '1062')
        {
            if (is_array($duplicate_keys) && count($duplicate_keys))
            {
                $where = [];

                foreach ($duplicate_keys as $keys)
                {
                    if (is_string($keys))
                    {
                        $where[$keys] = $data[$keys];
                    }
                    elseif (is_array($keys))
                    {
                        foreach ($keys as $key) if (isset($data[$key]))
                        {
                            $where[$key] = $data[$key];
                        }
                    }
                }

                if (($records = $this->find($where)) && count($records) === 1)
                {
                    return isset($records[0]['id']) ? $records[0]['id'] : TRUE;
                }
            }
        }

        return FALSE;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    /**
     * WHERE
     */
    public function where($key, $value=NULL, $escape=NULL, $binary=NULL)
    {
        if (is_null($binary))
        {
            $this->_db->where($key, $value, $escape);
        }
        else
        {
            $value = $this->_db->escape($value);
            $this->_db->where($key, "BINARY {$value}", FALSE);
        }
    }
    public function or_where($key, $value=NULL, $escape=NULL, $binary=NULL)
    {
        if (is_null($binary))
        {
            $this->_db->or_where($key, $value, $escape);
        }
        else
        {
            $value = $this->_db->escape($value);
            $this->_db->or_where($key, "BINARY {$value}", FALSE);
        }
    }

    // -------------------------------------------------------------------------------------------------------------------------------
    // Magic Methods
    // -------------------------------------------------------------------------------------------------------------------------------

    public function __call($method, $args)
    {
        if (isset($this->_db) && $this->_db instanceof CI_DB)
        {
            log_info("{$method}() run.");
            $_is_last_query = FALSE;

            switch ($method)
            {
                //case "insert":
                case "update":
                case "replace":
                case "delete":
                case "insert_batch":
                case "update_batch":
                case "insert_string":
                case "update_string":
                case "get_compiled_select":
                case "get_compiled_insert":
                case "get_compiled_update":
                case "get_compiled_delete":
                case "count_all":
                case "count_all_results":
                case "field_data":
                    array_unshift($args, $this->_table);
                    $_is_last_query = TRUE;
                    break;
                case "trans_start":
                    log_debug("DB Transaction Started !");
                    break;
                case "trans_commit":
                    log_debug("DB Transaction Commited !");
                    break;
                case "trans_rollback":
                    log_debug("DB Transaction Rollbacked !");
                    break;
                case "trans_complete":
                    log_debug("DB Transaction Completed !");
                    break;
                case "escape":
                    return $this->_db->escape($args[0]);
                case "empty_table":
                case "truncate":
                    show_error("disabled function. [{$method}]");
                    return FALSE;
            }

            if ($ret = call_user_func_array(array($this->_db, $method), $args))
            {
                if (($method === 'get_where' || ($method === 'query' && preg_match('|^select|i', $args[0]))) && $results = $ret->result_array())
                {
                    $this->_records = $results;
                    log_info('--- SQL ------------------------------------------------------------');
                    log_info($this->_db->last_query());
                    log_info('--------------------------------------------------------------------');
                }
                elseif ($_is_last_query)
                {
                    log_info('--- SQL ------------------------------------------------------------');
                    log_info($this->_db->last_query());
                    log_info('--------------------------------------------------------------------');
                    if ($method == 'update' || $method == 'insert' || $method == 'insert_batch' || $method == 'update_batch' || $method == 'delete')
                    {
                        $rows = $this->_db->affected_rows();
                        log_info("[ {$rows} ] rows affected.");
                        log_info('--------------------------------------------------------------------');
                        return $rows;
                    }
                }
            }
            elseif (strpos($method, 'trans_') !== 0)
            {
                $this->_error = $this->_db->error();
                $this->_error['method'] = $method;
                $this->_error['sql'] = $this->_db->last_query();
                if ($this->_error['code'] != '1062')
                    log_error($this->_error);
            }

            return $ret;
        }

        if ( ! is_string($method))
        {
            $method = "";
        }

        show_error('$db object is not exist !');
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function connection_id()
    {
        if (isset($this->_db->conn_id))
            return $this->_db->conn_id->thread_id;
        else
            return NULL;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function __toString()
    {
        $string  = "[{$this->_class} Class]\n";
        $string .= var_export($this->_records, TRUE);
        return $string;
    }

    // -------------------------------------------------------------------------------------------------------------------------------

    public function __destruct()
    {
        if (ini_get('display_errors') && $this->connection_id())
        {
            $this->_db->trans_rollback();
        }
    }

}

