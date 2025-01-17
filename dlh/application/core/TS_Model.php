<?php

/*
 *
 * generic CRUD class
 *
 * @author:     hygsani
 * @email:      kierjarat@gmail.com
 * @created at: 01.07.2015
 * @version:    1.0.0
 *              1.1.0 - 03.02.2017
 *
 */

class TS_Model extends CI_Model {

    protected $table_name;
    protected $primary_key;
    protected $schema;

    public function __construct() {
        parent::__construct();

        $this->schema = 'portal';
    }

    /*
    * get archives
    *
    * @return mixed
    */
    public function get_archives() {
        try {
            $result = $this->db->query(
                'SELECT EXTRACT(MONTH FROM created_at) AS month, to_char(created_at, \'Month\') AS month_name, EXTRACT(YEAR FROM created_at) AS year, COUNT(' . $this->primary_key . ') AS total
                FROM ' . $this->table_name . '
                GROUP BY month, year, month_name'
            );

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * get recent records
    *
    * @param string $fields
    * @param array $limit
    *
    * @return mixed
    */
    public function get_recent_posts($fields, $limit=5) {
        try {
            $this->db->select($fields);
            $this->db->from($this->table_name);
            $this->db->where('is_publish', 1);
            $this->db->where('EXTRACT(YEAR FROM created_at) = ', date('Y'));
            $this->db->limit($limit);
            $this->db->order_by('created_at', 'DESC');

            $result = $this->db->get();

            return $result->result();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /*
    * insert record from array values
    *
    * @param array $data
    * @param array $log
    *
    * @return int
    */
    public function insert(array $data, array $log=null) {
        try {
            $this->db->trans_start();

            $this->db->insert($this->table_name, $data);

            if ($log !== null) {
                $id = $this->db->insert_id();
                $this->db->insert($this->schema . '.m_user_action_logs', array_merge($log, array('data_id' => $id)));
            }

            $result = $this->db->trans_complete();

            return $result;
        } catch (Exception $e) {
            $this->db->trans_rollback();

            return $e->getMessage();
        }
    }

    /*
    * update record from array values
    *
    * @param int $id
    * @param array $data
    * @param array $log
    *
    * @return int
    */
    public function update($id, array $data, array $log=null) {
        try {
            $this->db->trans_start();

            if ($id !== null)
                $this->db->where($this->primary_key, $id);

            $this->db->update($this->table_name, $data);

            if ($log !== null) {
                $this->db->insert($this->schema . '.m_user_action_logs', array_merge($log, array('data_id' => $id)));
            }

            $result = $this->db->trans_complete();

            return $result;
        } catch (Exception $e) {
            $this->db->trans_rollback();

            return $e->getMessage();
        }
    }

    /*
    * delete record by primary key
    *
    * @param int $id
    * @param array $log
    *
    * @return int
    */
    public function delete($id, array $log=null) {
        try {
            $this->db->trans_start();

            $this->db->where($this->primary_key, $id);
            $this->db->delete($this->table_name);

            if ($log !== null) {
                $this->db->insert($this->schema . '.m_user_action_logs', array_merge($log, array('data_id' => $id)));
                $this->db->where('data_id', $id);
                $this->db->update($this->schema . '.m_user_action_logs', array('is_deleted' => $log['is_deleted']));
            }

            $result = $this->db->trans_complete();

            return $result;
        } catch (Exception $e) {
            $this->db->trans_rollback();

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
    * @param array $url_query, null
    *
    * @return mixed
    */
    public function get_all_records($offset=0, $limit=10, $is_publish=null, $search_by=array(), $order_by=array(), array $url_query=null) {
        try {
            $this->db->select('a.*, b.username');
            $this->db->from($this->table_name . ' a');
            $this->db->join($this->schema . '.m_user b', 'a.created_by = b.user_id', 'left');

            if ($offset !== null && $limit !== null) {
                $this->db->limit($limit, $offset);
            }

            if ($is_publish !== null) {
                $this->db->where('is_publish', $is_publish);
            }

			if ($url_query !== null) {
				$this->db->where(key($url_query), $url_query[key($url_query)]);
			}

            if ($search_by != null) {
                for ($i=0; $i < count($search_by['field']); $i++) {
                    $this->db->or_like($search_by['field'][$i], $search_by['value']);
                }
            }

            if ($order_by != null) {
                $this->db->order_by($order_by['column'][$order_by['sort'][0]['column']]['data'], $order_by['sort'][0]['dir']);
            }

            $result = $this->db->get();

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
    * @param array $url_query, null
    *
    * @return int
    */
    public function get_count_all_records($is_publish=null, $search_by=array(), array $url_query=null) {
        try {
            if ($is_publish !== null) {
                $this->db->where('is_publish', $is_publish);
            }

			if ($url_query !== null) {
				$this->db->where(key($url_query), $url_query[key($url_query)]);
			}

            if ($search_by != null) {
                for ($i=0; $i < count($search_by['field']); $i++) {
                    $this->db->or_like($search_by['field'][$i], $search_by['value']);
                }
            }

            $result = $this->db->count_all_results($this->table_name . ' a');

            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /* count by param (array) */
    public function count_by_param($param=null) {
        try {
            if ($param !== null) {
                if (count($param) > 1) {
                    for ($i=0; $i < count($param); $i++) {
                        $this->db->where(key($param), $param[key($param)]);

                        next($param);
                    }
                } else {
                    $this->db->where(key($param), $param[key($param)]);
                }
            }

            $result = $this->db->count_all_results($this->table_name . ' a');

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
            $this->db->select('a.*, b.username');
            $this->db->from($this->table_name . ' a');
            $this->db->join($this->schema . '.m_user b', 'a.created_by = b.user_id', 'left');
            $this->db->where($this->primary_key, $id);

            $result = $this->db->get();

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
    public function get_all_records_by_id(array $id, $limit=null) {
        try {
            $this->db->where(key($id), $id[key($id)]);

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

            if ($limit !== null) {
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

    public function update_view_counter($id) {
        try {
            $this->db->trans_start();

            $this->db->where($this->primary_key, $id);
            $view_counter = $this->db->get($this->table_name)->row()->view_counter;

            if ($view_counter >= 0) {
                $view_counter += 1;
            }

            $this->db->where($this->primary_key, $id);
            $this->db->update($this->table_name, array('view_counter' => $view_counter));

            $this->db->trans_complete();
        } catch (Exception $e) {
            $this->db->trans_rollback();

            return $e->getMessage();
        }
    }

    public function run_query($sql) {
        $result = $this->db->query($sql);

        return $result->row_array();
    }
}