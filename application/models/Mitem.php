

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mitem extends CI_Model
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
    function get_all_item()
    {
        $this->db->select('item.id, itemlist.id, item.user_id, user.team as team, user.name as uname, itemlist.name as iname, itemlist.type as itype')->from('item');
        $this->db->join('itemlist', 'item.item_id = itemlist.id', 'left');
        $this->db->join('user', 'user.id = item.user_id', 'left');
        $this->db->order_by('user.team')->order_by('user.name');
        $query = $this->db->get();
        $result['items'] = $query->result_array();
        return $result;
    }
    function get_all_list()
    {
        $this->db->select('id, name, type, atk')->from('itemlist');
        $query = $this->db->get();
        $result['itemlist'] = $query->result_array();
        return $result;
    }
    function get_item_list_by_user_id($user_id){
        $this->db->select("item.id as iid, itemlist.name as iname, count")->from('item')->where('user_id', $user_id)->where('count>', 0);
        $this->db->join('itemlist', 'itemlist.id = item.item_id', 'left');
        $query = $this->db->get();
        $temp = $query->result_array();
        $itemlist = array();
        foreach ($temp as $key => $value) {
            $temp_array = array(    $value['iid'], 
                                    $value['iname'], 
                                    $value['count']
                                    );
            array_push($itemlist, $temp_array);
        }
        return $itemlist;

    }
    function get_item_info($item_id, $method){
        $this->db->select("item.id as iid, itemlist.name as iname, count, itemlist.type as type, itemlist.atk as atk, descri")->from('item')->where('item.id', $item_id)->where('count>', 0);
        $this->db->join('itemlist', 'itemlist.id = item.item_id', 'left');
        $query = $this->db->get();
        $temp = $query->row_array();
        $itemlist = array();
        if (count($temp)>1) {
            $itemlist = array(    $temp['iid'], // 1
                                    $temp['iname'], 
                                    $temp['count'],
                                    $temp['type'], // 4
                                    $temp['atk'],
                                    $temp['descri'],
                                    );
        }
           
        if ($method==1) {
            return $itemlist;
        }else{
            return $temp;
        }
    }
    function use_item($iid){
        $i_info = $this->get_item_info($iid, 0);
        if (count($i_info)>0) {
            $count_t = (($i_info['count']-1>=0)?($i_info['count']-1):0);
            $data = array('count'=>$count_t);
            $this->db->where('id', $iid);
            $this->db->update('item', $data);
        }
    }
    function add_item($iid, $count, $user_id){
        $data = array(  'item_id' => $iid,
                        'user_id' => $user_id,
                        'count'=>$count);
        $this->db->insert('item', $data);
    }
    function get_item_from_md5($item_md5){
        $this->db->select('id, name')->from('itemlist')->where('md5(id)', $item_md5);
        $query = $this->db->get();
        $result = $query->result_array();
        $result['state'] = true;
        if ($query->num_rows()>1) {
                        
        }else if ($query->num_rows()==1) {
            $result = $result[0];
            $result['state'] = true;
        }else{
            $result['state'] = false;
        }
        // print_r($result);
        // die();
        return $result;
    }
    function loss_all_item($user_id){
        $this->db->where('user_id', $user_id);
        $this->db->where('item_id<=', 4);

        $this->db->update('item', array('count'=>0));
    }
    

}?>