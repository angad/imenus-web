<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * iMenus Constants and Global Functions Helper
 *
 * @package		iMenus
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Patrick
 */
define('CATEGORIES_TABLE', '`Category`');
define('ITEMS_TABLE', '`Item`');
define('PARENTS_TABLE', '`ItemParents`');
define('ITEMFEATURES_TABLE', '`ItemFeatures`');
define('FEATURES_TABLE', '`Feature`');
define('ORDERS_TABLE', '`Order`');
define('ORDERITEMS_TABLE', '`OrderItem`');
define('ORDERITEMFEATURES_TABLE', '`OrderItemFeatures`');
define('ORGANIZATION_TABLE', 'Organization');
define('MENU_TABLE', 'Menu');
define('THEMEVALUES_TABLE', 'ThemeValues');
define('THEMEVALUEOPTIONS_TABLE', 'ThemeValueOptions');

define('ITEM_FIELDS', 'ID, CategoryID, Name, ShortDescription, LongDescription, Price, Duration, Type, ImageSmall, ImageMedium, ImageLarge, TSV1');
define('FEATURE_FIELDS', 'ID, Name, Type, MenuID, MaxValue, Icon, StringValues, Fixed');

define('ITEMS_TYPE_ITEM', 0);
define('ITEMS_TYPE_MEAL', 1);

define('FEATURES_TYPE_NUMERIC', 0);
define('FEATURES_TYPE_OPTIONS', 1);

define('TSV_TYPE_CATEGORY', 0);
define('TSV_TYPE_ITEM', 1);
define('THEMEVALUE_LABEL_PREFIX', 'Theme Option');

define('ROOT_CATEGORY', '(Top Level)');

define('ACCESS_DENIED_MSG', 'You do not have permission to access this!');
define('ACCESS_DENIED', 'Access Denied');

define('ITEM_IMAGE_SMALL', 'imageSmall');
define('ITEM_IMAGE_MEDIUM', 'imageMedium');
define('ITEM_IMAGE_LARGE', 'imageLarge');

if (!function_exists('isAJAX')) {
    function isAJAX() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
}

/**
 * Escaping function for JavaScript input
 * Works like mysql_real_escape_string, except without the DB connection
 * Uses mysql_real_escape_string for database inputs; it's safer
 * 
 * @param   string
 * @return  string
 */
if (!function_exists('escape')) {
    function escape($str) {
        return strtr($str, array( "\x00" => '\x00', "\n" => '\n', "\r" => '\r', '\\' => '\\\\', "'" => "\'", '"' => '\"', "\x1a" => '\x1a'));
    }
}