<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Companies extends CI_Controller {
    // PENDIENTE: CAMBIAR LOS OK POR CODE, Y EL RESTO EN DATA
    public function __construct() {
        parent::__construct();
        
        $this->load->helper(array('form','url'));
        $this->load->library('form_validation');
        $this->load->model('companies_model');
    }

    public function index()
    {
    }

    public function create(){
        // Si la aplicación fuese multiidioma habría que especificar en el segundo parametro la variable correspondiente de los ficheros 
        // de "language", por simplificar voy a poner los textos directamente en español

        $validator = [
            [
                'field'   => 'name',
                'label'   => 'Nombre',
                'rules'   => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'description',
                'label'   => 'Descripcion',
                'rules'   => 'required',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'shortdesc',
                'label'   => 'shortdesc',
                'rules'   => 'max_length[100]',
            ],
            [
                'field'   => 'email',
                'label'   => 'Email',
                'rules'   => 'required|max_length[100]|valid_email',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'cif',
                'label'   => 'CIF',
                'rules'   => 'required|callback_validCif',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'logo',
                'label'   => 'Logo',
                'rules'   => 'required|max_length[999]',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'date',
                'label'   => 'Fecha',
                'rules'   => 'callback_validDate',
            ]
        ];

        
        // meter unique en el nombre de empresa, en el numero de cuenta, el CIF tambien, el email intuyo que tambien

        $this->form_validation->set_rules($validator);
        if (!empty($this->input->post('ccc'))){
            $this->form_validation->set_rules('ccc', 'ccc', 'exact_length[4]');
        }

        $this->form_validation->set_message('max_length', 'El %s debe tener como máximo %d carácteres');
        $this->form_validation->set_message('valid_email', 'El %s debe ser un email válido');
        $this->form_validation->set_message('validCif', 'El CIF no es válido');
        $this->form_validation->set_message('validDate', 'La fecha no es válida');
        $this->form_validation->set_message('exact_length', 'El %s debe tener %d carácteres');

        // No se especifica si el endpoint recibe la información por GET o por POST, voy a presuponer que por POST
        $name = $this->input->post('name');
        $description = $this->input->post('description');
        $cif = $this->input->post('cif');
        $logo = $this->input->post('logo');

        $shortDesc = $this->input->post('shortdesc') == '' ? null : $this->input->post('shortdesc');
        $ccc = $this->input->post('ccc') == '' ? null : $this->input->post('ccc');
        $date = $this->input->post('date') == '' ? null : $this->input->post('date');
        $status = $this->input->post('status');
        if($status == 1)
            $status = true;
        else if($status == 0)
            $status = false;
        else
            $status = null;

        $json['validations'] = $this->form_validation->run() !== false ? 1 : 0;
        if ($json['validations'] === 1){
            $data = [
                'name' => $name, 'description' => $description, 'ccc' => $ccc, 'date' => $date, 
                'status' => $status, 'shortDesc' => $shortDesc, 'logo' => $logo, 'cif' => $cif,
                'token' => password_hash($name, PASSWORD_BCRYPT)

            ];
            if($this->companies_model->insert($data)){
                $json['code'] = 200;
            }
            else{
                $json['code'] = 500;
                $json['message'] = $this->db->error();
            }

        }
        else{
            log_message('error', 'Companies/create: Errores de validación'); // M3_A
            $json['code'] = 400;
            $json['message'] = validation_errors();

        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    // MÉTODO PARA PROBAR EL UPDATE, CON POSTMAN SE PUEDE PROBAR IGUAL
    public function pruebasCurl()
    {
        $url = "http://localhost/codeigniter/companies/update";

        $header = array(
            'Content-Type:application/json',
            'Authorization: Bearer $2y$10$PShAZVrljzKD.xlxW6CT8.LOsnPtOLZgkNLsz8XriLX2YhXWBuyqO' 
        );
        
        $arr = array(
            'name' => 'nombre'
        );
        $fields = http_build_query($arr);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);

        echo $data;

    }

    public function update(){

        $validator = [
            [
                'field'   => 'name',
                'label'   => 'Nombre',
                'rules'   => 'required|max_length[50]',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'shortdesc',
                'label'   => 'shortdesc',
                'rules'   => 'max_length[100]',
            ],
            [
                'field'   => 'description',
                'label'   => 'Descripcion',
                'rules'   => 'max_length[100]',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ],
            [
                'field'   => 'logo',
                'label'   => 'Logo',
                'rules'   => 'max_length[999]',
                'errors' => [
                    'required' => 'Debe haber un %s.',
                ],
            ]
        ];

        $cif = $this->input->post('cif');
        $date = $this->input->post('date');
        $email = $this->input->post('email');
        $id = $this->input->post('id');

        if($cif != '' || $date != '' || $email != '' || $id != '')
        {
            $json['code'] = 400;
            $json['data']['message'] = 'Has introducido campos que no están permitidos actualizar';
            log_message('error', 'Companies/update: '.$json['data']['message']); // M3_A
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));
            die;
        }
        $name = $this->input->post('name');
        $authorization = isset(getallheaders()['Authorization']) ? getallheaders()['Authorization'] : '';
        $token = str_replace('Bearer ', '', $authorization);
        
        $data = ['updated_at' => date('Y-m-d H:i:s')];

        if($this->input->post('description') != '')
            $data['description'] = $this->input->post('description');

        if($this->input->post('shortdesc') != '')
            $data['shortdesc'] = $this->input->post('shortdesc');

        if($this->input->post('logo') != '')
            $data['logo'] = $this->input->post('logo');
        
        if($this->input->post('ccc') != '')
            $data['ccc'] = $this->input->post('ccc');

        if($this->input->post('status') != '')
        {
            $data['status'] = $this->input->post('status');
            
            if($data['status'] == 1)
                $data['status'] = true;
            else if($status == 0)
                $data['status'] = false;
            else
                $data['status'] = null;
        }
            


        $this->form_validation->set_rules($validator);
        if (!empty($this->input->post('ccc')))
        {
            $this->form_validation->set_rules('ccc', 'ccc', 'exact_length[4]');
        }

        $this->form_validation->set_message('max_length', 'El %s debe tener como máximo %d carácteres');
        $this->form_validation->set_message('exact_length', 'El %s debe tener %d carácteres');


        $json['validations'] = $this->form_validation->run() !== false ? 1 : 0;
        if ($json['validations'] === 1){
            
            if($this->companies_model->update($data, $name, $token)){
                $json['code'] = 200;
            }
            else{
                $json['code'] = 500;
                $json['data']['message'] = $this->db->error();
            }
        }
        else{
            log_message('error', 'Companies/update: Errores de validación'); // M3_A
            $json['code'] = 400;
            $json['data']['message'] = validation_errors();
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));

        
    }
    public function listC()
    {
        $page = $this->input->get('page') == '' ? 1 : intval($this->input->get('page'));

        $start = $page == 1 ? ($page - 1)*20 : ($page - 1)*20 + 1; 
        $limit = $page * 20;

        $authorization = isset(getallheaders()['Authorization']) ? getallheaders()['Authorization'] : '';
        $token = str_replace('Bearer ', '', $authorization);

        // Mision 3, Reto A
        if($page > 10 && $this->companies_model->tokenExist($token) === false)
        {
            $json['code'] = 401;
            $json['message'] = 'Error. Fallo de autorización';
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));

            log_message('error', 'Companies/list: '. $json['data']['message']); // M3_A
            
        }
        else
        {

            $companies = $this->companies_model->get($start, $limit);

            if(empty($companies))
            {
                $json['code'] = 400;
                $json['data']['message'] = 'Página no existente';
                log_message('error', 'Companies/list: '. $json['data']['message']); // M3_A
            }
            else
            {
                $json['code'] = 200;
                $json['data']['companies'] = $companies;
            }
    
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json));
        }


    }

    public function search()
    {
        $page = $this->input->get('page') == '' ? 1 : intval($this->input->get('page'));

        $start = $page == 1 ? ($page - 1)*20 : ($page - 1)*20 + 1; 
        $limit = $page * 20;

        $description = $this->input->get('description');

        // Comprobamos la longitud para evitar ataques por el backend

        if((strlen($description) >= 3))
        {
            $json['code'] = 200;
            $json['data']['descriptions'] =  $this->companies_model->search($description, $start, $limit);
            
        }
        else
        {
            log_message('error', 'Companies/search: Intento de descripción menor de 3 caracteres'); // M3_A
            $json['code'] = 400;
            $json['data']['descriptions'] = [];
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($json));
    }

    public function validCif($cif){
        return preg_match('/[A-Za-z]{1}[0-9]{8}/', $cif) == 1;
    }

    public function validDate($date) {
        if($date == '' || $date == null)
            return true;

        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    public function cosa()
    {
        $this->load->view('cosa/index', [], true);
    }

    
}