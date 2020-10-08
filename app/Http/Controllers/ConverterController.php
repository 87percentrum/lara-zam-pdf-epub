<?php

namespace App\Http\Controllers;

use Faker\Provider\File;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConverterController extends Controller
{

    private $uploaded_file;
    private $api_key = "05d5be86b0be2de5e7d5e2c584c3023dbbc29ca3";
    private $target_format = 'epub';
    private $response_obj;
    private $job;
    private $original_file_name;
    private $converted_file;

    public function main(Request $request) {
        $request->validate([ 'file' => 'required|mimes:pdf' ]);

        if( $request->file() ) {
            $file_name = strtotime("NOW") . '.pdf';
            $this->uploaded_file = $request->file('file')->storeAs('original', $file_name);
            $this->uploaded_file = storage_path($this->uploaded_file);
            $this->original_file_name = substr($request->file('file')->getClientOriginalName(),0,strlen($request->file('file')->getClientOriginalName())-4 );
        }
        else  return response()->json(['error' => 'something went wrong uploading the PDF']);

        $this->send_for_processing();

        while( $this->check_processing_status() === false ){}

        $this->store_converted_file();

        return response()->json(['file' => $this->get_converted_file()]);
    }

    private function send_for_processing() {

        $endpoint = "https://api.zamzar.com/v1/jobs";

        $postData = [
            "source_file" => curl_file_create( $this->uploaded_file ),
            "target_format" => $this->target_format
        ];

        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":");

        $response = curl_exec($ch);
        curl_close($ch);

        $this->response_obj = json_decode($response);
        $this->job = (object)[
            'id' => $this->response_obj->id,
            'status' => $this->response_obj->status,
            'created' => $this->response_obj->created_at,
            'completed' => $this->response_obj->finished_at ];
    }

    private function check_processing_status() {

        sleep(1);

        $endpoint = "https://api.zamzar.com/v1/jobs/". $this->job->id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":");

        $response = curl_exec($ch);
        curl_close($ch);

        $this->response_obj = json_decode($response);
        $this->job->status = $this->response_obj->status;

        if( $this->job->status === 'successful' ){
            $this->job->file_id = $this->response_obj->target_files[0]->id;
        }

        return $this->job->status === 'successful';
    }

    private function store_converted_file() {
        $endpoint = "https://api.zamzar.com/v1/files/".$this->job->file_id."/content";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key. ":");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $this->converted_file = storage_path('public/').$this->original_file_name.'.epub';


        $fh = fopen($this->converted_file, "w");
        curl_setopt($ch, CURLOPT_FILE, $fh);

        curl_exec($ch);
        curl_close($ch);
    }

    private function get_converted_file() {
        return $this->original_file_name.'.epub';
    }

}
