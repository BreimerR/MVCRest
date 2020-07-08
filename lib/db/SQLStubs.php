<?php
/**
 * Created by PhpStorm.
 * Author: Breimer
 * Email : brymher@yahoo.com
 * Date: 7/8/20
 * Time: 1:04 AM
 */

trait SQLStubs
{

    /**
     *
     */
    function _delete($table, $where)
    {

        $this->delete($table, $where);

        return !$this->errorExists();
    }

    /**
     * Associative array.
     * @param array $items .
     * @param string $table .
     * @return bool
     */
    function _insert($table, $items = array())
    {
        if (count($items)) {
            $insert = array();

            foreach ($items as $key => $value) {
                array_push($insert, $key . "/=/" . $value);
            }

            $this->insert($table, $insert);


            return !$this->errorExists();
        }

        return false;
    }

    /**
     * same array as insert.
     * @param $table
     * @param array $set
     * @param $where
     * @return bool
     *
     */
    function _update($table, $set = [], $where)
    {
        if (is_array($set)) {
            $set = (function ($set) {
                $update = [];
                foreach ($set as $key => $value) {
                    array_push($update, "$key/=/$value");
                }
                return $update;
            })($set);
        }

        $this->update($table, $set, $where);

        return !$this->errorExists();
    }
}