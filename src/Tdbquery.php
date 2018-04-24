<?php

namespace Jasechen\Tdbquery;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class Tdbquery
{

    protected $conn, $table;

    public function __construct($connName = 'mysql')
    {
        $this->conn = DB::connection($connName);
    } // END function


    /*
     *
     */
    public function fetch($query, $bindings = [])
    {
        $rows = $this->conn->select($query, $bindings);

        return Collection::make($rows);
    } // END function


    /*
     * fetchDatum
     *
     * @param  string  $table     table name
     * @param  array   $where     array of columns and values
     */
    public function fetchDatum($table, $where = [])
    {
        $bindings = [];

        $query = "SELECT * FROM " . $table . " ";

        if (empty($where)) {
            $query .= "WHERE 1";
        } else {
            $i = 0;
            foreach($where as $key => $value) {

                $query .= ($i == 0) ? "WHERE " : "AND ";
                $query .= "`" . $key . "` = ? ";

                array_push($bindings, $value);

                $i++;
            } // END foreach
        } // END if

        $query .= "LIMIT 1";

        $rows = $this->conn->select($query, $bindings);

        return Collection::make($rows);
    } // END function


    /*
     * fetchData
     *
     * @param  string  $table     table name
     * @param  array   $where     array of columns and values
     * @param  array   $orderby   array of columns and values
     * @param  integer $page      current page number
     * @param  integer $num_items number of per page
     */
    public function fetchData($table, $where = [], $orderby = [], $page = -1, $numItems = 20)
    {
        $bindings = [];

        $query  = "SELECT * FROM " . $table . " ";

        if (empty($where)) {
            $query .= "WHERE 1 ";
        } else {
            $i = 0;
            foreach($where as $key => $value) {

                $query .= ($i == 0) ? "WHERE " : "AND ";
                $query .= "`" . $key . "` = ? ";

                array_push($bindings, $value);

                $i++;
            } // END foreach
        } // END if else

        if (!empty($orderby)) {
            $i = 0;
            foreach($orderby as $column => $direction){
                $query .= ($i == 0) ? "ORDER BY " : ", ";
                $query .= $column . " " . strtoupper($direction) . " ";

                $i++;
            } // END foreach
        } // END if

        if ($page > 0) {
            $startItem = ($page-1) * $numItems;
            $query .= "LIMIT " . $startItem . ", " . $numItems;
        } // END if

        $rows = $this->conn->select($query, $bindings);

        return Collection::make($rows);
    } // END function


    /*
     * insert
     *
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    public function insert($table, $data)
    {
        $bindings = [];
        $columns = $this->fetchFields($table);

        $i = 0;
        $fieldQuery = $valueQuery = '';
        foreach ($data as $key => $value) {
            if (in_array($key, $columns)) {
                $fieldQuery .= ($i == 0) ? "" : ", ";
                $fieldQuery .= "`" . $key . "`";

                $valueQuery .= ($i == 0) ? "" : ", ";
                $valueQuery .= "?";

                array_push($bindings, $value);

                $i++;
            } // END if
        } // END foreach

        $query  = "INSERT INTO " . $table . "(" . $fieldQuery . ") VALUES (" . $valueQuery . ");";

        return $this->conn->insert($query, $bindings);
    } // END function


    /*
     * update
     *
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    public function update($table, $data, $where)
    {
        $bindings = [];
        $columns = $this->fetchFields($table);

        $i = 0;
        $setQuery = '';
        foreach ($data as $key => $value) {
            if (in_array($key, $columns)) {
                $setQuery .= ($i == 0) ? "SET " : ", ";
                $setQuery .= "`" . $key . "` = ?";

                array_push($bindings, $value);

                $i++;
            } // END if
        } // END foreach

        $i = 0;
        $whereQuery = '';
        foreach ($where as $key => $value) {
            if (in_array($key, $columns)) {
                $whereQuery .= ($i == 0) ? "WHERE " : "AND ";
                $whereQuery .= "`" . $key . "` = ? ";

                array_push($bindings, $value);

                $i++;
            } // END if
        } // END foreach

        $query = "UPDATE " . $table . " " . $setQuery . " " . $whereQuery . ";";

        return $this->conn->update($query, $bindings);
    } // END function


    /*
     * delete
     *
     * @param  string $table table name
     * @param  array $where array of columns and values
     */
    public function delete($table, $where = [])
    {
        $bindings = [];

        if (empty($where)) {
            $whereQuery = "WHERE 1 ";
        } else {
            $i = 0;
            $whereQuery = '';
            $columns = $this->fetchFields($table);

            foreach($where as $key => $value) {
                if (in_array($key, $columns)) {
                    $whereQuery .= ($i == 0) ? "WHERE " : "AND ";
                    $whereQuery .= "`" . $key . "` = ? ";

                    array_push($bindings, $value);

                    $i++;
                } // END if
            } // END foreach
        } // END if else

        $query  = "DELETE FROM " . $table . " " . $whereQuery . ";";

        return $this->conn->delete($query, $bindings);
    } // END function


    /*
     *
     */
    public function fetchFields($table)
    {
        $query = "SHOW COLUMNS FROM " . $table;

        $fields = $this->conn->select($query);

        foreach ($fields as $field) {
            $columns[] = $field->Field;
        }

        return empty($columns) ? false : $columns;
    } // END function

}