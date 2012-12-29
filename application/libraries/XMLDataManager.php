<?php
/*
 * @Library Name: XMLDataManager
 * @author      : Carlos
 * @Description : Biblioteca para simular o active record de acoes com insert,delete,update,join
 */

class XMLDataManager {
    private $_doc_xml = array('version'=>'1.0','charset'=>'utf-8');
    private $ci; //instance frawework
    private $_config;
    private $_xmlselect = array();
    private $_tmpTable;
    private $db;
    private $_result;
    private $_result_array;
    private $_num_rows;
    private $_tmpname;
    private $_table;
    private $_limit;
    private $_key = 'id';
    private $_last_id;
    private $_joins = array();
    private $_count_joins = 0;
    private $_join_data;
    private $_num_joins = 0;
    private $_joined = array();
    private $_columns_joined = array();
    private $_alias = array();
    private $_where = array();

    function __construct() {
        $this->ci = & get_instance();
        $this->ci->config->load('xml', TRUE);
        $this->_config = $this->ci->config->item('xml');
        $this->db = $this->_config['path'];
        $this->_table = $this->_config['table'];
        $this->_tmpname = $this->db;
        $xmlString = file_get_contents($this->db);

        $this->db = new SimpleXMLElement($xmlString);
        $this->ci->load->library('session');
    }

    function select($sql = '*') {
        $this->_xmlselect['columns'] = trim($sql);
        return $this;
    }

    function from($table) {
        $this->_tmpTable = $table;
        $this->_xmlselect['table'] = $table;
        return $this;
    }

    function join($table, $comp) {
        if (!in_array($table, array_keys($this->_joins))) {
            $this->_joins[$table] = $comp;
            $this->_count_joins++;
        }
        return $this;
    }

    function limit($limit) {
        $this->_limit = (int) $limit;
    }

    function where(array $where) {
        $this->_where = $where;
        return $this;
    }

    function exec() {

        $db = $this->db->database;

        if (empty($this->_xmlselect['table'])) {
            show_error('Tabela não selecionada.');
            return;
        }

        $result = '';
        $result_a = array();
        $i = 0;
        $row = $this->_table;
        $all_data_true = true;
        $this->tmp_row_table = $this->_xmlselect['table'];
        $alias = $this->alias();

        $join_array = array();
        $join_object = array();
        $join_enable = false;
        $joined_array_data = '';
        $joined_object_data = '';


        foreach ($db->$row['row']['element'] as $table) {
            $n = $table->attributes();
            $name_tb = (string) $n[$row['row']['attribute']];

            //checando joins
            if ($this->_count_joins > 0) {
                $joins_t = array_keys($this->_joins);
                $ind = array_search($name_tb, $joins_t);
                $tmp_table = $joins_t[$ind];
                if (in_array($name_tb, $joins_t) && strtolower(trim($name_tb)) == strtolower(trim($tmp_table))) {
                    $this->tmp_join_table = $tmp_table;
                    $qe = $this->alias(array($this->_joins[$tmp_table]));
                    $key_local = $qe[$tmp_table]['key'];
                    $key_table = $qe[$this->_xmlselect['table']]['key'];

                    foreach ($table->$row['column']['element'] as $column_join) {
                        $attr = $column_join->attributes();
                        $name_col = (string) $attr[$row['column']['attribute']];

                        if ($this->_columns_joined[$name_tb]['orig'] == 'all') {
                            $data_column = (string) $column_join;
                            $data_join_a[$name_col] = trim($data_column);
                            $data_join_o->$name_col = trim($data_column);
                            $data_join_orig[$name_col] = trim($data_column);
                        } else {

                            if (in_array($name_col, $this->_columns_joined[$name_tb]['orig'])) {

                                $index = array_search($name_col, $this->_columns_joined[$name_tb]['orig']);
                                $alias_n = $this->_columns_joined[$name_tb]['alias'][$index];
                                $alias_o = $this->_columns_joined[$name_tb]['orig'][$index];
                                $data_column = (string) $column_join;
                                $data_join_a[$alias_n] = trim($data_column);
                                $data_join_o->$alias_n = trim($data_column);
                                $data_join_orig[$alias_o] = trim($data_column);
                            }
                        }
                    }

                    $this->_join_data['_data_orig'][] = $data_join_orig;
                    $this->_join_data['_data_array'][] = $data_join_a;
                    $this->_join_data['_data_object'][] = $data_join_o;
                    $this->_join_data['_join_table'] = $tmp_table;
                    $data_join_o = '';
                    $data_join_a = '';
                }
            }


            //select
            if (strtolower(trim($name_tb)) == strtolower(trim($this->_xmlselect['table']))) {
                $this->_join_data['_join_original']['table'] = $name_tb;
                foreach ($table->$row['column']['element'] as $column) {
                    $attr = $column->attributes();
                    $name_col = (string) $attr[$row['column']['attribute']];
                    if ($this->_columns_joined[$name_tb]['orig'] == 'all') {
                        $result->$name_col = (string) trim($column);
                        $result_a[$name_col] = (string) trim($column);
                    } else {
                        if (in_array($name_col, $this->_columns_joined[$name_tb]['orig'])) {
                            $index = array_search($name_col, $this->_columns_joined[$name_tb]['orig']);
                            $alias_n = $this->_columns_joined[$name_tb]['alias'][$index];
                            $result->$alias_n = (string) trim($column);
                            $result_a[$alias_n] = (string) trim($column);
                        }
                    }

                    $all_columns[$name_col] = (string) trim($column);
                }
                $this->_join_data['_join_original'][] = $all_columns;


                $this->_result[] = $result;
                $this->_result_array[] = $result_a;
            }





            $i++;

            //resetando os dados dos arrays 
            $result = '';
            $result_a = array();
            $all_columns = array();
        }
        $this->_num_rows = count($this->_result);
    }

