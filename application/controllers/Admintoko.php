<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admintoko extends CI_Controller
{
    public function index()
    {
        $data['tittle'] = 'Dashboard';
        $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
        $this->session->userdata('email_admin')])->row_array();

        $this->load->model('User_model');
        $data['profile'] = $this->User_model->ProfileToko()->result();

        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('admintoko/index',$data);
        $this->load->view('template/footer_admintoko');
    }

    public function produk()
    {
        $data['tittle'] = 'Produk';
        $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
        $this->session->userdata('email_admin')])->row_array();

        $this->load->model('Produk_model');
        $data['kategori'] = $this->Produk_model->kategori();

        $this->load->model('User_model');
        $data['produk'] = $this->User_model->ProdukToko()->result();
        
        $data['toko'] =  $this->db->get_where('toko',['toko_id' =>
        $data['admin_toko']['toko_id']])->row_array();

        $this->form_validation->set_rules('kategori_id', 'Kategori Produk', 'required');
        $this->form_validation->set_rules('nama', 'Nama Produk', 'required');
        $this->form_validation->set_rules('ket', 'Keterangan Produk', 'required');
        $this->form_validation->set_rules('harga', 'Harga Produk', 'required');
        $this->form_validation->set_rules('berat', 'Berat Produk', 'required');
        $this->form_validation->set_rules('stok', 'Stok Produk', 'required');
        $this->form_validation->set_rules('toko_id', 'Nama Toko', 'required');
        $this->form_validation->set_rules('gambar_produk', 'Gambar Produk', 'required');

        if($this->form_validation->run() ==  false)
        {
        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('produk/index',$data);
        $this->load->view('template/footer_admintoko');
        }
        else
        {
            $kategori_id = $this->input->post('kategori_id');
            $nama_produk = $this->input->post('nama');
            $ket_produk = $this->input->post('ket');
            $harga_produk = $this->input->post('harga');
            $stok_produk = $this->input->post('stok');
            $toko_id = $this->input->post('toko_id');
            $gambar_produk = $_FILES['gambar_produk']['name'];
            if ($gambar_produk) 
            {
                $config['upload_path'] = './asset/img/produk/';
                $config['allowed_types'] = 'jpg|jpeg|png';
                $this->load->library('upload', $config);
                
                if ($this->upload->do_upload('gambar_produk'))
                {
                    $gambar_produk = $this->upload->data('file_name');
                }
                else 
                {
                    echo $this->upload->display_errors();
        
                }
            }
        
            $data =[
                'kategori_id' => $kategori_id,
                'nama_produk'      => $nama_produk,
                'ket_produk'       => $ket_produk,
                'harga_produk'      => $harga_produk,
                'stok_produk'          => $stok_produk,
                'toko_id'       => $toko_id,
                'gambar_produk' => $gambar_produk,
            ];
            $this->load->model('Produk_model');
            $this->Produk_model->tambah_produk($data, 'produk');
            redirect('Admintoko/produk');
        }
    }

    public function edit_produk($produk_id)
    {
        $data['tittle'] = 'Produk';
        $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
        $this->session->userdata('email_admin')])->row_array();

        $where = array('id_produk' =>$produk_id);
        $this->load->model('Produk_model');
        $data['produk'] = $this->Produk_model->edit_produk($where,'produk')->result();

        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('produk/edit_produk',$data);
        $this->load->view('template/footer_admintoko');
    }

    public function updateproduk()
    {
        $produkid          = $this->input->post('id_produk');
        $nama_produk       = $this->input->post('nama_produk');
        $keterangan_produk  = $this->input->post('ket_produk');
        $harga_produk     = $this->input->post('harga_produk');
        $stok_produk  = $this->input->post('stok_produk');

        $data = [
            'nama_produk'     => $nama_produk,
            'ket_produk'    => $keterangan_produk,
            'harga_produk'      => $harga_produk,
            'stok_produk'    => $stok_produk,
        ];

        $where = [
            'id_produk'     => $produkid
        ];
        $this->load->model('Produk_model');
        $this->Produk_model->update_produk($where, $data,'produk');
        redirect('Admintoko/produk');
    }

    public function hapus_produk($produkid)
    {
        $this->load->model('Produk_model');
        $this->Produk_model->HapusKurir($produkid);
        $this->session->set_flashdata('flash', 'Dihapus');
        redirect('Admintoko/produk');   
    }

    public function detail_produk($id_produk)
    {
        $data['tittle'] = 'Produk';
        $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
        $this->session->userdata('email_admin')])->row_array();
        $data['detail'] = $this->Produk_model->detail_brg($id_produk)->result();

        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('produk/detail_produk',$data);  
        $this->load->view('template/footer_admintoko');
    }

    public function tampil_invoice()
    {
       $data['tittle'] = 'Invoice';
       $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
       $this->session->userdata('email_admin')])->row_array();

        $data['invoice'] = $this->invoice_model->tampil_invoice_toko()->result();
        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('admintoko/invoice',$data);
        $this->load->view('template/footer_admintoko');
    }

    public function detail_invoice($id_invoice)
    {
       $data['tittle'] = 'Invoice';
       $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
       $this->session->userdata('email_admin')])->row_array();

        $data['invoice'] = $this->invoice_model->detail_invoice_toko($id_invoice)->row();
        $data['pesanan'] = $this->invoice_model->detail_invoice_toko2($id_invoice)->result();
        $data['pesanan2'] = $this->invoice_model->tampil_detail_admintoko2($id_invoice);
        $this->load->view('template/header',$data);
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('admintoko/detail',$data);
        $this->load->view('template/footer_admintoko');
    }

    public function laporan()
     {
        $data['tittle'] = 'Laporan';
        $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
        $this->session->userdata('email_admin')])->row_array();

        $data['tahun'] = $this->laporan_model->getTahun();
        $this->load->view('template/header');
        $this->load->view('template/sidebar_admintoko',$data);
        $this->load->view('template/topbar_admintoko',$data);
        $this->load->view('admintoko/laporan', $data);
        $this->load->view('template/footer_admintoko');
     }

     function filter()
     {
        $awal = $this->input->post('awal');
        $akhir = $this->input->post('akhir');
        $tahun1 = $this->input->post('tahun1');
        $bulanawal = $this->input->post('bulanawal');
        $bulanakhir = $this->input->post('bulanakhir');
        $tahun2 = $this->input->post('tahun2');
        $nilai = $this->input->post('nilai');

        if($nilai == 1)
        {
            $data['tittle'] = 'Laporan';
            $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
            $this->session->userdata('email_admin')])->row_array();
            $data['datafilter'] = $this->laporan_model->filterTanggaltoko($awal,$akhir)->result();

            $this->load->view("tanggal", $data);
        }

        elseif($nilai == 2)
        {
            $data['tittle'] = 'Laporan';
            $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
            $this->session->userdata('email_admin')])->row_array();
            $data['datafilter'] = $this->laporan_model->filterBulantoko($tahun1, $bulanawal, $bulanakhir)->result();

            $this->load->view("bulan", $data);
        }

        else
        {
            $data['tittle'] = 'Laporan';
            $data['admin_toko'] =  $this->db->get_where('admin_toko',['email_admin' =>
            $this->session->userdata('email_admin')])->row_array();
            $data['datafilter'] = $this->laporan_model->filterTahuntoko($tahun2)->result();

            $this->load->view("tahun", $data);
        }
     }

} 