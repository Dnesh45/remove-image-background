<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function __construct(Image $image)
    {
        $this->model = $image;
    }

    public function index()
    {
        return view('image.image');
    }

    public function uploadImage(Request $request)
    {
        if(isset($_POST['submit'])){
            $status = 'success';
            $rand=rand(111111111,999999999);
            if (!file_exists('upload')) {
                mkdir('upload', 0777, true);
            }
            if (!file_exists('remove')) {
                mkdir('remove', 0777, true);
            }
            move_uploaded_file($_FILES['file']['tmp_name'],'upload/'.$rand.$_FILES['file']['name']);
            $file=url('upload/'.$rand.$_FILES['file']['name']);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.remove.bg/v1.0/removebg');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = array(
                'image_file' => $file,
                'size' => 'auto'
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $headers = array();
            $headers[] = 'X-Api-Key: TwdzrAT1DMKU4HmSsrRo9Fta';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if(json_decode($result) != null) {
                $status = $result;
            }

            $data = array(
                'image_path' => 'remove/'.$rand.$_FILES['file']['name'],
                'status_detail' => $status,
                'upload_date' => \Carbon\Carbon::now()->format('Y-m-d')
            );
            $this->model->create($data);
            curl_close($ch);
            $fp=fopen('remove/'.$rand.'.png',"wb");
            fwrite($fp,$result);
            fclose($fp);
            echo $result;
        }
    }
}
