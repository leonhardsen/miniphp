<?php 
/**
 * model
 * Model class for MVC applications (integration for queryBuilder and mysqliInterface classes)
 * @author  Leonardo Ruiz 
 */
class model{
	/**
     * @var string Table primary key name
     */
	protected $primary_key = 'id';

	/**
     * Create and set object
     * @param string $id Index of table row     
     */
	public function __construct($id = NULL){
		if($id != NULL && is_numeric($id)){
			$obj = $this->find($id);
			$this->setObject($obj);			
		}else{
			$obj = $this->any();
			$this->setEmptyObject($obj);	
		}
	}

	/**
     * Set object based in a table row
     * @param object $obj Object to be set
     */
	public function setObject($obj){
		$obj_vars = get_object_vars($obj[0]);
		foreach($obj_vars as $obj_ref => $obj_var){
			$this->$obj_ref = $obj_var;
		}
	}

	/**
     * Set a empty object based in a table row
     * @param object $obj Object to be set
     */
	public function setEmptyObject($obj){
		$obj_vars = get_object_vars($obj[0]);
		foreach($obj_vars as $obj_ref => $obj_var){
			$this->$obj_ref = '';
		}
	}

	/**
     * Find a table row by the primary key
     * @param string $id Index to search
     */
	public function find($id){
		$qb = new queryBuilder();
		$qb->table($this->table);
		$qb->where($this->primary_key,'=',$id);
		$sql = $qb->get();
		$interface = new mysqliInterface();
		$result = $interface->select($sql);
		return $result;
	}

	/**
     * Return any table row     
     */
	public function any(){
		$qb = new queryBuilder();
		$qb->table($this->table);
		$qb->limit('1','0');
		$sql = $qb->get();
		$interface = new mysqliInterface();
		$result = $interface->select($sql);
		return $result;
	}

	/**
     * Return all table rows (deleted = 0)
     */
	public function all(){
		$qb = new queryBuilder();
		$qb->table($this->table);		
		$qb->where('deleted','=','0');
		$sql = $qb->get();		
		$interface = new mysqliInterface();
		$result = $interface->select($sql);
		return $result;
	}

	/**
     * Create a new row
     * @param string $primary_key_name primary key name
     */
	private function newSave($primary_key_name){
		$qb = new queryBuilder();
		$qb->table($this->table);	
		$data = $this->formatData();
		$sql = $qb->insert($data);		
		$interface = new mysqliInterface();
		$lastId = $interface->insert($sql);		
		$this->$primary_key_name = $lastId;
		return $lastId;
	}

	/**
     * Update a existing row
     * @param string $primary_key_name primary key name
     */
	private function updateSave($primary_key_name){
		$qb = new queryBuilder();
		$qb->table($this->table);
		$qb->where($this->primary_key,'=',$this->$primary_key_name);
		$data = $this->formatData();
		$sql = $qb->update($data);
		$interface = new mysqliInterface();
		$affected = $interface->update($sql);
		return $affected;
	}

	/**
     * Create a array with attributes and values
     * @return array $data attributes and values array
     */
	private function formatData(){
		$data = array();
		$obj_vars = get_object_vars($this);
		foreach($obj_vars as $obj_ref => $obj_var){
			if($obj_ref != 'table' && $obj_ref != 'primary_key' && $obj_ref != 'id'){
				$data[$obj_ref] = $obj_var;
			}
		}
		return $data;
	}

	/**
     * Save object
     */
	public function save(){
		$primary_key_name = $this->primary_key;	
		if($this->$primary_key_name != ''){				
			$return = $this->updateSave($primary_key_name);
		}else{			
			$return = $this->newSave($primary_key_name);
		}
		return $return;
	}

	/**
     * Delete object
     */
	public function delete(){
		$primary_key_name = $this->primary_key;
		$qb = new queryBuilder();
		$qb->table($this->table);
		$qb->where($this->primary_key,'=',$this->$primary_key_name);
		$sql = $qb->delete();
		$interface = new mysqliInterface();
		$affected = $interface->delete($sql);
		return $affected;
	}

	/**
     * Upload files     
     * @param array $files $files data
     */
	public function uploadFiles($files){		
		$return = array();
		$keys = array_keys($files);
		$i = 0;
		foreach($files as $file){			
			move_uploaded_file($file['tmp_name'], $this->$upload_dir.$file['name']);
			$return[$keys[$i]] = $file['name'];
			$i++;
		}
		return $return;
	}

	/**
     * Set $files values to object attributes
     * @param array $files $files data
     */
	public function setFiles($files){
		foreach($files as $key => $file){			
			if($file != ''){
				$this->$key = $file;
			}
		}
	}

	/**
     * Set $post values to object attributes
     * @param array $post $post data
     * @param array $files $files data
     */
	public function setPost($post,$files = NULL){		
		if(!empty($files)){
			$uploaded_files = $this->uploadFiles($files);
			$this->setFiles($uploaded_files);
		}
		foreach($post as $key => $value){
			if(isset($this, $this->$key)){
				$this->$key = $value;
			}			
		}		
	}

}

?>