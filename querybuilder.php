<?

class QueryBuilder	{
    private $conn;
	private $type;
	private $table;
	private $query;
	private $columns;
	public $aJoin;
	public $aWhere;
	public $aGroupBy;
	public $aOrderBy;
	public $aLimitBy;
	public $aOffsetBy;
	
     public function __construct($conn) {
        $this->conn = $conn;
	}
	

    public function getData() {
		//echo $this->query;
		if ($result=mysqli_query($this->conn,$this->query)) {
       
           $aObj = array();
            while ($obj = $result->fetch_object()) {
              try { 
              if (is_object($obj)) {
                  array_push($aObj,$obj);
               }
                } catch (Exception $e) {}
            }

            return $aObj;
        }
	}

    /**
    *
    * build : builds the query string -  currently select available
    *
    */

	public function build() {
	
		if ($this->type == 'select') { 
			$this->query = 'select ';
			if ($this->columns != '') {
				$this->query .= $this->columns . ' ';
			}
			
			$this->query .= 'from ' . $this->table . ' ';
			
			//join
            if (count($this->aJoin) > 0) {
			   
                $len = count($this->aJoin);
                for ($i=0;$i<$len;$i++) {
                    $tJoin = $this->aJoin[$i];
                    
                     $this->query .= ' join ';
                    
                    if ($tJoin->operator == 'IN') {	
                        $this->query .= "$tJoin->name $tJoin->operator ($tJoin->value) ";
                    } else {
                        $this->query .= "$tJoin->joinTable on  $tJoin->column1 $tJoin->operator $tJoin->column2 ";
                    }
					
                }
            }
			
			//build where
			$this->query .= 'where ';
			$len = count($this->aWhere);
			$i = 0;
			$append = "";

				for ($i=0;$i<$len;$i++) {
					$tWhere = $this->aWhere[$i];
					$and = "";
					$append = "";
				switch ($tWhere->operator) {
					case "IN":
						$append = "$tWhere->name $tWhere->operator ($tWhere->value) ";
						break;
					case "BETWEEN":
						$tValueArray = explode (",", $tWhere->value);//(date_field BETWEEN '2010-01-30 14:15:55' AND '2010-09-29 10:15:55')
						$append = "$tWhere->name $tWhere->operator '$tValueArray[0]' and '$tValueArray[1]' ";
						break;

					case "=":
						$append = "$tWhere->name $tWhere->operator $tWhere->value ";
						break;

					default:
						$append = "$tWhere->name $tWhere->operator $tWhere->value ";
				
				}
				
				if ($i > 0 ) {
					$and = " and ";
				}

				if ($i == 0 ) {
					$and = "";
				}
				$this->query = $this->query . $and . $append;
			}
  //and patch
    $testAnd = substr($this->query , -5);
      
      if ($testAnd == " and ") {
       $this->query =  substr($this->query, 0, -5);
      }

			//build group by
			if ($this->aGroupBy != "") {
				$this->query .= 'group by ' . $this->aGroupBy . ' ' ;
			}

			//build order by
			if ($this->aOrderBy != "") {
				$this->query .= 'order by ' . $this->aOrderBy . ' ' ;
			}

			if ($this->aLimitBy != "") {
				$this->query .= 'limit ' . $this->aLimitBy . ' ' ;
			}

			
			if ($this->aOffsetBy != "") {
				$this->query .= 'offset ' . $this->aOffsetBy . ' ' ;
			}
           
		}

        return $this->getData();
	
	}
	
    /**
    *
    * run : runs the build functionality to create the query string and execute
    *
    */
	public function run() {
		return $this->build();
	}
	
    /**
    *
    * select : creates the select function
    *
    */
	public function select($table)	{
		$this->type = __FUNCTION__;
		$this->table = $table;
		return $this;
	}
	
    /**
    *
    * update :  creates the update query
    *
    */
	public function update($table)	{
		$this->type = "update";
		$this->table = $table;
		return $this;
	}
	
   /*
    *
    * columns :  set the colums used for select/update
    *
    */
	public function columns($columns)	{
		$this->columns = $columns;
		return $this;
	}
   /*
    *
    * join :  sets the where clause from array of WhereBuilder Objects
    *
    */	
	public function join($thisJoin)	{
		$this->aJoin = $thisJoin;
		return $this;
	}
   /*
    *
    * where :  sets the where clause from array of WhereBuilder Objects
    *
    */	
	public function where($thisWhere)	{
		$this->aWhere = $thisWhere;
		return $this;
	}

	/*
    *
    * groupBy :  sets the groupBy List
    *
    */	
	public function groupBy($thisGroup)	{
		$this->aGroupBy = $thisGroup;
		return $this;
	}

		/*
    *
    * orderBy :  sets the groupBy List
    *
    */	
	public function orderBy($thisOrder)	{
		$this->aOrderBy = $thisOrder;
		return $this;
	}

			/*
    *
    * limitBy :  sets the groupBy List
    *
    */	
	public function limitBy($thisLimit)	{
		$this->aLimitBy = $thisLimit;
		return $this;
	}

	/*
    *
    * offsetBy :  sets the groupBy List
    *
    */	
	public function offsetBy($thisOffset)	{
		$this->aOffsetBy = $thisOffset;
		return $this;
	}

	public function getQueryString()	{
		return $this->query;
	}


}

/*
*
* WhereBuilder :  Creates a WhereBuilder Instance
*
*/	
class whereBuilder {
	public $name, $value, $operator;
	
     public function __construct($name, $value, $operator) {
       	$this->name = $name;
		$this->value = $value;
		$this->operator = $operator;
		return $this;
    }

}

/*
*
* JoinBuilder :  Creates a WhereBuilder Instance
*
*/
class JoinBuilder {
	public $joinTable, $column1, $column2, $operator;
	
	 public function __construct($joinTable,$column1, $column2, $operator) {
		$this->joinTable = $joinTable;
		$this->column1 = $column1;
		$this->column2 = $column2;
		$this->operator = $operator;
		return $this;
	}
}


?>