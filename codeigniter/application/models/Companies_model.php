<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Companies_model extends CI_Model {
    public function __construct() {

    }

    public function insert($data) {
        $bool = false;
        if($this->db->select('name')->from('companies')->where('name', $data['name'])->get()->num_rows() == 0)
            $bool = $this->db->insert('companies', $data);

        return $bool;
    }

    public function update($data, $name, $token) {
        $bool = false;
        $bool = $this->db->where('name', $name)->where('token', $token)->update('companies', $data);

        return $bool;
    }
    

    public function get($start, $limit) {
        $query = $this->db->select('id, name, logo, status, email')
                 ->from('companies')
                 ->limit($limit, $start)
                 ->get();

        return $query->result();
    }

    public function tokenExist($token)
    {
        return $this->db->select('token')->from('companies')->where('token', $token)->get()->num_rows() > 0;
    }

    public function search($description, $start, $limit) {
        $query = $this->db->select('description')
                 ->from('companies')
                 ->like('description', $description, 'both')
                 ->limit($limit, $start)
                 ->get();

        return $query->result();
    }

    
    public function exists($idCompanie) {
        return $this->db->select('id')->from('companies')->where('id', $idCompanie)->get()->num_rows() > 0;
    }

    public function isFavorite($data) {
        return $this->db->select('id')->from('favorites')->where('idCompanie', $data['idCompanie'])->where('idOwner', $data['idOwner'])->get()->num_rows() > 0;
    }

    public function insertFavorites($data) {
        return $this->db->insert('favorites', $data);
    }

}