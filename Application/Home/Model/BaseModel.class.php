<?php
namespace Home\Model;
use Think\Model;

class BaseModel extends Model{
	/** 存储查询字段映射表，在类初始化时创建，采用一维数组的方式记录数据库字段和前台字段的映射关系 */
	protected $fieldMapping;
	
	/** 记录查询的字段，由子类查询方法设置，若为空表示查询所有字段 */
	protected $selectField;
	
	/** 查询条件，由子类调用查询语句前设置
	 *  $selectCond支持多种查询条件的格式
	 *  1、为字符串：直接作为查询条件使用，即'where + $selectCond';
	 *  2、为标准一维数组array(key=>value, key1=>value1)：查询与条件，即'where key = value and key1 = value1';
	 *  3、以上情况混合array(key=>value, string)：查询与条件，即'where key = value and string';
	 *  4、一维数组key为多字段情况array(key1||key2=>value, key=>value1)：查询或条件，即为'where (key1 = value or key2 = value) and key = value1';
	 *  5、不规则格式array(key=>value, array(key1=>value1, key2=>value2))：即为'where key = value and (key1 = value1 or key2 = value2)';
	 *  6、array(key=>value, key1 => array(value1, value2))：即为'where key = value and key1 in (value1, value2)';
	 *  7、当所有的value为null时，会将key=value的条件自动转换成key is null;
	 */
	protected $selectCond;
		
	/** 排序方式，由子类设置，不设置该字段表示默认不排序
	 *  $selectOrder的形式是array (array('数据库字段名', true/false, true/false), array('数据库字段名', true/false, true/false));
	 *  第一个参数是数据库字段名
	 *  第二个参数表示排序方式，true为升序排列，false为降序排列
	 *  第三个参数表示是否需要采用中文排序方式，true表示非中文方式排序，false为中文方式排序
	 *  第二个和第三个参数可以不输入，表示默认采用第一个参数数组字段采用升序非中文方式排序
	 *  若采用二维数组传入多个以上形式的三元组，则表示多字段排序方式，按照字段顺序依次排序
	 */
	protected $selectOrder;
	
	/** 对查询记录进行数量限制，格式为"$start, $count"，
	 *  表示从$start这条记录开始，限制$count条记录，一般需要配合$selectOrder使用
	 *  记录数编号从0开始
	 *  */
	protected $selectLimit;
	
	//新增方法属性
	/** 插入字段，格式为array(key=>value, key1=>value1),
	 *  也可以是多条数据，格式为 array(array(key=>value, key1=>value1),array(key=>value, key1=>value1))
	 *  */
	protected $insertData;
	
	//更新方法属性
	/** 更新的字段和值，格式为array (key => value)，update xx set key = value*/
	protected $updateData;
	
	/** 更新的字段条件，和select格式的条件一样*/
	protected $updateCond;
	
	/** 统计类字段更新和值，格式为array (key => value)，表示key字段增加value个值，value为负表示减*/
	protected $incData;
	
	/** 统计类字段更新的字段条件，和select格式的条件一样*/
	protected $incCond;
	
	
	//删除属性
	/** 删除的字段条件，和select格式的条件一样*/
	protected $deleteCond;
	
	protected $userID;		//存储用户ID
	
	
	//空方法，供子类调用，用来初始化数据库字段和前台字段的对应表
	protected function initMap(){
			
	}
	
	/** 函数名：	parseFields
	 * 输入：		$data 一维数组，形如array ('数据库字段' => value, '前台字段' => value);
	 * 返回值：	一维数组，将所有的输入变成array ('数据库字段' => value)
	 * 说明：		私有方法，该方法自动被insertRecord和updateRecord方法调用，用来做前后台字段统一转成后台字段的处理
	 * */
	private function parseFields($data){
		foreach ($data as $key => $value){
			// 首先判断key是否为前台字段，如果key是前台字段则转成后台字段
			if (in_array($key, $this->fieldMapping)){
				$data[array_search($key, $this->fieldMapping)] = $value;
				unset($data[$key]);
			}
				
			// 如果key不是前台字段，再判断是否为后台字段，如果是后台字段，不做处理，如果不是后台字段，删除
			else if (!array_key_exists($key, $this->fieldMapping)){
				unset($data[$key]);
			}
		}
		return $data;
	}
	
