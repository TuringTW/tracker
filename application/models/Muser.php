

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Muser extends CI_Model
{
     function __construct()
     {
          // Call the Model constructor
        $this->load->library('session');
        $this->load->model(array('login_check'));
        $required_power = 2;
        $this->login_check->check_init($required_power);
        parent::__construct();

    }
    // 取得合約列表
   
    function get_user_info($user_id, $method=1){
        $this->db->select('id, name, user, team, level, exp, blood, str, dex, inte, money, wood, iron, ruby, point')->from('user')->where('id', $user_id);
        $query = $this->db->get();
        $result['state'] = 0;
        if ($query->num_rows()>0) {
            $result['result'] = $query->row_array();      
            $result['state'] = 1;
            if ($method==2) {
                $temp = array( 
                    $result['result']['id'],
                    $result['result']['name'],
                    $result['result']['user'],//3
                    $result['result']['team'],
                    $result['result']['level'], //5
                    $result['result']['exp'],
                    $result['result']['blood'],
                    $result['result']['str'], //8
                    $result['result']['dex'],
                    $result['result']['inte'], //10
                    $result['result']['money'],
                    $result['result']['wood'], //12
                    $result['result']['iron'],
                    $result['result']['ruby'],//14
                    $result['result']['point'] );//15
                $result['result'] = $temp;
            }
        }
        
        return $result;
    }
    function add_resource($type, $value, $user_id){
        switch ($type) {
            case 0: //money
                $data=array('money'=>$value);
                break;
            case 1: //wood
                $data=array('wood'=>$value);
                break;
            case 2: //iron
                $data=array('iron'=>$value);
                break;
            case 3: //ruby
                $data=array('ruby'=>$value);
                break;
            default:
                $data = array();
                break;
        }

        $this->db->where('id', $user_id);
        $this->db->update('user', $data);
    }
    function minus_hp($user_info, $value){
        $data = array('blood'=>($user_info['blood']-$value>0)?$user_info['blood']-$value:0);
        $this->db->where('id', $user_info['id']);
        $this->db->update('user', $data);
    }
    function add_user_point($user_id, $type){
        $user_info = $this->get_user_info($user_id);
        if ($user_info['result']['point']>0) {
            switch ($type) {
                case 1:
                    $data = array('str'=>($user_info['result']['str']+1));
                    break;
                case 2:
                    $data = array('dex'=>($user_info['result']['dex']+1));
                    break;
                case 3:
                    $data = array('inte'=>($user_info['result']['inte']+1));
                    break;
                
                default:
                    $data = array();
                    break;
            }
            $data['point'] = $user_info['result']['point']-1;

            $this->db->where('id', $user_id);
            $this->db->update('user', $data);
            return true;
        }else{
            return false;
            
        }
    }
    function add_user_exp($user_id, $value){
        $user_info = $this->get_user_info($user_id);
        $data = array('exp'=>$user_info['result']['exp']+$value);
        $this->db->where('id', $user_id);
        $this->db->update('user', $data);
    }
    function check_upgrade($user_id){
        $user_info = $this->get_user_info($user_id);
        $level_now = $user_info['result']['level'];
        $exp = $user_info['result']['exp'];
        if (100*($level_now+1)*$level_now/2<$exp) { 
            $this->add_user_level($user_id);
            return 1;
        }else{
            return 0;
        }
    }
    function add_user_level($user_id){
        $user_info = $this->get_user_info($user_id);
        $data = array('level'=>$user_info['result']['level']+1);
        $data = array('point'=>$user_info['result']['point']+3);
        $this->db->where('id', $user_id);
        $this->db->update('user', $data);
    }
    
}

?>