<?php

  require_once('comp/calendar/classes/tc_calendar.php');

function getMapValue($map, $key, $defaultValue = null) {
  $value = $defaultValue;
  if (($map != null) and ($key != null) and isset($map[$key])) {
    $value = $map[$key];
  }

  return $value;

}

function isIEBrowser() {
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  return preg_match('/MSIE/i', $u_agent);
}

/********************************************************************/
/* Field Types, used to define Objects stared in the data base      */
/********************************************************************/
  /*
   FieldType's have attributes, an attribute is a set method that
   returns $this.  so that you can create a Field like so:
   new FTDate()->size(10)->nullable(true)->unique(true)
   and it would return an FTDate object
   */
  class FieldType {
    private $unique = false;
    private $nullable = true;
    private $sqlTypeString = 'text';
    private $defaultValue = null;
    private $valueQuoted = false;

    public function __construct($sqlType = null, $quoted = false) {
      $this->sqlTypeString = $sqlType;
      $this->valueQuoted = $quoted;
    }

    protected function setSQLTypeString($str) {
      $this->sqlTypeString = $str;
    }

    public function getSQLTypeString() {
       return $this->sqlTypeString;
    }

    public function isPrimative() {
      return true;
    }

    #attributes
    public function unique($val = true) {
      $this->unique = $val;
      return $this;
    }
    public function nullable($val = true) {
      $this->nullable = $val;
      return $this;
    }
    public function defaultValue($val = null) {
      $this->defaultValue = $val;
      return $this;
    }
    
    public function isUnique() {
      return $this->unique;
    }
    public function isNullable() {
      return $this->nullable;
    }
    public function getDefaultValue() {
      return $this->defaultValue;
    }
    public function isSQLValueQuoted() {
      return $this->valueQuoted;
    }
    #TODO maybe add size?
    public function getHtmlField($name = null) {
       return '<input type="text" name="' . $name . '"/>';
    }

    public function toValue($v, $odb = null) {
      return $v;
    }

    /* used to convert the value to a sql related value */
    public function toSQLValue($v) {
      return $v;
    }
  }


  class FTInt extends FieldType {
    public function __construct() {
      parent::__construct('int');
    }
  }

  # this is a special case field type that is used for ids
  class FTID extends FieldType {
    public function __construct() {
      parent::__construct('int not null auto_increment, primary key(id)');
      parent::unique(false);
      parent::nullable(true);  
    }
    #attributes over ridden to ignore the setting them
    public function unique($val = true) {
      return $this;
    }
    public function nullable($val = true) {
      return $this;
    }
    public function defaultValue($val = null) {
      return $this;
    }
  }


  class FTString extends FieldType {
    private $len;

    public function __construct($len = null) {
      parent::__construct('text', true);
      if ($len != null) {
        parent::setSQLTypeString("varchar($len)");
      }

      $this->len = $len;
    }

    public function getLength() {
      return $this->len;
    }
  }

  /* models a list of string items in the database
     seperated by a ;
  */
  class FTStringList extends FTString {
    public function __construct() {
      parent::__construct(null);
    }

    /* value should return an array of strings */
    public function toValue($v, $odb = null) {
      return $v;
    }

    /* value should be converted from an array to a string delim by ; */
    public function toSQLValue($v) {
      $str = null;
      if (is_array($v)) {
        $str = join(';', $v);
      } else if (is_string($v)) {
        $str = $v;
      }

      return $str;
    }
  }

  class FTSecret extends FTString {
    public function __construct() {
      parent::__construct(30);
    }
    #attributes over ridden to ignore the setting them
    public function unique($val = true) {
      return $this;
    }
    public function nullable($val = true) {
      return $this;
    }
    public function defaultValue($val = null) {
      return $this;
    }
  }

  class FTFile extends FTString {
    private $defaultPath = null;
    public function __construct($defaultPath = null) {
      parent::__construct(100);
      $this->defaultPath = $defaultPath;
    }
    public function unique($val = true) {
      return $this;
    }
    public function nullable($val = true) {
      return $this;
    }
    public function defaultValue($val = null) {
      return $this;
    }
    public function getPath() {
       return $this->defaultPath;
    }
/*
    public function toValue($v, $odb = null) {
      $root = $_SERVER['DOCUMENT_ROOT'];
      $file = $v;
print_r($v);
      # check if this is a file object sent in from a form
      if (is_array($file)
        and isset($file['name']) 
        and ($file['name'] != null) 
        and (strlen($file['name']) > 0)) {
        #ok, we have a file.  Save it to the default path
        #dont actually save it, just grab the name and prepend the path
        $fromfile = $file['tmp_name'];
        $tofile = $root . $this->defaultPath . '/' . $file['name'];
        $rc = move_uploaded_file($fromfile, $tofile);
        if (! $rc) { 
          error_log("ERROR moving file from $fromfile to $tofile ");
        }

        $file = $this->defaultPath . '/' . $file['name'];
      } else {
        $file = null;
      }
       
      return $file;
    }
*/
  }


  class FTDecimal extends FieldType {
    private $size = 10;
    private $scale = 2;

    public function __construct($size = 10, $scale = 2) {
      parent::__construct("decimal($this->size, $this->scale)");
      $this->size = $size;
      $this->scale = $scale;
    }
  }

  class FTBoolean extends FieldType {
    public function __construct() {
      parent::__construct("tinyint(1)"); # add in default
    }

    public function isNullable() {
      return false;
    }

    public function getDefaultValue() {
      return "0";
    }

    public function isSQLValueQuoted() {
      return false;
    }

    public function toValue($v, $odb = null) {
      $rtn = $v;
      if (is_string($v)) {
        $rtn = (($v == 'Y') or ($v == '1'));
      }
      
      return $rtn;
    }
  }

  class FTDate extends FieldType {
    public function __construct() {
      parent::__construct('date', true);
    }
    # value out is mm/dd/yyyy format
    public function toValue($v, $odb = null) {
      $value = $v;
      if (($value != null) and (strlen($value) > 0)) {
       $dtparts = split('-', $value);
        if (count($dtparts) == 3) {
          $value = $dtparts[1] . '/' . $dtparts[2] . '/' . $dtparts[0];
        }
      }
      return $value;
    }

    # value in to db is yyyy-mm-dd
    public function toSQLValue($v) {
      $value = $v;
      if (($value != null) and (strlen($value) > 0)) {
       $dtparts = split('/', $value);
        if (count($dtparts) == 3) {
          $value = $dtparts[2] . '-' . $dtparts[0] . '-' . $dtparts[1];
        }
      }
      return $value;
    }
  }
  class FTReference extends FieldType {
    private $classname;

    public function __construct($classname) {
      parent::__construct('int');
      $this->classname = $classname;
    }

    public function getClassName() {
      return $this->classname;
    }

    public function isPrimative() {
      return false;
    }

    public function toValue($v, $odb = null) {
      $rtn = null;

      if ($v != null) {
        if (is_int($v) or is_string($v)) {
          # this is the id, so let's load the object
          $rtn = $odb->fetch($this->classname, $v);
        } else if ($v instanceof $this->classname) {
          # else we should really check that this is and instanceof
          $rtn = $v;
        }
      }
      return $rtn;
    }
  }

  class Field {
    public $name;
    public $type;     # FieldType 
    public $display;

    /* type field will not be altered, so have it passed by ref */
    public function __construct($name, &$type, $display) {
      $this->name = $name;
      $this->type = $type;
      $this->display = $display;
    }
   
    public function isReference() {
      return ($this->type->isPrimative() == false);
    }

    public function toValue($v, $odb = null) {
      return $this->type->toValue($v, $odb);
    }

    public function toSQLValue($v) {
      return $this->type->toSQLValue($v);
    }
  }