    function result() {
        $dt['objects_data'] = array();
        $dt['objects_data'] = $this->_result;
        if ($this->_count_joins > 0) {
            $dt = $this->get_join($this->_result);
        }
        asort($dt['objects_data']);

        $i = $this->_limit;
        $j = 1;
        $w = count($this->_where);
        $kw = $w > 0 ? array_keys($this->_where) : array();
        $a = array();
        foreach ($dt['objects_data'] as $item) {
            $da[] = $item;
            if ($w > 0) {
                foreach ($item as $k => $v) {

                    if (array_key_exists($k, $this->_where)) {
                        $pos = array_search($k, array_keys($this->_where));

                        $_id = $kw[(int) $pos];
                        if ($this->_where[$_id] == $v) {
                            $a[] = $item;
                        }
                    }
                }
            }
            if ($this->_limit > 0 && $j == $i && $w == 0) {
                break;
            }

            $j++;
        }

        $dt['objects_data'] = count($a) > 0 ? $a : $da;

        return $dt['objects_data'];
    }

    function result_array() {
        $dt['array_data'] = array();
        $dt['array_data'] = $this->_result_array;
        if ($this->_count_joins > 0) {
            $dt = $this->get_join($this->_result_array);
        }
        asort($dt['array_data']);
        $i = $this->_limit;

        $w = count($this->_where);
        $kw = $w > 0 ? array_keys($this->_where) : array();
        $a = array();
        $j = 1;
        foreach ($dt['array_data'] as $item) {
            $da[] = $item;
            if ($w > 0) {
                foreach ($item as $k => $v) {

                    if (array_key_exists($k, $this->_where)) {
                        $pos = array_search($k, array_keys($this->_where));

                        $_id = $kw[(int) $pos];
                        if ($this->_where[$_id] == $v) {
                            $a[] = $item;
                        }
                    }
                }
            }


            if ($this->_limit > 0 && $j == $i && $w == 0) {
                break;
            }
            $j++;
        }
        $dt['array_data'] = $da;

        return $dt['array_data'];
    }

    function num_rows() {
        return $this->_num_rows;
    }

