<?php
namespace Graphpish\Sql;

class SqlException extends \Exception {
	public function __construct(\PDO $conn, $msg='') {
		parent::__construct('SQL-Error '.$msg.': '.json_encode($conn->errorInfo()));
	}
}