/********************************************************************/
/* DBObjects, these are object stored in the database.  The will be */
/*  created and stored by the controllers in a map (model)          */
/********************************************************************/
  

  #every object in the db i.e. every table has an id
  class DBObject {
    private $fields = array();
    private $field_ary = array();
    private $odb = null;

    #HERE add ODB to the constructor
    public function __construct() {
       $ft = new FTID();
       $this->addField(new Field('id', $ft, 'ID'));
    }

    public function createNewObject($dboClassName) {
      $dbo = new $dboClassName();
      $dbo->setODB($this);
      return $dbo;
    }
  


    public function setODB($odb) {
      $this->odb = $odb;
    }

    public function getODB() {
      return $this->odb;
    }

    public function setFieldValues($value_map) {
      foreach($value_map as $fn => $val) {
        $this->$fn = $val;
      }
    }

    public function resetBooleanValues() {
      foreach($this->fields as $field) {
        if ($field instanceof FTBoolean) {
          $fn = $field->name;
          $this->$fn = false;
        }
      }
    }

    public function __set($name, $value) {
      # TODO: need to check if the name is in the list of fields
      $field = getMapValue($this->fields, $name);
      if ($field != null) {
        $odb = $this->odb;
        $this->field_ary[$name] = $field->toValue($value, $odb);
      }
    }

    public function setAll($map) {
      echo "called set all should change to setFieldValues<br/>";
      debug_print_backtrace();
      echo "<br/>";
      foreach($map as $key => $value) {
        $this->$key = $value;
      }
    }

    public function __get($name) {
      $value = getMapValue($this->field_ary,  $name);
      return $value;
    }

    /*
     * a reference is a one to many relationship between 
     *  the id of this object and the reference table
     *  example:  a member can have many family members, or shooters
     */
    public function addReference($something) {
    }

    public function addField($field) {
      $this->fields[$field->name] = $field;
    }

    public function getFields() {
      return $this->fields;
    }

    public function getFieldByName($name) {
      return getMapValue($this->fields, $name);
    }

    public function dump() { 
      echo get_class($this) . " [ " . PHP_EOL;
      echo "values:(" . PHP_EOL;
      foreach ($this->field_ary as $k => $v) {
        echo $k .  "= '";
        if ($v instanceof DBObject) {
           $v->dump();
        } else {
           echo "$v" . PHP_EOL;
        }
       echo "'";
      }
      echo ")] " . PHP_EOL;
    }
    public function __toString() {
      return $this->name;
    }
  }


  # Lookups are special case DBObjects
  #  the are matched by value and not by id
  class Lookup extends DBObject {
    public function __construct($value_map = null) {
      parent::__construct($value_map);
      $ft = new FTString(20);
      $ft = $ft->unique()->nullable(false);
      $this->addField(new Field('value', $ft, 'Value'));
      $this->addField(new Field('name', new FTString(50), 'Name'));
      $ft = new FTInt();
      $ft->nullable(false)->defaultValue(1);
      $this->addField(new Field('sort_order', $ft, 'Order'));
    } 
  }

  # special case object that is used for web security.
  # to add more info beyound login, password and roles, extend this class
  class WebUser extends DBObject {
    private static $salt = 'ADS4rTj6';
    public function __construct($value_map = null) {
      parent::__construct($value_map);
      $ft = new FTString(60);
      $ft = $ft->unique()->nullable(false);
      $this->addField(new Field('login', $ft, 'Login'));
      #TODO make this private
      $this->addField(new Field('password', new FTSecret(20), 'Password'));

      $ft = new FTBoolean();
      $this->addField(new Field('login_enabled', $ft, 'Login Enabled'));
      #comma seperated string of roles
      $this->addField(new Field('role', new FTString(150), 'Roles'));
    }

    public function encryptPassword($pwd) {
      $epwd = crypt($pwd, WebUser::$salt);
      return $epwd;
    }

    public function hasRole($roleName) {
      $roles = split(';', $this->role);
      return in_array($roleName, $roles);
    }
  }