    function delete($table, array $id) {
        $db = $this->db->database;
        $row = $this->_table;
        $this->_xmlselect['table'] = $table;
        $this->num_registers_delete = 0;
        foreach ($db->$row['row']['element'] as $table) {
            $n = $table->attributes();
            $name = (string) $n[$row['row']['attribute']];
            $remove = dom_import_simplexml($table);
            if (strtolower(trim($name)) == strtolower(trim($this->_xmlselect['table']))) {
                foreach ($table->$row['column']['element'] as $column) {
                    $attr = $column->attributes();
                    $name = (string) $attr[$row['column']['attribute']];
                     $_id = trim($name);
                    if ($_id == $this->_key && (int) $column == (int) $id[$this->_key]) {
                        $remove->parentNode->removeChild($remove);
                        $this->save();
                        $this->num_registers_delete++;
                    }
                }
            }
        }
        if($this->num_registers_delete > 0) return true;  
        return false;
    }
    function num_registers_delete()
    {
        if(isset($this->num_registers_delete)){ return $this->num_registers_delete;}
        
    }
    
    
    
    function update($table, $data, $where = array()) {

        if (!is_array($where) || count($where) == 0) {
            show_error('N&atilde;o foi enviado a condi&ccedil;&atilde;o where');
        }
        if (isset($data[$this->_key])) {
            unset($data[$this->_key]);
        }
        $data = !is_array($data) ? array() : $data;
        if (count($data) == 0) {
            show_error('N&atilde;o foram enviado dados para alterar');
        }


        $db = $this->db->database;
        $row = $this->_table;
        $root = $row['row']['element'];
        $col = $row['column']['element'];
        $q = $db->xpath('' . $root . '[@' . $row['row']['attribute'] . '="' . $table . '"]');


        $data_keys = array_keys($data);
        $data_values = array_values($data);
        $where_keys = array_keys($where);
        $where_values = array_values($where);
        $pause = '';
        $alter = false;
        $remove = '';
        $result = '';
        $data = array();
        foreach ($q as $k => $columns) {
            $dcl = dom_import_simplexml($columns);

            $data['root']['element'] = $row['row']['element'];
            $data['root']['@attribute'] = $row['row']['attribute'];
            $data['root']['@value'] = $table;
            foreach ($columns as $col) {
                $attr = $col->attributes();
                $dms = dom_import_simplexml($col);
                $id = $attr[$row['column']['attribute']];
                $data['columns'][] = array(
                    'element' => $row['column']['element'],
                    'value' => (string) $col,
                    '@attribute' => $row['column']['attribute'],
                    '@value' => (string) $id);
                $pos = (int) array_search($id, $where_keys);

                if (in_array($id, $where_keys)) {
                    //recuperando indice do where com os nomes das colunas
                    $associative = $where_keys[$pos];
                    $value_update = $where_values[$pos];
                    $val = (string) $col;
                    if (strtolower(trim($associative)) == strtolower(trim($id)) && $value_update == $val) {
                        $alter = true;
                    }
                }
            }
            //se exists register for editable 
            if ($alter) {

                $dcl->parentNode->removeChild($dcl);
                $add = $this->db->database->addChild($data['root']['element']);
                $add->addAttribute($data['root']['@attribute'], $data['root']['@value']);
                foreach ($data['columns'] as $cl) {
                    $pos = (int) array_search($cl['@value'], $data_keys);
                    if (in_array($cl['@value'], $data_keys)) {
                        $_col = $add->addChild($cl['element'], $data_values[$pos]);
                    } else {
                        $_col = $add->addChild($cl['element'], $cl['value']);
                    }

                    $_col->addAttribute($cl['@attribute'], $cl['@value']);
                }
                $this->save();
                return true;
            }
            $data = array();
            $alter = false;
        }
        return false;
    }
    
    

    function set_key($key) {
        $this->_key = (string) $key;
    }

