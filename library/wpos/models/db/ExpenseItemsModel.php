<?php

/**
 * ExpensesModel is part of Wallace Point of Sale system (WPOS) API
 *
 * ExpensesModel extends the DbConfig PDO class to interact with the config DB table
 *
 * WallacePOS is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * WallacePOS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details:
 * <https://www.gnu.org/licenses/lgpl.html>
 *
 * @package    wpos
 * @copyright  Copyright (c) 2014 WallaceIT. (https://wallaceit.com.au)
 * @link       https://wallacepos.com
 * @author     Michael B Wallace <micwallace@gmx.com>
 * @since      File available since 18/04/16 4:24 PM
 */
class ExpenseItemsModel extends DbConfig
{

    /**
     * @var array
     */
    protected $_columns = ['id', 'expenseid', 'notes', 'status', 'locationid', 'userid', 'ref', 'dt'];

    /**
     * Init the DB
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $expenseid
     * @param $ref
     * @param $amount
     * @param $notes
     * @param $status
     * @param $locationid
     * @param $userid
     * @param $dt
     * @return bool|string Returns false on an unexpected failure, returns -1 if a unique constraint in the database fails, or the new rows id if the insert is successful
     */
    public function create($expenseid, $ref, $amount, $notes, $status, $locationid, $userid, $dt)
    {
        $sql = "INSERT INTO expenses_items (`expenseid`, `ref`, `amount`, `notes`, `status`, `locationid`, `userid`, `dt`) VALUES (:expenseid, :ref, :amount, :notes, :status, :locationid, :userid, :dt);";
        $placeholders = [":expenseid" => $expenseid, ":ref" => $ref, ":amount" => $amount, ":notes" => $notes, ":status" => $status, ":locationid" => $locationid, ":userid" => $userid, ":dt" => $dt];

        return $this->insert($sql, $placeholders);
    }

    /**
     * @param null $expenseId
     * @param null $locationid
     * @return array|bool Returns false on an unexpected failure or an array of selected rows
     */
    public function get($expenseId = null, $locationid = null)
    {
        $sql = 'SELECT i.*, e.name as expense FROM expenses_items as i RIGHT OUTER JOIN expenses AS e ON i.expenseid=e.id';
        $placeholders = [];
        if ($expenseId !== null) {
            if (empty($placeholders)) {
                $sql .= ' WHERE';
            }
            $sql .= ' i.expenseid =:id';
            $placeholders[':id'] = $expenseId;
        }
        if ($locationid !== null) {
            if (empty($placeholders)) {
                $sql .= ' WHERE';
            } else {
                $sql .= ' AND';
            }
            $sql .= ' i.locationid =:locationid';
            $placeholders[':locationid'] = $locationid;
        }

        return $this->select($sql, $placeholders);
    }

    /**
     * Get a single sale object using its reference.
     * @param $ref
     * @return array|bool Returns false on failure or an array with a single record on success
     */
    public function getByRef($ref)
    {
        $sql = 'SELECT i.*, e.name as expense FROM expenses_items as i RIGHT OUTER JOIN expenses AS e ON i.expenseid=e.id WHERE';
        $placeholders = [];
        if (is_array($ref)) {
            $ref = array_map([$this->_db, 'quote'], $ref);
            $sql .= " i.ref IN (" . implode(', ', $ref) . ");";
        } else if (is_numeric(str_replace("-", "", $ref))) {
            $sql .= " i.ref=:ref;";
            $placeholders[":ref"] = $ref;
        } else {
            return false;
        }
        return $this->select($sql, $placeholders);
    }


    /**
     * @param $id
     * @param $expenseid
     * @param $ref
     * @param $amount
     * @param $notes
     * @param $status
     * @param $locationid
     * @param $userid
     * @param $dt
     * @return bool|int Returns false on an unexpected failure or number of affected rows
     */
    public function edit($id, $expenseid, $ref, $amount, $notes, $status, $locationid, $userid, $dt)
    {

        $sql = "UPDATE expenses_items SET `expenseid`=:expenseid, `ref`=:ref, `amount`=:amount, `notes`=:notes, `status`=:status, `locationid`=:locationid, `userid`=:userid, `dt`=:dt WHERE id=:id;";
        $placeholders = [":id" => $id, "expenseid" => $expenseid, ":ref" => $ref, ":amount" => $amount, ":notes" => $notes, ":status" => $status, ":locationid" => $locationid, ":userid" => $userid, ":dt" => $dt];

        return $this->update($sql, $placeholders);
    }

    /**
     * @param null $id
     * @return bool|int Returns false on an unexpected failure or number of affected rows
     */
    public function remove($id = null)
    {
        $placeholders = [];
        $sql = "DELETE FROM expenses_items WHERE";
        if (is_numeric($id)) {
            $sql .= " `id`=:id;";
            $placeholders[":id"] = $id;
        } else if (is_array($id)) {
            $id = array_map([$this->_db, 'quote'], $id);
            $sql .= " `id` IN (" . implode(', ', $id) . ");";
        } else {
            return false;
        }

        return $this->delete($sql, $placeholders);

    }

}