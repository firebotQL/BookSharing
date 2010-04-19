<?php

class Friend_model extends Model
{
    function Friend_model()
    {
        parent::Model();
    }

    function add_friend($friend_id, $user_id)
    {
        $this->db->select('fl.id');
        $this->db->join('friend_list fl', 'fl.connect_id = fc.id');
        $this->db->user('user_id', $user_id);
        $this->db->where('friend_id', $friend_id);
        $result = $this->db->get('friend_connect fc');
        if ($result->num_rows() == 0)
        {
            $fc_data = array('user_id' => $user_id);
            $this->db->insert('friend_connect', $fc_data);
            $fc_id = $this->db->insert_id();
            $fl_data = array('connect_id' => $fc_id,
                             'friend_id' => $friend_id);
            $result = $this->db->insert('friend_list', $fl_data);
            return $result;
        }
        else
        {
            return TRUE;
        }
    }

    function get_friend_list($user_id)
    {
        $this->db->select('fl.id');
        $this->db->join('friend_list fl', 'fl.connect_id = fc.id');
        $this->db->user('user_id', $user_id);
        $result = $this->db->get('friend_connect fc');
        if ($result->num_rows() > 0)
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }
}