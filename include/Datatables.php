<?php

/**
 * MysqliDb Datatables Wrapper
 *
 * This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
 * Extended by Rajnish Savaliya as per needs
 *
 * @package    CorePHP 
 * @subpackage libraries
 * @category   library
 * @version    1.0
 * @author     Rajnish Savaliya <rajnishsavaliya@gmail.com>
 */

class Datatables {

    /**
     * Global container variables for chained argument results
     *
     */
    public $table;
    public $group_by = array();
    public $select = array();
    public $joins = array();
    public $columns = array();
    public $where = array();
    public $filter = array();
    public $add_columns = array();
    public $edit_columns = array();
    public $unset_columns = array();
    public $db;
    protected $request;

    function __construct($db) {
        $this->db = $db;
    }
    

    /**
     * Generates the FROM portion of the query
     *
     * @param string $table
     * @return mixed
     */
    public function from($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Generates the SELECT portion of the query
     *
     * @param string $columns
     * @param bool $backtick_protect
     * @return mixed
     */
    public function select($columns, $backtick_protect = TRUE) {
        foreach ($this->explode(',', $columns) as $val) {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->columns[] = $column;
            $this->select[$column] = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
        }
        
        
        return $this;
    }

    /**
     * Generates a custom GROUP BY portion of the query
     *
     * @param string $val
     * @return mixed
     */
    public function group_by($val) {
        $this->group_by[] = $val;
        $this->db->group_by($val);
        return $this;
    }

    /**
     * Generates the JOIN portion of the query
     *
     * @param string $table
     * @param string $fk
     * @param string $type
     * @return mixed
     */
    public function join($table, $fk, $type = NULL) {
        $this->joins[] = array($table, $fk, $type);
        $this->db->join($table, $fk, $type);
        return $this;
    }

     public function where($cond) {
         
         $this->where[] = $cond;
         $this->db->buildWhere($cond);
        return $this;
    }

    /**
     * Sets additional column variables for adding custom columns
     *
     * @param string $column
     * @param string $content
     * @param string $match_replacement
     * @return mixed
     */
    public function add_column($column, $content, $match_replacement = NULL) {
        $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    /**
     * Sets additional column variables for editing columns
     *
     * @param string $column
     * @param string $content
     * @param string $match_replacement
     * @return mixed
     */
    public function edit_column($column, $content, $match_replacement) {
        $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    /**
     * Unset column
     *
     * @param string $column
     * @return mixed
     */
    public function unset_column($column) {
        $column = explode(',', $column);
        $this->unset_columns = array_merge($this->unset_columns, $column);
        return $this;
    }

    /**
     * Builds all the necessary query segments and performs the main query based on results set from chained statements
     *
     * @param string $output
     * @param string $charset
     * @return string
     */
    public function generate($output = 'json', $charset = 'UTF-8') {

        $this->get_ordering();
        $this->get_filtering();
        return $this->produce_output(strtolower($output), strtolower($charset));
    }

    private function getPost($field){
        return isset($_POST[$field]) ? $_POST[$field] : "";
    }

    /**
     * Generates the ORDER BY portion of the query
     *
     * @return mixed
     */
    private function get_ordering() {
        if ($this->check_mDataprop())
            $mColArray = $this->get_mDataprop();
        elseif ($this->getPost('sColumns'))
            $mColArray = explode(',', $this->getPost('sColumns'));
        else
            $mColArray = $this->columns;

        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));

        for ($i = 0; $i < intval($this->getPost('iSortingCols')); $i++)
            if (isset($mColArray[intval($this->getPost('iSortCol_' . $i))]) && in_array($mColArray[intval($this->getPost('iSortCol_' . $i))], $columns) && $this->getPost('bSortable_' . intval($this->getPost('iSortCol_' . $i))) == 'true')
                $this->db->orderBy($mColArray[intval($this->getPost('iSortCol_' . $i))], $this->getPost('sSortDir_' . $i));
    }

    /**
     * Generates a %LIKE% portion of the query
     *
     * @return mixed
     */
    private function get_filtering() {
        if ($this->check_mDataprop())
            $mColArray = $this->get_mDataprop();
        elseif ($this->getPost('sColumns'))
            $mColArray = explode(',', $this->getPost('sColumns'));
        else
            $mColArray = $this->columns;

        $sWhere = '';
        $sSearch = $this->getPost('sSearch');
        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));

