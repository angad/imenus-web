<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

class POS_model extends CI_Model {

	function __construct() {
		parent::__construct();
        $this->load->database();
        $this->load->helper('globals');
	}
    
    function getActiveOrders($orgID) {
        // 0-items-defined safeguard
        if (count($this->db->query('SELECT 1 FROM '.ORDERS_TABLE)->result_array()) == 0)
            return array();
        return $this->db->query(
        'SELECT O.ID, O.OrganizationID, O.Remarks, O.TableNumber, SUM(OI.Quantity * I.iQty) AS ItemsOrdered, SUM(OI.Quantity * I.Price) AS TotalBill
        FROM '.ORDERS_TABLE.' O
            INNER JOIN '.ORDERITEMS_TABLE.' OI ON O.ID = OI.OrderID
            INNER JOIN (
        
                SELECT ID, 1 AS iQty, Price
                FROM '.ITEMS_TABLE.'
                WHERE TYPE = '.ITEMS_TYPE_ITEM.'
                UNION
                SELECT MEAL.ID, SUM( MEALITEM.ItemQuantity ) AS iQty, MEAL.Price
                FROM '.ITEMS_TABLE.' MEAL
                INNER JOIN '.PARENTS_TABLE.' MEALITEM ON MEAL.ID = MEALITEM.ParentID
                AND MEAL.Type = '.ITEMS_TYPE_MEAL.'
                ) I ON OI.ItemID = I.ID
        WHERE O.OrganizationID = ?
        GROUP BY O.ID', array($orgID))->result_array();
    }

    function getOrderItemETAs($orderID) {
        return $this->db->query(
        'SELECT O.ID AS OrderID, OI.ID AS OrderItemID, I.ID AS ItemID, I.Name, OI.Quantity * I.iQty AS Quantity,
          I.Duration, UNIX_TIMESTAMP( OI.Timestamp ) AS Timestamp, UNIX_TIMESTAMP(OI.Started) AS Started,
          IF(OI.Started, GREATEST(I.Duration * OI.Quantity - (NOW() - OI.Started), 0),
           SUM( GREATEST( I2.Duration * OI2.Quantity - IF(OI2.Started, NOW( ) - OI2.Started, 0), 0) )) AS ETA
         FROM (
             '.ORDERS_TABLE.' O
             INNER JOIN '.ORDERITEMS_TABLE.' OI ON O.ID = OI.OrderID
             INNER JOIN (
                     SELECT ID, Name, Duration, 1 as iQty, ID AS iID FROM '.ITEMS_TABLE.' Where Type = '.ITEMS_TYPE_ITEM.'
                 UNION
                     SELECT MEAL.ID, MEALITEM.Name, (SELECT MAX(PARENTS.ItemQuantity * ITEM.Duration) FROM '.PARENTS_TABLE.' PARENTS INNER JOIN '.ITEMS_TABLE.' ITEM ON PARENTS.ItemID = ITEM.ID
		              WHERE PARENTS.ParentID = MEAL.ID) AS Duration, IP.ItemQuantity as iQty, MEALITEM.ID AS iID
                      FROM '.ITEMS_TABLE.' MEAL INNER JOIN '.PARENTS_TABLE.' IP ON MEAL.ID = IP.ParentID AND MEAL.Type = '.ITEMS_TYPE_MEAL.' INNER JOIN
                       '.ITEMS_TABLE.' MEALITEM ON IP.ItemID = MEALITEM.ID
             ) I ON OI.ItemID = I.ID
         )
         INNER JOIN (
             '.ORDERS_TABLE.' O2
             INNER JOIN '.ORDERITEMS_TABLE.' OI2 ON O2.ID = OI2.OrderID
             INNER JOIN (
                     SELECT ID, Duration FROM '.ITEMS_TABLE.' WHERE Type = '.ITEMS_TYPE_ITEM.'
                 UNION
                     SELECT MEAL.ID, SUM(MEALITEM.Duration * IP.ItemQuantity) AS Duration FROM '.ITEMS_TABLE.' MEAL
                      INNER JOIN '.PARENTS_TABLE.' IP ON MEAL.ID = IP.ParentID AND MEAL.Type = '.ITEMS_TYPE_MEAL.' INNER JOIN '.ITEMS_TABLE.' MEALITEM ON IP.ItemID = MEALITEM.ID GROUP BY MEAL.ID
             ) I2 ON OI2.ItemID = I2.ID
         ) ON OI.Timestamp >= OI2.Timestamp
             AND O.OrganizationID = O2.OrganizationID
         WHERE O.ID = ?
         GROUP BY OI.ID, I.ID, I.iID', array($orderID))->result_array();
    }
    
    
    function getOrderItemDetails($orderID) {
        return $this->db->query('SELECT OI.OrderID, OI.ID AS OrderItemID, OI.ItemID, OI.Quantity, OI.Remarks, OI.Timestamp, OI.Started, I.'.str_replace(', ', ', I.', ITEM_FIELDS).'
                                 FROM '.ORDERITEMS_TABLE.' OI INNER JOIN '.ITEMS_TABLE.' I ON OI.ItemID = I.ID AND OI.OrderID = ?', array($orderID))->result_array();
    } 
    
    function removeOrder($orderID) {
        $this->db->query('DELETE O, OI, OIF FROM '.ORDERS_TABLE.' O LEFT JOIN '.ORDERITEMS_TABLE.' OI ON O.ID = OI.OrderID LEFT JOIN '.ORDERITEMFEATURES_TABLE.' OIF ON OI.ID = OIF.OrderItemID WHERE O.ID = ?', array($orderID));
    }
}