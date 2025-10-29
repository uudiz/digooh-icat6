<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**

*

*/

class User_model extends CI_Model

{

public function read(){

       //$this->db->where('company_id',111);
       $query = $this->db->query("select id,name,email,auth as role from `cat_user`");       
       $users =  $query->result_array();
       if($users){
          foreach ($users as $key=>$user) {
            if($user['role']==0){
                $this->db->where('user_id',$user['id']);
                $query = $this->db->query('select criteria_id from cat_user_criteria');
                $users[$key]['criteria'] = array_column($query->result_array(), 'criteria_id');
            }
          }
       }
       return $users;
   }  



   public function insert($data){

       $this->user_name    = $data['name']; // please read the below note

       $this->user_password  = $data['pass'];

       $this->user_type = $data['type'];


       if($this->db->insert('cat_user',$this))

       {    

           return 'Data is inserted successfully';

       }

         else

       {

           return "Error has occured";

       }

   }



   public function update($id,$data){



      $this->user_name    = $data['name']; // please read the below note

       $this->user_password  = $data['pass'];

       $this->user_type = $data['type'];

       $result = $this->db->update('tbl_user',$this,array('user_id' => $id));

       if($result)

       {

           return "Data is updated successfully";

       }

       else

       {

           return "Error has occurred";

       }

   }



   public function delete($id){



       $result = $this->db->query("delete from `tbl_user` where user_id = $id");

       if($result)

       {

           return "Data is deleted successfully";

       }

       else

       {

           return "Error has occurred";

       }

  }

}