        if ($sSearch != '')
            for ($i = 0; $i < count($mColArray); $i++)
                if ($this->getPost('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns))
                    $sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";

        $sWhere = substr_replace($sWhere, '', -3);

        if ($sWhere != '')
            $this->db->where('(' . $sWhere . ')');

        $sRangeSeparator = $this->getPost('sRangeSeparator');

        for ($i = 0; $i < intval($this->getPost('iColumns')); $i++) {
            if (isset($_POST['sSearch_' . $i]) && $this->getPost('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns)) {
                $miSearch = explode(',', $this->getPost('sSearch_' . $i));

                foreach ($miSearch as $val) {
                    if (preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches))
                        $this->db->where($this->select[$mColArray[$i]] . ' ' . $matches[1], $matches[3]);
                    elseif (!empty($sRangeSeparator) && preg_match("/(.*)$sRangeSeparator(.*)/i", trim($val), $matches)) {
                        $rangeQuery = '';

                        if (!empty($matches[1]))
                            $rangeQuery = 'STR_TO_DATE(' . $this->select[$mColArray[$i]] . ",'%d/%m/%y %H:%i:%s') >= STR_TO_DATE('" . $matches[1] . " 00:00:00','%d/%m/%y %H:%i:%s')";

                        if (!empty($matches[2]))
                            $rangeQuery .= (!empty($rangeQuery) ? ' AND ' : '') . 'STR_TO_DATE(' . $this->select[$mColArray[$i]] . ",'%d/%m/%y %H:%i:%s') <= STR_TO_DATE('" . $matches[2] . " 23:59:59','%d/%m/%y %H:%i:%s')";

                        if (!empty($matches[1]) || !empty($matches[2]))
                            $this->db->where($rangeQuery);
                    } else
                        $this->db->where($this->select[$mColArray[$i]] . ' LIKE', '%' . $val . '%');
                }
            }
        }

    }

    /**
     * Compiles the select statement based on the other functions called and runs the query
     *
     * @return mixed
     */
    private function get_display_result($output) {
        $limit = 0;
        $offset = 0;
        
        if (strtolower($output) == 'json')
        {
            $iStart = $this->getPost('iDisplayStart');
            $iLength = $this->getPost('iDisplayLength');
            
            
            
            if ($iLength != '' && $iLength != '-1')
            {
                $limit = $iLength;
                $offset = ($iStart) ? $iStart : 0;
                
            }
        }
        
        
        return $this->db->get($this->table,$this->built_select(),[],[],$limit,$offset);
    }
    
    private function built_select(){
        $t = [];
        foreach ($this->select as $al=>$sql)
        {
            $t[] = $sql." AS ".$al;
        }
        return $t;
    }

    /**
     * Builds an encoded string data. Returns JSON by default, and an array of aaData and sColumns if output is set to raw.
     *
     * @param string $output
     * @param string $charset
     * @return mixed
     */
    private function produce_output($output, $charset) {
        $aaData = array();
        $rResult = $this->get_display_result($output);
        
        if ($output == 'json') {
            $iTotal = $this->get_total_results();
            $iFilteredTotal = $this->get_total_results(TRUE);
        }
        
        foreach ($rResult as $row_key => $row_val) {

            $aaData[$row_key] = ($this->check_mDataprop()) ? $row_val : array_values($row_val);

            foreach ($this->add_columns as $field => $val)
                if ($this->check_mDataprop())
                    $aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
                else
                    $aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);

            foreach ($this->edit_columns as $modkey => $modval)
                foreach ($modval as $val)
                    $aaData[$row_key][($this->check_mDataprop()) ? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);

            $aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_mDataprop()) ? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));

            if (!$this->check_mDataprop())
                $aaData[$row_key] = array_values($aaData[$row_key]);
        }

        $sColumns = array_diff($this->columns, $this->unset_columns);
        $sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));
        
        if ($output == 'json') {
            $sOutput = array
                (
                'sEcho' => intval($this->getPost('sEcho')),
                'iTotalRecords' => $iTotal,
                'iTotalDisplayRecords' => $iFilteredTotal,
                'aaData' => $aaData,
                'sColumns' => implode(',', $sColumns)
            );

            if ($charset == 'utf-8')
                return json_encode($sOutput);
            else
                return $this->jsonify($sOutput);
        } else
            return array('aaData' => $aaData, 'sColumns' => $sColumns);
    }

    /**
     * Get result count
     *
     * @return integer
     */
    private function get_total_results($filtering = FALSE) {
        if ($filtering)
            $this->get_filtering();
        
        foreach ($this->joins as $val)
            $this->db->join($val[0], $val[1], $val[2]);

        foreach ($this->where as $val)
        {
            $this->db->buildWhere($val);
        }
        
        foreach ($this->group_by as $val)
            $this->db->groupBy($val);

        
        $fir_key = current($this->select);
        
        $row =  $this->db->get_row($this->table,"COUNT($fir_key) AS cnt");
        
        if($row)
        {
            return $row['cnt'];
        }
    }

    /**
     * Runs callback functions and makes replacements
     *
     * @param mixed $custom_val
     * @param mixed $row_data
     * @return string $custom_val['content']
     */
    private function exec_replace($custom_val, $row_data) {
        $replace_string = '';

        if (isset($custom_val['replacement']) && is_array($custom_val['replacement'])) {
            foreach ($custom_val['replacement'] as $key => $val) {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));

                if (preg_match('/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches) && is_callable($matches[1])) {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

                    foreach ($args as $args_key => $args_val) {
                        $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        $args[$args_key] = (in_array($args_val, $this->columns)) ? ($row_data[($this->check_mDataprop()) ? $args_val : array_search($args_val, $this->columns)]) : $args_val;
                    }

                    $replace_string = call_user_func_array($func, $args);
                } elseif (in_array($sval, $this->columns))
                    $replace_string = $row_data[($this->check_mDataprop()) ? $sval : array_search($sval, $this->columns)];
                else
                    $replace_string = $sval;

                $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
            }
        }

        return $custom_val['content'];
    }

    /**
     * Check mDataprop
     *
     * @return bool
     */
    private function check_mDataprop() {

        if (!$this->getPost('mDataProp_0'))
            return FALSE;

        for ($i = 0; $i < intval($this->getPost('iColumns')); $i++)
            if (!is_numeric($this->getPost('mDataProp_' . $i)))
                return TRUE;

        return FALSE;
    }

    /**
     * Get mDataprop order
     *
     * @return mixed
     */
    private function get_mDataprop() {
        $mDataProp = array();

        for ($i = 0; $i < intval($this->getPost('iColumns')); $i++)
            $mDataProp[] = $this->getPost('mDataProp_' . $i);

        return $mDataProp;
    }

    
    
    /**
     * Explode, but ignore delimiter until closing characters are found
     *
     * @param string $delimiter
     * @param string $str
     * @param string $open
     * @param string $close
     * @return mixed $retval
     */
    public function explode($delimiter, $str, $open = '(', $close = ')') {
        $retval = array();
        $hold = array();
        $balance = 0;
        $parts = explode($delimiter, $str);
        
        foreach ($parts as $part) {
            if(trim($part))
            {
                $hold[] = $part;
                $balance += $this->balanceChars($part, $open, $close);
                
                if ($balance < 1) {
                    $retval[] = implode($delimiter, $hold);
                    $hold = array();
                    $balance = 0;
                }
            }
        }
        
        if (count($hold) > 0)
            $retval[] = implode($delimiter, $hold);
            
            return $retval;
    }

    
    /**
     * Return the difference of open and close characters
     *
     * @param string $str
     * @param string $open
     * @param string $close
     * @return string $retval
     */
    private function balanceChars($str, $open, $close) {
        $openCount = substr_count($str, $open);
        $closeCount = substr_count($str, $close);
        $retval = $openCount - $closeCount;
        return $retval;
    }
    
    /**
     * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
     *
     * @param mixed $result
     * @return string
     */
    private function jsonify($result = FALSE) {
        if (is_null($result))
            return 'null';

        if ($result === FALSE)
            return 'false';

        if ($result === TRUE)
            return 'true';

        if (is_scalar($result)) {
            if (is_float($result))
                return floatval(str_replace(',', '.', strval($result)));

            if (is_string($result)) {
                static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
            } else
                return $result;
        }

        $isList = TRUE;

        for ($i = 0, reset($result); $i < count($result); $i++, next($result)) {
            if (key($result) !== $i) {
                $isList = FALSE;
                break;
            }
        }

        $json = array();

        if ($isList) {
            foreach ($result as $value)
                $json[] = $this->jsonify($value);

            return '[' . join(',', $json) . ']';
        } else {
            foreach ($result as $key => $value)
                $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);

            return '{' . join(',', $json) . '}';
        }
    }

}
/* End of file Datatables.php */
?>