/********************************************************************/
/* ODB, This class is allows access of database structures as       */
/*  objects                                                         */
/********************************************************************/
  class ODB {
    private $dblink = null;

    # HERE add logging to the connect and disconnect
    public function __construct() {
      # if you are creating a ODB object, you intend to use it
      #  so just connect
      $this->dblink = mysql_connect('localhost', 'kbdbuser', 'kbdbpwd', true);
      mysql_select_db('kbdbv02', $this->dblink);
      ##error_log("db connect");
#      debug_print_backtrace();
    }

    public function __destruct() {
      mysql_close($this->dblink);
      #error_log("db close");
    }


    public function escape_query($str) {
      $rtn = strtr($str, array(
        "\0" => "",
        "'"  => "&#39;",
        "\"" => "&#34;",
        "\\" => "&#92;",
        // more secure
        "<"  => "&lt;",
        ">"  => "&gt;"
       ));
      return strtr($rtn, array(
        "&#92;&#39;" => "&#39;",
        "&#92;&#34;" => "&#34;"
        ));
    }
    public function unescape_query($str) {
      return strtr($str, array(
        "&#39;" => "'",
        "&#34;" => "\"",
        "&#92;" => "\\",
        "&lt;" => "<",
        "&gt;" => ">"
      ));
    }

    function toTableName($obj_name) {
      $table_name = '';
      $len = strlen($obj_name);
      $c = $obj_name{0};
      $table_name .= $c;
      for ($i = 1; $i< $len; $i++) {
        $c = $obj_name{$i};
        if (ucfirst($c) == $c) {
          $table_name .= "_$c";
        } else {
          $table_name .= $c;
        }
      }
      return strtolower($table_name);
    }

    /**
     * fetch the first occurence of the object
     */
    public function fetch($obj_name, $id) {
      $obj = null;
      $sql = 'select * from ' . $this->toTableName($obj_name)
          . ' where id = ' . $id . ' limit 1';
      #echo "SQL = $sql<br/>\n";
      $rs = mysql_query($sql, $this->dblink);
      if (! $rs) {
        echo "Fetch Error <br/>" . PHP_EOL;
        echo "sql =  $sql <br/>" . PHP_EOL;
        echo "error =  " . mysql_error() . "<br/>" . PHP_EOL;
        echo "<pre>";
        debug_print_backtrace();
        echo "<pre>";
        die('ugh');
 
      } else {
        if ($aa = mysql_fetch_assoc($rs)) {
          $aa = array_map(array($this, 'unescape_query'), $aa);
          $obj = new $obj_name();
          $obj->setODB($this);
          $obj->setFieldValues($aa);
        }
      }
      $refCache = array();
      #$this->loadReferences($obj, $refCache);
/*
      foreach ($obj->getFields() as $field) {
        if ($field->isReference()) {
          $fn = $field->name;
          if ($obj->$fn != null) {
            $obj->$fn = $this->fetch($field->type->getClassName(), $obj->$fn);
          }
        }
      }
*/
      return $obj;
    }


    public function fetchLookup($obj_name, $value) {
      $obj = new $obj_name();
      $obj->setODB($this);
      $sql = 'select * from ' . $this->toTableName($obj_name)
          . " where value = '" . $value . "' limit 1";
      #echo "SQL = $sql\n<br/>";
      $rs = mysql_query($sql, $this->dblink);
      if ($aa = mysql_fetch_assoc($rs)) {
        $aa = array_map(array($this, 'unescape_query'), $aa);
        $obj->setFieldValues($aa);
      }
      return $obj;
    }

    #reference_cache is array( 'EventType' => array(id => value))
    public function loadReferences(&$obj, &$reference_cache) {
      foreach ($obj->getFields() as $field) {
        if ($field->isReference()) {
          $fn = $field->name;
          if ($obj->$fn != null) {
            $className = get_class($obj);
            $refCacheID = $className . ":" . $field->name;
            if (! isset($reference_cache[$refCacheID])) {
                $reference_cache[$refCacheID] = array();
            }
            # lets see if it is in the cache
            if (! isset($reference_cache[$refCacheID][$obj->$fn])) {
              if ($field->type instanceof Lookup) {
                $o = $this->fetchLookup($field->type->getClassName()
                                       ,$obj->$fn);
              } else {
                $o = $this->fetch($field->type->getClassName(), $obj->$fn);
              }
              $reference_cache[$refCacheID][$obj->$fn] = $o;
            }
            $obj->$fn = $reference_cache[$refCacheID][$obj->$fn];
          }
        }
      }
    }

    /**
     * this method is to fetch a list of id, quickly, for instance
     * select all ids from the event_type table where value is like shoot
     */
    public function fetchIDList($obj_name, &$cond) {
      $ary = array();
      $sql = 'select id from ' . $this->toTableName($obj_name);
      if ($cond != null) { 
        if (isset($cond['cond'])) {
           $sql .= ' where ' . $cond['cond'];
        }
        if (isset($cond['order'])) {
           $sql .= ' order by ' . $cond['order'];
        }
        if (isset($cond['limit'])) {
           $sql .= ' limit ' . $cond['limit'];
        }
      }
      #echo "SQL = $sql\n";
      $rs = mysql_query($sql, $this->dblink);
      while ($aa = mysql_fetch_assoc($rs)) {
        array_push($ary, $aa['id']);
      }
      return $ary;
    }

    public function fetchFirst($obj_name, &$cond = null) {
      $ary = array();
      $sql = 'select * from ' . $this->toTableName($obj_name);
      if ($cond != null) { 
        if (isset($cond['cond'])) {
           $sql .= ' where ' . $cond['cond'];
        }
        if (isset($cond['order'])) {
           $sql .= ' order by ' . $cond['order'];
        }
        $sql .= ' limit 1';
      }
      #echo "$sql" . PHP_EOL;
      $rs = mysql_query($sql, $this->dblink);
      if ($aa = mysql_fetch_assoc($rs)) {
        $aa = array_map(array($this, 'unescape_query'), $aa);
        $obj = new $obj_name();
        $obj->setODB($this);
        $obj->setFieldValues($aa);
      }
      return $obj;
    }

    public function clearObjects($obj_name) {
      $sql = 'delete from ' . $this->toTableName($obj_name);
      $this->execSQL($sql);
    }

    public function fetchAll($obj_name, &$cond = null) {
      $ary = array();
      $sql = 'select * from ' . $this->toTableName($obj_name);
      if ($cond != null) { 
        if (isset($cond['cond'])) {
           $sql .= ' where ' . $cond['cond'];
        }
        if (isset($cond['order'])) {
           $sql .= ' order by ' . $cond['order'];
        }
        if (isset($cond['limit'])) {
           $sql .= ' limit ' . $cond['limit'];
        }
      }
      
      #echo "SQL = $sql\n";
      $rs = mysql_query($sql, $this->dblink);
      while ($aa = mysql_fetch_assoc($rs)) {
        $aa = array_map(array($this, 'unescape_query'), $aa);
        $obj = new $obj_name();
        $obj->setODB($this);
        $obj->setFieldValues($aa);
        array_push($ary, $obj);
      }
      
      return $ary;
    }

    private function execSQL($sql) {
      $rc = mysql_query($sql, $this->dblink);
      if (! $rc) {
         error_log(mysql_error());
      }
    }
    public function createTable($obj_name) {
      $index_commands = array();
      $obj = new $obj_name();
      $tableName = $this->toTableName($obj_name);
      $inxCount = 0;
      $sql = 'create table ' . $tableName . '(' . PHP_EOL;
      $sep = '  ';
      foreach($obj->getFields() as $field) {
        $field_sql_extra = '';
        $sql .= $sep . $field->name . ' ' 
             . $field->type->getSQLTypeString();
        #TODO add check for default here
        if ($field->type->isUnique()) {
           $inxCount++;
           $inx_sql = 'create unique index ' . $tableName . "_inx$inxCount"
                  . ' on ' . $tableName . "($field->name)";
       
           array_push($index_commands, $inx_sql);
        }
        if (! $field->type->isNullable()) {
           $field_sql_extra .= ' not null';
        }
        $dval = $field->type->getDefaultValue();
        if ($dval != null) {
          $field_sql_extra .= ' default ' . $dval;
        }
       

       $sql .= $field_sql_extra . PHP_EOL;
        $sep = ' ,';
      }
      $sql .= ')' . PHP_EOL;
      $this->execSQL($sql);
      echo "executing: $sql" . PHP_EOL;
      foreach($index_commands as $sql) {
        $this->execSQL($sql);
        echo "executing: $sql" . PHP_EOL;
      }
    }

    public function dropTable($obj_name) {
      $sql = 'drop table ' . $this->toTableName($obj_name) . PHP_EOL;
      $this->execSQL($sql);
      echo $sql;
    }

    public function deleteObject($obj) {

      # assumes ids are positive numbers
      if (($obj->id != null) and ($obj->id > 0)) {
         $table = $this->toTableName(get_class($obj));
         $sql = 'delete from ' . $table . ' where id = ' . $obj->id;
         #error_log($sql);
         $this->execSQL($sql);
      }

    }

    private function insert($obj) {
      $obj_name = get_class($obj);
      $table = $this->toTableName($obj_name);
      $fieldNames = array();
      $fieldValues = array();

      foreach ($obj->getFields() as $field) {
        $fn = $field->name;
        $value = $obj->$fn;
        if ($value != null) {
          if ($field->isReference()) {
            $value = $obj->$fn->id;
          } else {
            $value = $field->type->toSQLValue($value);
          }
          if ($field->type->isSQLValueQuoted()) {
            $value = $this->escape_query($value);
            $value = "'" . $value . "'";
          }
          array_push($fieldNames, $fn);
          array_push($fieldValues, $value);
        }
      }
      $sql = 'insert into ' . $table
           . '(' . implode(',', $fieldNames) . ') values ('
           . implode(',', $fieldValues) . ')';
      #error_log($sql . PHP_EOL);
      $this->execSQL($sql);
      
    }

    private function update($obj) {
      #echo "updating object $obj - not Implemented yet\n";
      $obj_name = get_class($obj);
      $table = $this->toTableName($obj_name);
      $valuesAry = array();
      $sql = 'update ' . $table;
      $sep = ' set ';

      foreach ($obj->getFields() as $field) {
        $fn = $field->name; 
        if ($fn != 'id') {
          $value = $obj->$fn;
          if ($value != null) {
            if ($field->isReference()) {
              $value = $obj->$fn->id;
            } else {
              $value = $field->type->toSQLValue($value);
            }
            if ($field->type->isSQLValueQuoted()) {
              $value = $this->escape_query($value);
              $value = "'" . $value . "'";
            }
          } else {
            $value = 'null';
          }
          $sql .= $sep . $fn . ' = ' . $value;
          $sep = ', ';
        }
      }
      $sql .= ' where id = ' . $obj->id;
      #echo "$sql<br/>" . PHP_EOL;
      $this->execSQL($sql);
    }

    public function save($obj) {
      if (($obj->id == null) or (trim($obj->id) == '')) {
        $obj->id = null;
        $this->insert($obj);
      } else {
        $this->update($obj);
      }
    }
  }
