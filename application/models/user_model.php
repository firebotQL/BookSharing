<?php

class User_model extends Model {

    function validate() {
        $this->db->where('username', $this->input->post('username'));
        $this->db->where('password', md5($this->input->post('password')));
        $query = $this->db->get('user');
        if ($query->num_rows == 1) {
            return $query;
        }
    }

    function create_user() {
        $new_user = array(
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password'))
        );

        $insert = $this->db->insert('user', $new_user);

        // receiving newly created user
        $this->db->where($new_user);

        $query = $this->db->get('user');

        if ($query->num_rows() == 1) {
            $row = $query->row();

            $new_user_data = array(
                'user_id' => $row->id,
                'first_name' => $this->input->post('first_name'),
                'second_name' => $this->input->post('second_name'),
                'email_address' => $this->input->post('email_address')
            );

            $insert2 = $this->db->insert('user_data', $new_user_data);
            return $insert2 && $insert;
        }
        $this->db->free_result();
        return $insert;  

    }

    function user_exist_by_name($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function exist_user_email($email)
    {
        $this->db->where('email_address', $email);
        $query = $this->db->get('user_data');
        if ($query->num_rows() > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }



    function user_exist_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0)
        {
            return $query;
        }
        else
        {
            return FALSE;
        }
    }

    function get_user_data($id)
    {
        $this->db->where('user_id', $id);
        $query = $this->db->get('user_data');
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }
        return NULL;
    }

    function update_user_data($id, $user_data)
    {
        $this->db->where('user_id', $id);
        $this->db->update('user_data', $user_data);
    }

    function update_password($id, $password)
    {
        $data = array('password' => md5($password));
        $this->db->where('id', $id);
        $this->db->update('user', $data);
    }

    function get_user_id($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0)
        {
            return $query->row()->id;
        }
        else
        {
            return NULL;
        }
    }
}