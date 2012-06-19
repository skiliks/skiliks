<?php
/**
 * COciTableSchema class file.
 *
 * @author Ricardo Grana <rickgrana@yahoo.com.br>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2011 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * COciTableSchema represents the metadata for a Oracle table.
 *
 * @author Ricardo Grana <rickgrana@yahoo.com.br>
 * @version $Id: COciTableSchema.php 3515 2011-12-28 12:29:24Z mdomba $
 * @package system.db.schema.oci
 */
class COciTableSchema extends CDbTableSchema
{
	/**
	 * @var string name of the schema (database) that this table belongs to.
	 * Defaults to null, meaning no schema (or the current database).
	 */
	public $schemaName;
}
