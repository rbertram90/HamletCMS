<?php
/**************************************************************************
    class RBModel
    
    This class provides the generic basic CRUD functionality for every
    model under Blog CMS project.
    
    The functions provide an interface to the database class - which can
    be used directly but this way prevents having to repeat myself in the
    different models. It will also handle security!
    
    $_SESSION['userprivileges'] = array(
        '325235235' => 2,
        '435235235' => 1,
        '{blogid} => {permission-level}
    );
    
    1 = postonly
    2 = all
    
    Date: 23 Aug 2014
    Author: R.Bertram
    
**************************************************************************/

class BlogCMSModel {

    protected $db;
    protected $tblname;
    protected $privilegeLevel;

    public function __construct($db) {
        $this->db = $db;
        $this->privilegeLevel = $this->getUserPrivilegeLevel();
    }
    
    public function getCount($arrayWhere) {
        return $this->db->countRows($this->tblname, $arrayWhere);
    }
    
    public function get($arrayWhat, $arrayWhere, $order='', $limit='', $multi=true) {
        if($multi) {
            return $this->db->selectMultipleRows($this->tblname, $arrayWhat, $arrayWhere, $order='', $limit='');
        } else {
            return $this->db->selectSingleRow($this->tblname, $arrayWhat, $arrayWhere, $order='', $limit='');
        }
    }
    
    public function insert($arrayWhat) {
        return $this->db->insertRow($this->tblname, $arrayWhat);
    }
    
    public function update($arrayWhere, $arrayWhat) {
        return $this->db->updateRow($this->tblname, $arrayWhere, $arrayWhat);
    }
    
    public function delete($arrayWhere) {
        if($this->privilegeLevel = 2)
        return $this->db->deleteRow($this->tblname, $arrayWhere);
    }
    
    public function getUserPrivilegeLevel($blogid) {
        if(!isset($_SESSION['userprivileges'])) return 0;
        if(!isset($_SESSION['userprivileges'][$blogid])) return 0; // this won't work for posts!!!!
        return $_SESSION['userprivileges'][$blogid];
    }

}
?>