<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

/**
 * iMenus Constants Helper
 *
 * @package		iMenus
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Patrick
 */
define('CATEGORIES_TABLE', 'Category');
define('ITEMS_TABLE', 'Item');
define('PARENTS_TABLE', 'ItemParents');

define('ITEM_FIELDS', 'ID, CategoryID, Name, ShortDescription, LongDescription, Price, Type, ImageSmall, ImageMedium, ImageLarge');

define('ITEMS_TYPE_ITEM', 0);
define('ITEMS_TYPE_MEAL', 1);

define('ROOT_CATEGORY', '(Top Level)');

define('ACCESS_DENIED_MSG', 'You do not have permission to access this!');
define('ACCESS_DENIED', 'Access Denied');

define('ITEM_IMAGE_SMALL', 'imageSmall');
define('ITEM_IMAGE_MEDIUM', 'imageMedium');
define('ITEM_IMAGE_LARGE', 'imageLarge');
?>