/********************************************************************/
/* Controller:  MVC Pattern controller, connects the view to the    */
/*  model                                                           */
/********************************************************************/
  class Controller {
    private $name;
    private $view;
    private $template;
    private $model;
    private $protectedMethods = array();
    private $requestURI;
    private $streamFile = array();
    private $securityRoles = array();
    private $user = null;  // WebUser object
    private $odb = null;

    public function __construct() {
    }

    public function setODB($odb) {
      $this->odb = $odb;
    }

    public function getODB() {
      return $this->odb;
    }


    public function addSecurityRole($function, $role) {
      if (!isset($this->securityRoles[$function])) {
        $this->securityRoles[$function] = array($role);
      } else {
        array_push($this->securityRoles[$function], $role);
      }
    }
  
    function getSecurityRoles() {
      return $this->securityRoles;
    } 

    public function getUser() {
      return $this->user;
    }

    public function setUser($user) {
      $this->user = $user;
    }

    public function setStreamFile($contentType, $fileName) {
      $this->streamFile['contentType'] = $contentType;
      $this->streamFile['fileName'] = $fileName;
    }

    public function getStreamFile() {
      return $this->streamFile;
    }


    public function getRequestURI() {
      return $this->requestURI;
    }

    public function setRequestURI($requestURI) {
      $this->requestURI = $requestURI;
    }

    public function setName($name) {
      $this->name = $name;
    }

    public function getName() {
      return $this->name;
    }

    public function setView($view) {
      $this->view = $view;
    }

    public function getView() {
      if (isset($this->view)) {
        $view = $this->view;
      } else {
        $view = 'view/' . $this->getName() . '.php';
      }
      return $view;
    }

    public function getTemplate() {
      return $this->template;
    }

    public function setTemplate($template) {
      $this->template = $template;
    }

    public function getModel() {
      return $this->model;
    }

    public function setModel($model) {
      $this->model = $model;
    }

    public function __call($name, $arguments) {
      # just ignore any and method not defined in the object that is called
    }
  }

  class SecurityController extends Controller {
    private $delegate = null;

    private $loginForm = 'code/loginform.php';
    private $userOCN = 'Person';

    private $origPathInfo = null;

    public function __construct($delegate, $pathInfo) {
      parent::__construct();
      $this->delegate = $delegate;
      $this->delegate->setUser($this->fetchUser());
      $this->origPathInfo = $pathInfo;
    }

    public function setODB($odb) {
      $this->delegate->setODB($odb);
    }

    public function getODB() {
      return $this->delegate->getODB();
    }

    public function getUser() {
      return $this->delegate->getUser();
    }

    public function setUser($user) {
      $this->delegate->setUser($user);
    }

    public function setStreamFile($contentType, $fileName) {
      $this->delegate->setStreamFile($contentType, $fileName);
    }

    public function getStreamFile() {
      return $this->delegate->getStreamFile();
    }


    public function getRequestURI() {
      return $this->delegate->getRequestURI();
    }

    public function setRequestURI($requestURI) {
      $this->delegate->setRequestURI($requestURI);
    }

    public function setName($name) {
      $this->delegate->setName($name);
    }

    public function getName() {
      return $this->delegate->getName();
    }

    public function setView($view) {
      $this->delegate->setView($view);
    }

    public function getView() {
      return $this->delegate->getView();
    }

    public function getTemplate() {
      return $this->delegate->getTemplate();
    }

    public function setTemplate($template) {
      $this->delegate->setTemplate($template);
    }

    public function getModel() {
      return $this->delegate->getModel();
    }

    public function setModel($model) {
      $this->delegate->setModel($model);
    }

    private function fetchUser() {
      $user = $this->fetchUserFromCookie();
      return $user;
    }

    private function fetchUserFromCookie() {
      $user = null;
#print_r($_COOKIE);
      if (isset($_COOKIE['kbuser'])) {
        $id = $_COOKIE['kbuser'];
        $odb = $this->getODB();
        $user = $odb->fetch($this->userOCN, $id);
#echo "user fetched from cookie $id";
      }
      return $user;
    }

    public function login($request = null) {
      $message = getMapValue($request, 'message');
      $model = array('message' => $message);
      $this->setModel($model);
      $this->setView($this->loginForm);
    }

    public function logout($data = null) {

      # unset the user cookie, and unset the user
      $this->setUser(null);
      setcookie('kbuser', '', time()-3600, '/');
      $this->setView('code/logout.php');
    }

    public function showLogin($request = null) {
      $this->setView('code/loginform.php');
    }

    public function processLogin($request = null) {
      $tempWebUser = new WebUser();
      $parms = getMapValue($request, 'data');
      $login = getMapValue($parms, 'login', '');
      $password = getMapValue($parms, 'password', '');
      $password = $tempWebUser->encryptPassword($password);
      $expiration = time() + 60*60*24*7;  // keep logged in for 7 days
      if ($login === 'kenoshabowmen') {
         $expiration = null;
      }
      $user = null;

      if ($login != '') {
        $odb = new ODB();
        $cond = array('cond' => "login='$login' and password='$password' "
                   . " and login_enabled = " . true); 
        $user = $odb->fetchFirst('Person', $cond); 
      }
      $this->setUser($user);

      if ($user == null) {
        setcookie('kbuser', '', time()-3600, '/');
        $request['message'] = 'Unable to login with the username and password.'
                   . ' Please try again.  If you forgot your password, '
                   . ' please fill in the request password form.';
        $this->login($request);
      } else {
        setcookie('kbuser', $user->id, $expiration, '/');
        $this->setModel(array('original-request' => $this->origPathInfo));
        $this->setView('code/loginsuccess.php');
        $this->setTemplate(null);
      }
    }

    public function processForgotPassword($request = null) {
      # will hard code the gate_code check here, but need to put this
      #either in a config file or db
      $GATE_CODE_VALUE = "5935";
      $data = getMapValue($request, 'data');
      $email = getMapValue($data, 'email');
      $zip = getMapValue($data, 'zip');
      $gate_code = getMapValue($data, 'gate_code');
      $infoCorrect= false;
      $userObj = null;

      if (($gate_code == $GATE_CODE_VALUE) and ($email != null)) {
        // fetch the user account
        $odb = $this->getODB();
        $cond = array('cond' =>
                   "email = '$email' and zip = '$zip' and email is not null");
        $userObj = $odb->fetchFirst('Person', $cond);
        $infoCorrect = ($userObj != null);
      }

      if ($infoCorrect) {
        $userObj->password = $userObj->encryptPassword($GATE_CODE_VALUE);
        $request['message'] = 'An email will be sent to you shortly, with your password';
        $this->sendPasswordEmail($userObj, $GATE_CODE_VALUE);
        $odb->save($userObj);
      } else {
        $request['message'] = 'We were unable to verify your information.';
      }

      $this->login($request);
    }

    private function generateRandomPassword() {
      $rtn = '';
      $str = "abcdefghijklmnopqrstuvwxyz";
      $str = $str . strtoupper($str)
           . "1234567890" . "!@#$%^&*()_";
      $char_count = strlen($str);
      $char_ary = str_split($str);
      $topEnd = rand(10, 15);
      for ($i = 0; $i < $topEnd; $i++) {
        $inx = rand(0, $char_count-1);
        $rtn = $rtn . $char_ary[$inx];
      }
      return $rtn;
    }

    private function sendPasswordEmail($user, $newPassword) {
       $header = "Reply-To: unknown@kenoshabowmen.com\r\n";
       $header .= "Return-Path: unknown@kenoshabowmen.com\r\n";
       $header .= "From: security@kenoshabowmen.com\r\n";
       $header .= "Organization: Kenoshabowmen\r\n";
       $header .= "MIME-Version: 1.0\r\n";
       $header .= "Content-Type: text/html charset=iso-8859-1\r\n";

       $to = $user->email;
       $subject = "kenoshabowmen.com forgotten password";

       $body = "<html><head><title>Fogotten Password</title></head>"
             .  "<body>Your password has been reset to <b>$newPassword</b>."
             .  "<br/>Please login and change your password."
             . "</body></html>";

#error_log("message sent to $to");
       mail($to, $subject, $body, $header);
    }

    private function canAccess($function) {
      $sr = $this->delegate->getSecurityRoles();
      $rtn = false;
      $user = $this->getUser();
      if (isset($sr[$function])) {
        if ($user != null) {
          foreach(split(';', $user->role) as $role) {
            $rtn = ($rtn or in_array($role, $sr[$function]));
          }
        }
      } else {
       #if no roles are set, then access is open
        $rtn = true;
      }
     
      return $rtn;
    }

    public function __call($name, $arguments) {
      # arguments are passed as an array
      # we need to break this up.
      $request = null;
      if (($arguments != null) and (count($arguments) > 0)) {
        $request = $arguments[0];
      }

      $rtn = null;
      if ($this->canAccess($name)) {
        $rtn = $this->delegate->$name($request);
      } else {
        $this->login();
      }
      return $rtn;
    }
  }

  /**
   * This class adds restful maintenance to the contoller objects
   */
  class EditableController extends Controller {
    private $className = null;
    private $controllerName = null;

    public function __construct($className, $controllerName = null) {
      parent::__construct();
      $this->className = $className;
      $this->controllerName = $controllerName;
      if ($controllerName == null) {
        $this->controllerName = strtolower($className);
      }
    }

    // this method removes any key whose values is blank
    private function reduceData($data) {
      $ary = array();
      foreach($data as $key => $value) {
        if (($value != null) or (trim($value) != '')) {
          $ary[$key] = $value;
        }
      }
      return $ary;
    }

    # screw restful, here is my restful
    # after maint, it is either <null> which is the list
    #  or new or edit or save, or delete  id is passed in in get or post
    public function maint($request = null) {
      $maintMethod = getMapValue($request,'pathInfo', '');
      $data = getMapValue($request,'data');
      $data = $this->reduceData($data);
      switch ($maintMethod) {
        case 'new':
          $this->editThis();
          break; 
        case 'edit':
          $id = getMapValue($data, 'id');
          $this->editThis($id);
          break; 
        case 'save':
          #check the action
          foreach ($data as $key => $value) {
            if (trim($value) == '') {
              $data[$key] = null;
            }
          }
          $action = getMapValue($data, 'maintaction', 'save');
          unset($data['maintaction']);
          if ($action == 'save') {
            $this->doMaintSave($data);
          } else if ($action == 'delete') {
            $id = getMapValue($data, 'id');
            $this->deleteThis($id);
          } else {
            $this->viewAll();
          }
          break; 
        case 'delete':
          $this->deleteThis($data);
          break; 
        default:  # or list
          $this->viewAll();
          break; 
      }
    }
 
    protected function doMaintSave($data) {
      $id = getMapValue($data, 'id', '');
      $odb = $this->getODB();
      if ($id == '') {
        $obj = new $this->className();
        $obj->setODB($odb);
      } else {
        $obj = $odb->fetch($this->className, $id);
      }
      $obj->setFieldValues($data);
      $odb->save($obj);
      $this->viewAll();
    }

    protected function deleteThis($id = null) {
      $odb = $this->getODB();
      $obj = new $this->className();
      $obj->setODB($odb);
      $obj->id = $id;
      $odb->deleteObject($obj);
      $this->viewAll();
    }

    protected function editThis($id = null) {
      $odb = $this->getODB();
      $obj = null;
      if ($id == null) {
        $obj = new $this->className();
        $obj->setODB($odb);
      } else {
        $obj = $odb->fetch($this->className, $id);
      }
      if ($obj == null) {
         $message = "Object $id not found";
         $this->viewAll($message);
      } else {
        $model = array('object' => $obj
                      ,'title' => "$this->className"
                  ,'url-root' => "/index.php/$this->controllerName/maint"
                      );
        $view = 'code/ec_edit.php';
        $this->setView($view);
        $this->setModel($model);
      }
    }

    protected function viewAll($message = null) {
      $obj = new $this->className();
      $obj->setODB($this->getODB());
      $model = array('object' => $obj
                  ,'title' => "$this->className"
                  ,'url-root' => "/index.php/$this->controllerName/maint"
                    );
      $view = 'code/ec_list.php'; 
      $this->setView($view);
      $this->setModel($model);
    }

  }

