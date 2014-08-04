<?php
class DBHandler
{
	private $DBConfig = array();
// 	private $gameuid = NULL;
	private $conn = NULL;
	private $option = array();
	private $pconnect = False;
	private $charset  = 'utf8';
	
	public function __construct($table,$gameuid,$server) {
			self::initialize($table,$gameuid,$server);
	}
	
	private function initialize($table,$gameuid,$server)
	{
		$this->DBConfig['host'] = DB_HOST;
		$this->DBConfig['username'] = DB_USER;
		$this->DBConfig['password'] = DB_PWD;
		$this->DBConfig['dbname']   = DB_NAME;           //逻辑服的数据库
	}
	public function execute($sql) {
		try {
			$connection = $this->connect ();
			return $connection->query ( $sql );
		} catch ( Exception $e ) {
			$this->writeSqlError ( $sql, $e );
		}
	}
	
	/**
	 +----------------------------------------------------------
	 * 连接数据库，获得句柄
	 +----------------------------------------------------------
	 * @return MooMySQL
	 +----------------------------------------------------------
	 */
	private function connect()
	{
		if (isset($this->conn)){
			return $this->conn;
		}

		$this->conn = $this->getDriver();
		$this->conn->connect
					(
						$this->DBConfig['host'],          //DB Server
						$this->DBConfig['username'],      //DB User
						$this->DBConfig['password'],      //DB Password
						$this->DBConfig['dbname'],        //DB Name
						$this->pconnect,                  //Pconnect ?
						$this->charset                    //Charset
					);
		
		return $this->conn;
	}
	
	/**
	 +----------------------------------------------------------
	 * 取得数据库驱动
	 +----------------------------------------------------------
	 * @return MooMySQL
	 +----------------------------------------------------------
	 */
	private function getDriver()
	{
		$driver = $this->DBConfig['driver'];
		
		switch ($driver)
		{
			case 'Mysql':
				require_once FRAMEWORK . 'database/drivers/MySQLDriver.class.php';
				$instance = new MooMySQL();
				break;
			default:
				require_once FRAMEWORK . 'database/drivers/MySQLDriver.class.php';
				$instance = new MySQLDriver();
				break;
		}
		
		return $instance;
	}
	
	public function getOne($sql)
	{
		try {
			return $this->connect ()->getOne ( $sql );
		} catch ( Exception $e ) {
			$this->writeSqlError ( $sql, $e );
		}
	}
	public function getAll($sql)
	{
		try {
			return $this->connect ()->getAll ( $sql );
		} catch ( Exception $e ) {
			$this->writeSqlError ( $sql, $e );
		}
	}
	
	public function getTable()
	{
		return $this->DBConfig['table'];
	}

	public function writeSqlError($sql, $e) {
		$fileName = date ( "Y-m-d", time () ) . "sqlerror.sql";
		$temtime = date ( "Y-m-d H:i:s", time () );
		$strAdd = "#[$temtime]\n";
		file_put_contents ( PATH_ROOT . "/log/$fileName", $strAdd . $e . $sql."\n", FILE_APPEND );
	}
}
?>