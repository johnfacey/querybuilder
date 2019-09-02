<?

class ORMSchema {
	public $field = "";
}

class ORM {

	private $schema = "";
	private $primary_key_name = "";
    private $table = "";
	function __construct() {
		return $this;
	}

	public function init($table) {
		$this->table = $table;
		$schema = $this->getSchema($table);
		for ($i=0;$i<count($schema);$i++) {
			$value = $schema[$i]->field;
			$this->$value = "";
		}
		$this->schema = $schema;
		return $this;
	}

	public function setPK($key_name) {
		$this->primary_key_name = $key_name;
	}

	public function deleteSchema() {
		//unset($this->schema);
		//unset($this->primary_key_name);
		//unset($this->primary_key_value);
		//return $this;
	}

	public function getSchema($table) {
		
		global $conn;
		$query = "DESCRIBE $table";
		$result=mysqli_query($conn,$query);
		$num=mysqli_num_rows($result);
		
		$items = Array();
		
		for ($i=0;$i<$num;$i++) {
			$thisItem = new ORMSchema();
			$result->data_seek($i);
			$thisItem = $this->getSchemaRow($result);
			array_push($items,$thisItem);
		}
		return $items;
	}

	public function getSchemaRow($result) {
		$row = $result->fetch_assoc();
		$thisDataRow = new ORMSchema();
		$thisDataRow->field = $row["Field"];
		return $thisDataRow;
	}

    public function getVar($varName){
       return $this->$varName;
    }

    public function setVar($varName,$value){
	  if ($value == null || $value == "null") {

	  } else {
		  
      	$this->$varName = str_replace("'", "", $value);
	  }
    }

    public function getData($result) {
		$row = $result->fetch_assoc();
		for ($i=0;$i<count($this->schema);$i++) {
			$value = $this->schema[$i]->field;
			$this->$value = $row[$value];
		}
		return $this;
	}

	/**
	 * @param $result
	 * @return ORM
     */
	public function getDataRow($result) {
		$row = $result->fetch_assoc();
		$length = count($this->schema);

		$thisDataRow = new ORM();

		for ($i=0;$i<$length;$i++) {
			$value = $this->schema[$i]->field;
			$thisDataRow->$value = $row[$value];
		}
		return $thisDataRow;

	}

	public function commit_API() {
		if ($this->primary_key_name == "") {
			$primary_key_name = $this->schema[0]->field;
		} else {
			$primary_key_name = $this->primary_key_name;
		}
		
		if ($this->${primary_key_name} > 0) {
		 	$this->commit(); 	
		} else {
		 	$this->insert();
		}
		return $this;
	}

	public function insert() {
		global $conn;

		$length = count($this->schema);
		$schemaColumns = array();
		$schemaValues = array();

		for ($i=0;$i<$length;$i++) {
			$value = $this->schema[$i]->field;
			array_push($schemaColumns,$value);
			array_push($schemaValues,"'".$this->$value."'");
		}
		$schemaColumns = implode(",",$schemaColumns);
		$schemaValues = implode(",",$schemaValues);

		$query = "INSERT INTO $this->table ($schemaColumns) VALUES ($schemaValues)";
		
		$result=mysqli_query($conn,$query);
	
	}

	/**
	 * commit: saves this object data properties
     */
	public function commit() {
		global $conn;

		if ($this->primary_key_name == "") {
			$primary_key_name = $this->schema[0]->field;
		} else {
			$primary_key_name = $this->primary_key_name;
		}
		$primary_key_value = $this->$primary_key_name;

		$length = count($this->schema);
		$schemaString = array();

		for ($i=0;$i<$length;$i++) {
			$value = $this->schema[$i]->field;
			$valueData = "'".$this->$value."'";
			if ($primary_key_name != $value) {
				global ${$value};
					//if (isset(${$value})) {
					array_push($schemaString,"$value = $valueData");
					//}
			}
		}

		$schemaString = implode(",",$schemaString);
		
		$query = "update $this->table set $schemaString where $primary_key_name = '$primary_key_value'";
		mysqli_query($conn,$query);

	}
}
?>