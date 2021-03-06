<?php 

class Transaksi extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model('transaksi_model');
    }

    public function index(){
        $data['transaksi'] = $this->transaksi_model->get_data_transaksi()->result();
        $this->load->view('template_admin/header');
        $this->load->view('template_admin/sidebar');
        $this->load->view('admin/data_transaksi', $data);
        $this->load->view('template_admin/footer');
    }
    
    public function tambah_transaksi(){
        $data['transaksi'] = $this->transaksi_model->get_data('transaksi')->result();
        $data['user'] = $this->transaksi_model->get_data('user')->result();
        $data['mobil'] = $this->transaksi_model->get_data_mobil('mobil')->result();
        $this->load->view('template_admin/header');
        $this->load->view('template_admin/sidebar');
        $this->load->view('admin/form_tambah_transaksi', $data);
        $this->load->view('template_admin/footer');
    }

    public function tambah_transaksi_simpan(){    
        $id_transaksi = $this->input->post('id_transaksi');
        $id_user = $this->input->post('id_user');
        $id_mobil = $this->input->post('id_mobil');
        $tanggal_sewa = $this->input->post('tgl_sewa');
        $tanggal_kembali = $this->input->post('tgl_kembali');
        $harga_mobil = $this->input->post('harga');
        $selisih_hari=((abs(strtotime($tanggal_sewa) - strtotime($tanggal_kembali)))/(60*60*24));
        $total_sewa = $harga_mobil*$selisih_hari;
        $status = $this->input->post('status'); 

        $data = array(
            'id_transaksi' => $id_transaksi,
            'id_user' => $id_user,
            'id_mobil' => $id_mobil,
            'tanggal_sewa' => $tanggal_sewa,
            'tanggal_kembali' => $tanggal_kembali,
            'total_sewa' => $total_sewa,
            'status' => $status
        );

        $this->transaksi_model->insert_data($data,'transaksi');

        if($status == 1){
            $this->transaksi_model->insert_status_mobil_kosong($id_mobil, 'mobil');
        }else{
            $this->transaksi_model->insert_status_mobil_sedia($id_mobil, 'mobil');
        }
        $this->session->set_flashdata('pesan','
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data transaksi Berhasil Ditambahkan
            <button transaksi="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        redirect('admin/transaksi');
        
    }

    public function edit_transaksi($id){
        $where = array('id_transaksi'=> $id);
        $data['user'] = $this->transaksi_model->get_data('user')->result();
        $data['mobil'] = $this->transaksi_model->get_data('mobil')->result();
        $this->db->select('transaksi.id_transaksi, transaksi.id_user, transaksi.id_mobil, mobil.harga , user.nama, mobil.merk, transaksi.tanggal_sewa, transaksi.tanggal_kembali, transaksi.status');
        $this->db->from('transaksi');
        $this->db->join('mobil', 'mobil.id_mobil = transaksi.id_mobil');
        $this->db->join('user', 'user.id_user = transaksi.id_user');
        $this->db->where('id_transaksi', $id);
        $data['transaksi'] = $this->db->get()->result();
        $this->load->view('template_admin/header');
        $this->load->view('template_admin/sidebar');
        $this->load->view('admin/form_edit_transaksi', $data);
        $this->load->view('template_admin/footer');
    }

    public function edit_transaksi_simpan(){
        $id_transaksi = $this->input->post('id_transaksi');
        $id_user = $this->input->post('id_user');
        $id_mobil = $this->input->post('id_mobil');
        $tanggal_sewa = $this->input->post('tgl_sewa');
        $tanggal_kembali = $this->input->post('tgl_kembali');
        $harga_mobil = $this->input->post('harga');
        $selisih_hari=((abs(strtotime($tanggal_sewa) - strtotime($tanggal_kembali)))/(60*60*24));
        $total_sewa =$harga_mobil*round($selisih_hari);
        $status = $this->input->post('status');

        $data = array(
            'id_transaksi' => $id_transaksi,
            'id_user' => $id_user,
            'id_mobil' => $id_mobil,
            'tanggal_sewa' => $tanggal_sewa,
            'tanggal_kembali' => $tanggal_kembali,
            'total_sewa' => $total_sewa,
            'status' => $status
        );

        echo print_r($data);

        $where = array(
            'id_transaksi' => $id_transaksi
        );

        $this->transaksi_model->edit_data('transaksi', $data, $where);

        if($status == 1){
            $this->transaksi_model->insert_status_mobil_kosong($id_mobil, 'mobil');
        }else{
            $this->transaksi_model->insert_status_mobil_sedia($id_mobil, 'mobil');
        }

        $this->session->set_flashdata('pesan','
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            Data transaksi Berhasil Diubah
            <button transaksi="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>');
        redirect('admin/transaksi');
    }

    public function delete_transaksi($id){
        $where = array('id_transaksi' => $id);
        $this->transaksi_model->delete_data($where, 'transaksi');
            $this->session->set_flashdata('pesan','
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data transaksi Berhasil Dihapus
                <button transaksi="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>');
        redirect('admin/data_transaksi');
    }
}

?>