    function insert($table, $data) {
        $this->_row = $table;
        $columns = $this->_columns();
        $row = $this->_table;

        $add = $this->db->database->addChild($row['row']['element']);
        $add->addAttribute($row['row']['attribute'], $table);
        $pos = array_search($this->_key, $columns['columns']);
        $this->_xmlselect['table'] = $table;
        $this->_last_id = $this->last_id() + 1;
        unset($columns['columns'][$pos]);
        $_column = $add->addChild($row['column']['element'], $this->_last_id);
        $_column->addAttribute($row['column']['attribute'], $this->_key);

        foreach ($columns['columns'] as $item) {

            if (array_key_exists($item, $data)) {
                $column = $add->addChild($row['column']['element'], $data[$item]);
            } else {
                $column = $add->addChild($row['column']['element'], '');
            }

            $column->addAttribute($row['column']['attribute'], $item);
        }
        $this->save();
    }
    /* get last id inserted
     * return int
     */
    function insert_id() {
        return (int) $this->_last_id;
    }

    private function last_id() {
        $db = $this->db->database;
        $row = $this->_table;

        //
        foreach ($db->$row['row']['element'] as $table) {
            $n = $table->attributes();
            $name = (string) $n[$row['row']['attribute']];
            if (strtolower(trim($name)) == strtolower(trim($this->_xmlselect['table']))) {
                foreach ($table->$row['column']['element'] as $k) {
                    $attr = $k->attributes();
                    $key_ = (string) $attr[$row['column']['attribute']];
                    if ($key_ == $this->_key) {
                        $keys[] = (string) $k;
                    }
                }
            }
        }
        if (count($keys) == 0) {
            return 1;
        }

        $offset = count($keys) - 1;
        sort($keys);
        $last_id = array_slice($keys, $offset);
        return (int) $last_id['0'];
    }

    private function save() {
        $dom = new DOMDocument($this->_doc_xml['version'],$this->_doc_xml['charset']);
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $dom->loadXML($this->db->asXML());
        $dom->save($this->_tmpname);
    }

    private function _columns() {
        $db = $this->db->strutcture;
        $row = $this->_table;

        $columns = array();

        $structure = $db->xpath('table[@name="' . trim($this->_row) . '"]');
        if (!$structure) {
            show_error('Tabela ' . $this->_row . ' n&atilde;o encontrada .');
        }
        foreach ($structure[0]->columns->label as $col) {
            $columns['columns'][] = (string) trim($col);
        }

        return $columns;
    }

