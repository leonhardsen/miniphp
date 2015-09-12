<?php
/**
 * mysqliInterface
 * Interface class for mysqli
 * @author  Leonardo Ruiz 
 */
class mysqliInterface{
	/**
     * @var object mysqli connection
     */
	private $mysqli;

	/**
     * @var object mysqli statement
     */
	private $stmt;

	/**
     * Open mysqli connection       
     */
	private function open(){
		$this->mysqli = new mysqli(DB_SRVR, DB_USER, DB_PASS, DB_NAME);
	}

	/**
     * Close mysqli connection       
     */
	private function close(){
		$this->stmt->close();
		$this->mysqli->close();
	}

	/**
     * Prepare and execute the statement   
     * @param string $sql Sql syntax
     */
	public function execute($sql){		
		$this->open();		
		$this->stmt = $this->mysqli->prepare($sql);
		$this->stmt->execute();
	}

	/**
     * Execute select query (format results) 
     * @param string $sql Sql syntax
     */
	public function select($sql){
		$this->execute($sql);
		$metas = $this->stmt->result_metadata();
		$model = new stdClass();
		while($field = $metas->fetch_field()){
			$f_name = $field->name;
			$model->$f_name = NULL;	
			$fields[] = $f_name;
			$fields_reference[] = &$model->$f_name;
		}
		$this->stmt->store_result();
		call_user_func_array(array($this->stmt, 'bind_result'), $fields_reference);
		$result = array();
		while($this->stmt->fetch()){	
			$row = new stdClass();
			foreach($fields as $fd){
				$row->$fd = $model->$fd;
			}
			$result[] = $row;
		}
		return $result;
		$this->close();
	}

	/**
     * Execute insert query (format results) 
     * @param string $sql Sql syntax
     */
	public function insert($sql){
		$this->execute($sql);
		return $this->mysqli->insert_id;
		$this->close();
	}

	/**
     * Execute insert query (format results) 
     * @param string $sql Sql syntax
     */
	public function update($sql){
		$this->execute($sql);
		return $this->mysqli->affected_rows;
		$this->close();
	}

	/**
     * Execute insert query (format results) 
     * @param string $sql Sql syntax
     */
	public function delete($sql){
		$this->execute($sql);
		return $this->mysqli->affected_rows;
		$this->close();
	}


}

?>