/********************************************************************/
/* Dispatcher:  This is the main class that parses out the path info*/
/*  and determines which controller to call                         */
/*  the dispatcher is security aware.                               */
/********************************************************************/
  class Dispatcher {
    private $controllerSourceName;
    private $controllerSourceFile;
    private $controllerClass;
    private $action = 'index';
    private $rest = array();
    private $pathInfo;
    private $controllerName;
    private $config = null;
    private $webUser = null;
    private $odb = null;

    public function __construct($cfg = null) {
      $this->config = $cfg;
      $this->parsePathInfo();
      $this->odb = new ODB();
    }

    private function getConfigValue($key, $defaultValue) {
      $v = null;
      if (isset($this->config) 
         and ($this->config != null) 
         and (isset($this->config[$key]))) {
        $v = $this->config[$key];
      } else {
        $v = $defaultValue;
      }
      return $v;
    }

    public function getControllerName() {
      return $this->controllerName;
    }
 
    private function controllerNameToClass($name) {
      $ary = split('_', $name);
      $rtn = '';
      foreach($ary as $a) {
        $rtn .= ucfirst($a);
      }
      $rtn .= "Controller";
      return $rtn;
    }

    private function parsePathInfo() {
      $this->controllerName = $this->getConfigValue('default-controller','index');
      if (isset($_SERVER['PATH_INFO'])) {
        $this->pathInfo = $_SERVER['PATH_INFO'];
      } else {
        $this->pathInfo = '/';
      }

      $pathSplit = split('/', $this->pathInfo);
      if (! isset($pathSplit)) {
        $pathSplit = array();
      }
      $cnt = count($pathSplit);

      if ($cnt > 1) {
        if ($pathSplit[1] != '') {
          $this->controllerName = $pathSplit[1];
        }
      }
      $this->controllerClass = $this->controllerNameToClass($this->controllerName);
      $this->controllerSourceFile = "controller/" 
                                         . $this->controllerName . ".php";

      if ($cnt > 2) {
         $this->action = $pathSplit[2];
         if ($this->action == '') {
           $this->action = "index";
         }
      }
      if ($cnt > 3) {
         $this->rest['pathInfo'] = $pathSplit[3];
      }
      $this->rest['request_method'] = $_SERVER['REQUEST_METHOD'];
      $data_ary = null;
      if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $data_ary = $_GET;
      } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data_ary = $_POST;
      }
      # add files to the data variable
      if ($_FILES != null) {
        foreach($_FILES as $fileInx => $fileVal) {
           $data_ary[$fileInx] = $fileVal;
        }
      }
      $this->rest['data'] = $data_ary;
    } # end parsePathInfo


    public function dump() {
      echo "controller = $this->controllerClass" . PHP_EOL;
      echo "action = $this->action" . PHP_EOL;
      echo "rest = $this->rest" . PHP_EOL;
    }

    public function dumpServer() {
      foreach ($_SERVER as $key => $value) {
        echo "$key => $value" . PHP_EOL;
      }
    }

    private function getController() {
      $template = $this->getConfigValue('template', 'template/template.php');

      if (file_exists($this->controllerSourceFile)) {
        require_once($this->controllerSourceFile);
      }
      if (class_exists($this->controllerClass)) {
        $controller = new $this->controllerClass;
      } else {
        $controller = new Controller($this->odb);
      }
      $controller->setName($this->controllerName);
      $controller->setTemplate($template);
      $controller->setRequestURI($_SERVER['REQUEST_URI']);
      return $controller;
    }

    public function dispatch() {
      $securityPassed = false;
      $securityEnabled = $this->getConfigValue('security-enabled', false);

      $controller = $this->getController();
      $controller->setODB($this->odb);

      if ($securityEnabled) {
        $controller = new SecurityController($controller, $this->pathInfo);
      }
      #echo "action = $this->action " . get_class($controller);
      $fn = $this->action;
      $controller->$fn($this->rest);
      #call_user_func_array(array($controller, $this->action)
                            #,array($this->rest));
      return $controller;
    }


    public function streamFile($contentType, $filePath) {
      $fpa = split('/', $filePath);
      $fileName = $fpa[count($fpa)-1];
      $pdfContents = file_get_contents($filePath);

      header('Pragma: public');
      header('Expires: 0');
      header('Cache-Control:  must-revalidate, post-check=0, pre-check=0');
      header('Cache-Control: public');
      header('Content-Description: File Transfer');
      header("Content-Type: $contentType"); 
      header("Content-Disposition: attachment: filename=$fileName");
      header('Content-Transfer-Encoding: binary');
      echo $pdfContents;
    }
  } # end class

  #  IIS Fix
  if(!isset($_SERVER['DOCUMENT_ROOT'])) {
    #$host = substr($_SERVER['HTTP_HOST'], 4);
    $_drary = explode("\\", dirname(__FILE__));
    array_pop($_drary);  # removed the code directory
    $_SERVER['DOCUMENT_ROOT'] = join('/', $_drary);
  }
?>
