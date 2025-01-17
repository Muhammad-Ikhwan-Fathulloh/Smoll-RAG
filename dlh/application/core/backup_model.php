<?php

/*
 *
 * generic CRUD class
 *
 * @author:     hygsani
 * @email:      kierjarat@gmail.com
 * @created at: 01.07.2015
 * @version:    1.0.0
 *
 */

class TS_Model extends CI_Model {

    protected $table_name;
    protected $primary_key;

    public function __construct() {
        parent::__construct();
    }

    /*
    * insert record from array values
    *
    * @param array $data
    *
    * @return int
    */
    public function insert(array $data) {
        try {
            $this->db->insert($this->table_name, $data);

            $result = $this->db->affected_rows();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * update record from array values
    *
    * @param int $id
    * @param array $data
    *
    * @return int
    */
    public function update($id, array $data) {
        try {
            if ($id != null)
                $this->db->where($this->primary_key, $id);

            $this->db->update($this->table_name, $data);

            $result = $this->db->affected_rows();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * delete record by primary key
    *
    * @param int $id
    *
    * @return int
    */
    public function delete($id) {
        try {
            $this->db->where($this->primary_key, $id);
            $this->db->delete($this->table_name);

            $result = $this->db->affected_rows();

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get all records
    *
    * @param int $offset, 0
    * @param int $limit, 10
    * @param bool $is_publish, null
    * @param array $search_by
    * @param array $order_by
    *
    * @return mixed
    */
    public function get_all_records($offset=0, $limit=10, $is_publish=null, $search_by=array(), $order_by=array()) {
        try {
            if ($offset != null && $limit != null)
                $this->db->limit($limit, $offset);

            if ($is_publish !== null) {
                $this->db->where('is_publish', $is_publish);
            }

            if ($search_by != null) {
                for ($i=0; $i < count($search_by['field']); $i++) {
                    $this->db->or_like($search_by['field'][$i], $search_by['value']);
                }
            }

            if ($order_by != null) {
                $this->db->order_by($order_by['column'][$order_by['sort'][0]['column']]['data'], $order_by['sort'][0]['dir']);
            }

            $result = $this->db->get($this->table_name);

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * count all records
    *
    * @param bool $is_publish, null
    * @param array $search_by
    *
    * @return int
    */
    public function get_count_all_records($is_publish=null, $search_by=array()) {
        try {
            if ($is_publish !== null) {
                $this->db->where('is_publish', $is_publish);
            }

            if ($search_by != null) {
                for ($i=0; $i < count($search_by['field']); $i++) {
                    $this->db->or_like($search_by['field'][$i], $search_by['value']);
                }
            }

            $result = $this->db->count_all_results($this->table_name);

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get single record by primary key
    *
    * @param int $id
    *
    * @return array
    */
    public function get_record_by_id($id) {
        try {
            $this->db->where($this->primary_key, $id);

            $result = $this->db->get($this->table_name);

            return $result->row_array();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get single record by parameters
    *
    * @param int $id
    *
    * @return array
    */
    public function get_record_by_params(array $params) {
        try {
            if (count($params) > 1) {
                for ($i=0; $i < count($params); $i++) {
                    $this->db->where(key($params), $params[key($params)]);
                    next($params);
                }
            } else {
                $this->db->where(key($params), $params[key($params)]);
            }

            $result = $this->db->get($this->table_name);

            return $result->row_array();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get all records by primary key/foreign key
    *
    * @param array $id
    * @param int $limit, 5
    *
    * @return mixed
    */
    public function get_all_records_by_id(array $id, $limit=5) {
        try {
            $this->db->where($id['key'], $id['value']);

            if ($limit != null) {
                $this->db->limit($limit);
            }

            $result = $this->db->get($this->table_name);

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get all records by parameters
    *
    * @param array $params
    * @param array $order_by
    * @param int $limit, 5
    *
    * @return mixed
    */
    public function get_all_records_by_params(array $params, $order_by=array(), $limit=5) {
        try {
            if (count($params) > 1) {
                for ($i=0; $i < count($params); $i++) {
                    $this->db->where(key($params), $params[key($params)]);
                    next($params);
                }
            } else {
                $this->db->where(key($params), $params[key($params)]);
            }

            if ($order_by) {
                $this->db->order_by(key($order_by), $order_by[key($order_by)]);
            }

            if ($limit != null) {
                $this->db->limit($limit);
            }

            $result = $this->db->get($this->table_name);

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get all records for combobox options
    *
    * @return mixed
    */
    public function populate_options($params=array()) {
        try {
            //next($params);
            //echo $params[key($params)]; die(); //strpos($params[key($params)], '!'); die();

            if ($params) {
                if (count($params) > 1) {
                    for ($i=0; $i < count($params); $i++) {
                        if (strpos($params[key($params)], '!') !== false) {
                            $this->db->where(key($params) . ' !=', substr($params[key($params)], 1, strlen($params[key($params)])));
                        } else {
                            $this->db->where(key($params), $params[key($params)]);
                        }

                        next($params);
                    }
                } else {
                    $this->db->where(key($params), $params[key($params)]);
                }
            }

            $result = $this->db->get($this->table_name);

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}