    private function alias() {


        if ($this->_count_joins > 0) {

            $_columns = strpos($this->_xmlselect['columns'], ',') ? explode(',', $this->_xmlselect['columns']) : false;

            if ($_columns) {
                foreach ($_columns as $col) {
                    $col = trim($col);
                    if (preg_match('#[a-zA-Z|_]\s(as)\s[a-zA-Z|_]#', $col)) {
                        $_alias = explode(' as ', $col);
                        if (strpos($_alias[0], '.')) {
                            $_alias_explode = explode('.', $_alias[0]);
                            $this->_columns_joined[$_alias_explode[0]]['orig'][] = $_alias_explode[1];
                            $this->_columns_joined[$_alias_explode[0]]['alias'][] = $_alias[1];
                        }
                    } else
                    if (preg_match('#[a-zA-Z_]\.[a-zA-Z_]#', $col)) {
                        $_alias_explode = explode('.', $col);
                        $this->_columns_joined[$_alias_explode[0]]['orig'][] = $_alias_explode[1];
                        $this->_columns_joined[$_alias_explode[0]]['alias'][] = $_alias_explode[1];
                    } else
                    if (preg_match('#[\*|\.\*]#', $col) && !preg_match('#[a-zA-Z|_]\s(as)\s[a-zA-Z|_]#', $col)) {
                        $_alias_explode = explode('.', $col);
                        $this->_columns_joined[$_alias_explode[0]]['orig'] = 'all';
                        $this->_columns_joined[$_alias_explode[0]]['alias'] = 'all';
                    }
                }
            }
            foreach ($this->_joins as $k => $va) {
                if (preg_match('#=#', $va)) {
                    $join_comp = explode('=', $va);
                    if (preg_match('#\.#', $join_comp['0'])) {
                        $ex_p = explode('.', $join_comp['0']);
                        $data['join'][$ex_p['0']] = $ex_p['1'];
                    }
                    if (preg_match('#\.#', $join_comp['1'])) {
                        $ex_p = explode('.', $join_comp['1']);
                        $data['join'][$ex_p['0']] = $ex_p['1'];
                    }
                }
            }

            $this->_join_data['join'] = $data['join'];

            if (count($this->_columns_joined) == 0) {
                show_error('O metodo join precisa que seja selecionadas colunas de duas tabelas');
            }
        } else {
            $d = strpos($this->_xmlselect['columns'], ',') ? explode(',', $this->_xmlselect['columns']) : $this->_xmlselect['columns'];
            $_columns = !is_array($d) ? array($d) : $d;

            foreach ($_columns as $col) {

                if (preg_match('#[a-zA-Z|_]\s(as)\s[a-zA-Z|_]#', $col)) {
                    $_alias = explode(' as ', $col);
                    if (strpos($_alias[0], '.')) {
                        $_alias_explode = strpos($col, '.') ? explode('.', $col) : $col;
                        $tb = !is_array($_alias_explode) ? trim($this->_xmlselect['table']) : $_alias_explode[0];
                        $this->_columns_joined[$tb]['orig'][] = $_alias_explode[1];
                        $this->_columns_joined[$tb]['alias'][] = $_alias[1];
                    } else {
                        $tb = trim($this->_xmlselect['table']);
                        $this->_columns_joined[$tb]['orig'][] = $_alias[0];
                        $this->_columns_joined[$tb]['alias'][] = $_alias[1];
                    }
                } else
                if (preg_match('#\.[a-zA-Z_]#', $col)) {
                    $_alias_explode = strpos($col, '.') ? explode('.', $col) : $col;
                    $tb = !is_array($_alias_explode) ? trim($this->_xmlselect['table']) : $_alias_explode[0];
                    $this->_columns_joined[$tb]['orig'][] = $_alias_explode[1];
                    $this->_columns_joined[$tb]['alias'][] = $_alias_explode[1];
                } else
                if (preg_match('#[\*|\.\*]#', $col)) {
                    $_alias_explode = strpos($col, '.') ? explode('.', $col) : $col;
                    $tb = !is_array($_alias_explode) ? trim($this->_xmlselect['table']) : $_alias_explode[0];
                    $this->_columns_joined[$tb]['orig'] = 'all';
                    $this->_columns_joined[$tb]['alias'] = 'all';
                } else {
                    $tb = trim($this->_xmlselect['table']);
                    $this->_columns_joined[$tb]['orig'][] = $col;
                    $this->_columns_joined[$tb]['alias'][] = $col;
                }
            }
        }
    }

    private function get_join($result) {



        $ikeys = $this->_join_data['_data_orig'];
        //join
        //table
        //_join_table
        $rs = array();

        foreach ($result as $k => $v) {
            $original = ($this->_join_data['_join_original'][$k]);
            $tb_orig = $this->_join_data['_join_original']['table'];
            $t_join = $this->_join_data['_join_table'];
            $key = $this->_join_data['join'][$tb_orig];
            $_key = $this->_join_data['join'][$t_join];
            $value = $original[$key];


            if (gettype($this->get_data_joins($value, $_key)) == 'integer') {
                $pos = (int) $this->get_data_joins($value, $_key);
                $rs['array_data'][] = (array) array_merge((array) $this->_join_data['_data_array'][(int) $pos], (array) $result[$k]);
                $rs['objects_data'][] = (object) array_merge((array) $this->_join_data['_data_object'][(int) $pos], (array) $result[$k]);
                $this->_num_joins++;
            }
        }

        return($rs);
    }

    private function get_data_joins($value, $key) {

        $ikeys = $this->_join_data['_data_orig'];
        foreach ($ikeys as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $ka => $da) {
                    if (trim($da) == trim($value) && !empty($value) && $ka == $key) {
                        return (int) $k;
                    }
                }
            }
        }

        return 'no_data';
    }

}

?>