	/** 函数名：	buildDBMap
	 * 输入：		无
	 * 返回值：	一维数组，建立"数据库字段=>前台字段"的映射数组
	 * 说明：		私有方法，该方法自动被selectRecord方法调用，不需要用户处理
	 子类只需要设置$selectField，即其希望查询的数据库字段，自动生成映射关系
	 * */
	private function buildDBMap(){
		if($this->selectField==null){
			$this->selectField=array_keys($this->fieldMapping);
		}
		foreach ($this->selectField as $key => $value)
			$selectMap[$value] = $this->fieldMapping[$value];
		return $selectMap;
	}
	
	/** 函数名：	parseOneArrayOrder
	 * 输入：		array('数据库字段名', true/false, true/false)
	 * 返回值：	排序字符串，如 key asc
	 * 说明：		私有方法，该方法被parseOrder方法调用，解析单字段的排序
	 * */
	private function parseOneArrayOrder($array){
		$result = null;
		if (!is_array($array)){
			$result = $array;
		}
		switch (count($array)){
			case 1: $result = $array[0].' asc ';
			break;
			case 2: if ($array[1] == false)
				$result = $array[0].' desc ';
			else
				$result = $array[0].' asc ';
			break;
			case 3: if ($array[2] == false)
				$result = 'convert('.$array[0].' using gb2312)';
			else
				$result = $array[0];
			if ($array[1] == false)
				$result =$result.' desc ';
			else
				$result = $result.' asc ';
			break;
			default:
				break;
		}
		return $result;
	}
	
	/** 函数名：	parseOrder
	 * 输入：		类似array('数据库字段名', true/false, true/false)的一维或二维数组
	 * 返回值：	排序字符串，如 key asc
	 * 说明：		私有方法，供selectRecord方法调用，解析单字段或多字段的中英文升降序排序
	 * */
	private function parseOrder ($arrayOrder){
		$order = null;		//重新解析排序结果，默认为升序排列，默认为非汉字排列
		if (!is_array($arrayOrder)){
			$order = $arrayOrder;
		}
		else{
			//一维数组情况，原来代码可以不用修改
			if (!is_array($arrayOrder[0]))
				$order = $this->parseOneArrayOrder($arrayOrder);
			else{
				//二维数组情况，可以支持对多个字段排序
				foreach ($arrayOrder as $tmp){
					$parseResult = $this->parseOneArrayOrder($tmp);
					$order = $order.$parseResult.',';
				}
				$order = substr($order, 0, -1);
			}
		}
		return $order;
	}
	
	/** 函数名：	parseCond
	 * 输入：		形如$selectCond的条件格式，支持多种类型的查询、更新条件
	 * 返回值：	where子句
	 * 说明：		私有方法，供selectRecord、updateRecord等方法调用，解析查询或更新条件
	 * */
	private function parseCond ($arrayCond){
		$cond = null;	//重新解析查询条件，将条件为null的进行重新解释
	
		//如果查询条件为字符串，直接赋值给$cond
		if (!is_array($arrayCond)){
			$cond = $arrayCond;
			return $cond;
		}
		else {
			$cond = '';
			foreach ($arrayCond as $key => $value){
				//如果value是数组，需要对数组进行解析，有两种情况
				if (is_array($value)){
					//如果是如 0 => array(Cond => 1, Cond => 2)这种情况解析为 (Cond = 1 or Cond = 2)
					//注意如果是同名的字段有多种情况，会导致后面的值覆盖前面的值，需要对Cond前后加空格来区分
					if (!is_string($key)){
						$cond = $cond.'(';
						foreach ($value as $k => $v){
							if ($v == null)
								$cond = $cond.$k.' is null or ';
							else
								$cond = $cond.$k.'= '.$v.' or ';
						}
						$cond = substr($cond, 0, -4).') and ';
					}
					//如果是如  Cond => array(1, 2)这种情况解析为Cond in (1, 2)
					else{
						$cond = $cond.$key.' in ('.implode(',', $value).') and ';
					}
				}
				//如果value不是数组，就是key=>value的情况，直接判断value是否为null解析为 is null的效果即可
				else{
					//如果key不是字符串，说明整个条件是一个字符串，直接加上
					if(!is_string($key)){
						$cond = $cond.$value.' and ';
					}
					else{
						if ($value === null)
							$cond = $cond.$key.' is null and ';
						else
							$cond = $cond.$key.' = '.$value.' and ';
					}
				}
			}
		}
		$cond = substr($cond, 0, -4);
		return $cond;
	}
	
	
	/** 函数名：	selectRecord
	 * 输入：		$distinct，布尔值，表示是否进行唯一处理，默认不需要输入
	 *			当使用distinct时，一般为获取某ID值，因此和field配合使用，不支持多field的唯一处理，注意！
	 * 返回值：	返回查询后的数组，符合selectCond条件的selectMap字段，并根据selectOrder排序，符合selectLimit的数量
	 * 			没有获取到数据时返回null
	 * */
	protected function selectRecord($distinct = false){
	
		$selectMap = $this->buildDBMap();
		$cond = $this->parseCond($this->selectCond);
		$order = $this->parseOrder($this->selectOrder);
		//查询结果
		$result = $this->field($selectMap)->where($cond)->order($order)->limit($this->selectLimit)->distinct($distinct)->select();
		//清空limit条件，否则在同一个model中调用此方法时会出现limit方法一直存在的现象
		$this->selectLimit = null;
		return $result;
	}
	
