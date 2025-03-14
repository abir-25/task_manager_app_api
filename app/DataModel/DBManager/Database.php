<?php
/**
 * Created by PhpStorm.
 * User: yasin
 * Date: 2/11/2016
 * Time: 8:13 PM
 */

namespace App\DataModel\DBManager;

use App\DataModel\Manager\ErrorLogManager;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Database {

    /**
     * @throws \Exception
     */
    public function executeQueryDataReturn($query)
    {
        try {
            return DB::select($query);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeQueryDataReturnWithParameter($query, $parameter)
    {
        try {
            return DB::select($query,$parameter);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }

    }

    /**
     * @throws \Exception
     */
    public function executeQueryInsert($query,$parameter)
    {
        try {
            DB::insert($query,$parameter);
            return DB::getPdo()->lastInsertId();
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeQueryWithParameter($query,$parameter)
    {
        try {
            DB::update($query,$parameter);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeQuery($query)
    {
        try {
            DB::update($query);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeQueryDeleteWithParameter($query,$parameter)
    {
        try {
            DB::delete($query,$parameter);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeQueryDelete($query)
    {
        try {
            DB::delete($query);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeStatementWithParameter($query,$parameter)
    {
        try {
            DB::statement($query,$parameter);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function executeStatement($query)
    {
        try {
            DB::statement($query);
        } catch(QueryException $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function toArray($result)
    {
        $data=[];
        foreach ($result as $row){
            $data[]=(array) $row;
        }
        return $data;
    }

    public function executeTransaction(){
        DB::beginTransaction();
    }

    public function executeCommit(){
        DB::commit();
    }

    public function executeRollBack(){
        DB::rollback();
    }

    /* Insert Multiple Rows in a table */
    public function bulkInsertSqlStatement($table,$rows){
        $dataFields = [];
        $question_marks = [];
        $insert_values = array();
        foreach($rows as $d){
            $question_marks[] = '('  . $this->placeholders('?', sizeof($d)) . ')';
            $insert_values = array_merge($insert_values, array_values($d));
            $dataFields = array_keys($d);
        }
        $queryString = "INSERT INTO $table (" . implode(",", $dataFields ) . ") VALUES " . implode(',', $question_marks);
        return array($queryString,$insert_values);
    }

    /*  placeholders for prepared statements like (?,?,?)  */
    public function placeholders($text, $count=0, $separator=","){
        $result = array();
        if($count > 0){
            for($x=0; $x<$count; $x++){
                $result[] = $text;
            }
        }
        return implode($separator, $result);
    }

    function bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count, $more_condition_column=null,  $more_condition_column_value=null)
    {
        $field_array=explode(",",$update_column);
        $sql_up = "UPDATE $table SET ";
        for ($len=0; $len<count($field_array); $len++)
        {
            $sql_up.=" ".$field_array[$len]." = CASE $id_column ";
            for ($id=0; $id<count($id_count); $id++)
            {
                if (trim($data_values[$id_count[$id]][$len])=="") $sql_up.=" when ".$id_count[$id]." then  '".$data_values[$id_count[$id]][$len]."'" ;
                else $sql_up.=" when ".$id_count[$id]." then  ".$data_values[$id_count[$id]][$len]."" ;
            }
            if ($len!=(count($field_array)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
        }
        $sql_up.=" where $id_column in (".implode(",",$id_count).")";
        if($more_condition_column){
            $sql_up.=" AND $more_condition_column = $more_condition_column_value";
        }
        return $sql_up;
    }

    public function bulkInsertSqlStatementReDone($table,$rows){
        $dataFields = [];
        $question_marks = [];
        $insert_values = array();
        $values = '';
        foreach($rows as $d){
            $dataFields = array_keys($d);
            $values .= "(";
            foreach ($d as $t){
                $values .= "'$t',";
            }
            $values = substr($values, 0, -1);
            $values .=  "),";
        }
        $values = substr($values, 0, -1);

        return $queryString = "INSERT INTO $table (" . implode(",", $dataFields ) . ") VALUES " . $values;

    }

    function bulkInsert($tableInsert, $rowsInsert)
    {
        $insertQuery = $this->bulkInsertSqlStatement($tableInsert,$rowsInsert);
        $this->executeStatementWithParameter($insertQuery[0],$insertQuery[1]);
    }
}
