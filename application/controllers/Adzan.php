<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adzan extends CI_Controller {

	public function index()
	{
		$this->load->view('adzan');
	}

	public function getjadwal()
	{
		if($this->input->post()){
					$param  = array('city' => $_POST['kota'],'country' => 'ID',
			'method' => '11','month' => $_POST['bulan'],'year' => $_POST['tahun']);

		$urlApi = 'http://api.aladhan.com/v1/calendarByCity'; //Url API
		$urlApi .='?city='.$_POST['kota']; // Ambil kota dari form submit
		$urlApi .='&country=ID'; // Negara set default Indonesia
		$urlApi .='&method=11'; // method ke aplikasi
		/* 
		method method untuk aplikasi
		dokumentasi nya kunjungi https://aladhan.com/prayer-times-api#GetCalendarByCitys
		*/
		$urlApi .='&month='.$_POST['bulan']; // Ambil bulan dari form submit
		$urlApi .='&year='.$_POST['tahun']; // Ambil tahun dari form submit

		$ch = curl_init(); //set curl
		curl_setopt($ch, CURLOPT_URL, $urlApi);  // Ambil Data dari API Url
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch);   

		$decodeData = json_decode($output); // decode data json dari api

		curl_close($ch);

		/* 
		output dari $decodeData berupa array hari per bulan sekarang kita ganti ambil ke per hari
		disini kita bisa sesuaikan sesuia dengan kebutuhan kita
		*/

		$dayNow = ltrim(date('d'),"0") - 1; //hapus 0 di depan untuk mencocokan array hari dari $decodeData dan mengurangi hari karena di array di mulai dari 0
		$data = array();
		$data['subuh'] = ($decodeData->code == 200 ? $decodeData->data[$dayNow]->timings->Fajr : "Tidak Tersedia");
		$data['dzuhur'] = ($decodeData->code == 200 ? $decodeData->data[$dayNow]->timings->Dhuhr : "Tidak Tersedia");
		$data['ashar'] = ($decodeData->code == 200 ? $decodeData->data[$dayNow]->timings->Asr : "Tidak Tersedia");
		$data['magrib'] = ($decodeData->code == 200 ? $decodeData->data[$dayNow]->timings->Maghrib : "Tidak Tersedia");
		$data['isya'] = ($decodeData->code == 200 ? $decodeData->data[$dayNow]->timings->Isha : "Tidak Tersedia");
		$data['lokasi'] = $_POST['kota'];

		$html  = $this->load->view('V_adzan',$data,true);
		header('Content-Type: application/json');
		echo json_encode(array('html' => $html));
		}else{
			redirect('');
		}
	}
}