	/** 函数名：	insertRecord
	 * 输入：		无
	 * 返回值：	插入成功返回插入记录的主码ID，插入失败返回-1，插入多条数据成功返回第一条数据的主码ID
	 * 说明：		根据insertData的值将数据插入数据库，删除数据中为null的数据字段，在insert时不需要制定该字段，默认为null
	 * 			如果insertData为一维数组，插入一条记录，如果为二维数组，则插入多条数据
	 * */
	protected function insertRecord(){
		if (is_array($this->insertData[0])){ //value是数组，表明是多条记录
			foreach ($this->insertData as $key => $value){
				$this->insertData[$key]	= $this->parseFields($this->insertData[$key]);
				foreach($value as $x => $y)
				{
					if (($y == null) or ($y =='null'))
						unset($this->insertData[$key][$x]);
				}
			}
			$result = $this->addAll($this->insertData);
		}
		else{
			$this->insertData	= $this->parseFields($this->insertData);
			foreach ($this->insertData as $key => $value)
				if (($value == null))
				unset($this->insertData[$key]);
			$result= $this->add($this->insertData);
		}
		if ($result > 0)
			return $result;
		else
			return -1;
	}
		
	/** 函数名：	updateRecord
	 * 输入：		无
	 * 返回值：	更新成功返回更新的记录条数，无更新返回0，更新失败返回false
	 * 说明：		更新数据的基础方法，根据updateCond条件更新updateData数据，updateCond可以和selectCond条件一样的格式
	 * */
	protected function updateRecord(){
		$this->updateData = $this->parseFields($this->updateData);
		$cond = $this->parseCond($this->updateCond);
		$result = $this->where($cond)->setfield($this->updateData);
		return $result;
	}
	
	/** 函数名：	incRecord
	 * 输入：		incData记录新增数据的字段，形如array($key => $value)，针对key字段更新value数值，value为正为增加，为负为减少
	 * 返回值：	更新成功返回更新的记录条数，更新失败或者更新记录数为0返回false
	 * 说明：		更新统计类数据的基础方法，根据incCond条件更新incData数据，incCond可以和selectCond条件一样的格式
	 * */
	protected function incRecord(){
	
		$cond = $this->parseCond($this->incCond);
	
		foreach($this->incData as $key=>$value){
			$result = $this->where($cond)->setInc($key,$value);
		}
		if ($result == 0)
			$result = false;
		return $result;
	}
	
	/** 函数名：	deleteRecord
	 * 输入：		无
	 * 返回值：	统计类更新成功返回true，更新失败返回false
	 * 说明：		删除数据的基础方法，根据deleteCond条件删除数据，deleteCond可以和selectCond条件一样的格式，不允许删除所有记录
	 * 			如果存在数据并删除默认返回true
	 * */
	protected function deleteRecord(){
		if ($this->deleteCond == null)
			return false;
		else{
			$cond = $this->parseCond($this->deleteCond);
			$result = $this->where($cond)->delete();
			return !is_bool($result);
		}
	}
	
	/** 函数名：	countRecord
	 * 输入：		使用selectCond记录查询条件，返回符合条件的记录条数
	 * 返回值：	符合查询条件的记录条数
	 * */
	protected function countRecord(){
		$parsedCond = $this->parseCond($this->selectCond);
		$result = $this->where($parsedCond)->count();
		return $result;
	}
	
}