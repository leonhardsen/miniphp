<?php
/**
 * queryBuilder
 * SQL query builder class
 * @author  Leonardo Ruiz 
 */
class queryBuilder{
	/**
     * @var string Table name
     */
	private $table;

	/**
     * @var array Columns to be returned
     */
	private $lists;

	/**
     * @var array WHERE conditions
     */
	private $where;

	/**
     * @var string BEETWEEN condition
     */
	private $between;

	/**
     * @var string NOT BEETWEEN condition
     */
	private $notbetween;

	/**
     * @var array Columns to ORDER BY
     */
	private $orderby;

	/**
     * @var array Results limit
     */
	private $limit;

	/**
     * Set table attribute
     * @param string $table Table name     
     */
	public function table($table){
		$this->table = $table;
	}

	/**
     * Set lists attribute
     * @param array $array Columns array    
     */
	public function lists($array){		
		$this->lists = $array;
	}

	/**
     * Set where attribute
     * @param string $field Table column
     * @param string $operator Conditional operator
     * @param string $value Column value
     * @param string $concat Type of concatenation: AND or OR
     */
	public function where($field,$operator,$value,$concat = NULL){	
		$array = array($field,$operator,$value,$concat);	
		$this->where[] = $array;
	}

	/**
     * Set between attribute
     * @param string $field Table column     
     * @param array $values Values to compare: begins in values[0] and ends in values[1]
     */
	public function between($field,$values){	
		$array = array($field,$values[0],$values[1]);	
		$this->between = $array;
	}

	/**
     * Set notbetween attribute
     * @param string $field Table column     
     * @param array $values Values to compare: begins in values[0] and ends in values[1]
     */
	public function notbetween($field,$values){	
		$array = array($field,$values[0],$values[1]);	
		$this->notbetween = $array;
	}
	
	/**
     * Set orderby attribute
     * @param string $field Table column     
     * @param string $order Results order: ASC or DESC 
     */
	public function orderby($field,$order = 'ASC'){	
		$array = array($field,$order);	
		$this->orderby[] = $array;
	}

	/**
     * Set limit attribute
     * @param string $limit Results limit
     * @param string $offset Results to skip
     */
	public function limit($limit,$offset = NULL){	
		$array = array($limit,$offset);	
		$this->limit = $array;
	}

	/**
     * Make WHERE syntax     
     * @return string WHERE syntax
     */
	private function makeWhere(){
		$sql_where = '';
		$first_where = 0;
		foreach($this->where as $where){
			if($first_where == 0){
				$sql_where .= ' WHERE ';
			}else{
				if($where[3] == 'or'){
					$sql_where .= ' OR ';
				}else{
					$sql_where .= ' AND ';
				}
			}
			$sql_where .= $where[0].$where[1].$where[2];
			$first_where++;
		}
		return $sql_where;
	}

	/**
     * Make SELECT syntax     
     * @return string SELECT syntax
     */
	public function get(){		
		$sql = 'SELECT ';
		if(empty($this->lists)){
			$sql .= '*';
		}else{
			foreach($this->lists as $list){
				$sql .= $list.', ';
			}
			$sql = substr($sql,0,-2);
		}
		$sql .= ' FROM '.$this->table;		
		if(!empty($this->where)){
			$sql .= $this->makeWhere();
		}		
		if(!empty($this->between)){
			$sql .= ' WHERE '.$this->between[0].' BETWEEN '.$this->between[1].' AND '.$this->between[2];
		}		
		if(!empty($this->notbetween)){
			$sql .= ' WHERE '.$this->notbetween[0].' NOT BETWEEN '.$this->notbetween[1].' AND '.$this->notbetween[2];
		}		
		if(!empty($this->orderby)){
			$first_order = 0;
			foreach($this->orderby as $orderby){
				if($first_order == 0){
					$sql .= ' ORDER BY ';
				}
				$sql .= $orderby[0].' '.$orderby[1].', ';
				$first_order++;
			}
			$sql = substr($sql,0,-2);
		}		
		if(!empty($this->limit)){
			if($this->limit[1] == NULL){
				$sql .= ' LIMIT '.$this->limit[0];
			}else{
				$sql .= ' LIMIT '.$this->limit[1].', '.$this->limit[0];
			}
		}		
		return $sql;
	}

	/**
     * Make INSERT syntax     
     * @return string INSERT syntax
     */
	public function insert($data){
		$sql = 'INSERT INTO '.$this->table.' (';
		$fields = '';
		$values = '';
		foreach($data as $f => $d){
			$fields .= $f.', ';
			$values .= '"'.$d.'", ';
		}
		$fields = substr($fields,0,-2);
		$values = substr($values,0,-2);
		$sql .= $fields.') VALUES ('.$values.')';		
		return $sql;
	}

	/**
     * Make UPDATE syntax     
     * @return string UPDATE syntax
     */
	public function update($data){
		$sql = 'UPDATE '.$this->table.' SET ';		
		foreach($data as $f => $d){
			$sql .= $f.'="'.$d.'", ';
		}
		$sql = substr($sql,0,-2);		
		if(!empty($this->where)){
			$sql .= $this->makeWhere();
		}		
		return $sql;
	}

	/**
     * Make DELETE syntax     
     * @return string DELETE syntax
     */
	public function delete(){
		$sql = 'DELETE FROM '.$this->table;				
		if(!empty($this->where)){
			$sql .= $this->makeWhere();
		}		
		return $sql;
	}
}