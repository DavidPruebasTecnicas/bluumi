<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Owners extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        $this->load->library('form_validation');
        $this->load->model('companies_model');
    }

    public function index(){

    }

    private function getOwners($params) {
        $web = "https://gorest.co.in/public-api/".$params;

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Bearer '.GOREST_TOKEN 
        );
    
        $process = curl_init($web); 
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_HTTPGET , 1);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        curl_close($process);

        return $return;
    }
    // M2
    private function normalizedLimit($limit) {
        if(intval($limit) % 20 !== 0)
        {
            $limit = 20;
            log_message('error', 'Owners/owners: Recibido limit que no es multiplo de 20'); // M3_A
        }
        return $limit;
    }
    // M2_A
    public function owners($id = '') {
        if($id === '') {
            $page = $this->input->get('page') == '' ? 1 : $this->input->get('page');
            $limit = $this->input->get('limit') == '' ? 20 : $this->normalizedLimit($this->input->get('limit')); // M2_RETOA

            $size = $limit / 20;
            $content = [];
            $code = 500;
            // Mision2_RetoA
            for($i = 0; $i < $size; $i++)
            {
                $pageParam = $page + $i;
                $re = $this->getOwners("users?page=$pageParam");
                $re = json_decode($re);
                $content[] = $re->data;
                
                if($i == 0)
                    $code = $re->code;
            }
            $json['code'] = $code;
            $json['data'] = $content;
            $json = json_encode($json);
        }
        else {
            $owner = $this->getOwners("users/$id");
            $ownerStd = json_decode($owner);

            if($ownerStd->code == 200) {
                $posts = $this->getOwners("users/$id/posts");
                $postsStd = json_decode($posts);
                
                $ownerStd->posts = $postsStd->data;

                if(!empty($ownerStd->posts)) {
                    foreach($ownerStd->posts as $key => $o) {
                        $comments = $this->getOwners("posts/$o->id/comments");
                        $commentsStd = json_decode($comments);
                        $o->comments = $commentsStd->data;
                    }
                    
                }

                $json['code'] = 200;
                $json = json_encode($ownerStd);
            }
            else{
                $json['code'] = 400;
                $json = $owner;
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($json);
    
    }
    // M2_B
    public function favorites() {
        $idCompanie = $this->input->post('idCompanie');
        $idOwner = $this->input->post('idOwner');
        
        if($this->companies_model->exists($idCompanie)) {
            $owner = $this->getOwners("users/$idOwner");
            $ownerStd = json_decode($owner);
            if($ownerStd->code == '200'){
                $data = ['idOwner' => $ownerStd->data->id, 'idCompanie' => $idCompanie];

                if($this->companies_model->isFavorite($data) === false) { // Comprobamos que no esté ya en favoritos
                    if($this->companies_model->insertFavorites($data)){
                        $json['code'] = 200;
                        $json['data']['message'] = 'Exito';
                    }
                    else{
                        $json['code'] = 500;
                        $json['data']['message'] = 'Error base datos';
                    }
                }
                else {
                    $json['code'] = 400;
                    $json['data']['message'] = 'Ya era favorito';
                    log_message('error', 'Owners/favorites: '.$json['data']['message']); // M3_A
                }
            }
            else {
                $json = $ownerStd; // Voy a devolver el mismo error que devuelve la API cuando no encuentra usuario
            }
        }
        else {
            $json['code'] = 400;
            $json['data']['message'] = 'No existe compania';

            log_message('error', 'Owners/favorites: '.$json['data']['message']); // M3_A
        }

        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));
    }

    
}