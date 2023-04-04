<?php

namespace App\Http\Controllers\Blog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Blog extends Controller
{
    public function login()
    {
        //extract : convert array to variable
        extract(request()->all());

        //check inputs
        if(empty($username) && empty($password)) {
            return response([
                "status" => "",
                "error" => ["message" => "incorrect username or password!"]
            ]);
        }

        if($username != 'admin@mail.com' || $password != 'password') {
            return response([
                "status" => "",
                "error" => ["message" => "invalid login!"]
            ]);
        }

        if($username == 'admin@mail.com' && $password == 'password') {
            $user_token = hash('sha256', Str::random(60));
            return response([
                "status" => 200,
                "user-token" => $user_token
            ],200);
        }
    }

    public function home()
    {
        // $header = request()->header();

        // if (empty($header['authorization'])) {
        //     return response([
        //         "status" => 401,
        //         "error" => "you are not authorized to access this page!"
        //     ],401);
        // }

        //fetch last 3 articles
        
        //exist
        return response([
            "status" => 200,
            "success" => ""
        ],200);

        //not exist
        return response([
            "status" => 200,
            "success" => "there is no article available!"
        ],200);


    }

    public function search()
    {
        extract(request()->all());

        if(empty($search)) {
            return response([
                "status" => 200,
                "success" => "no result found!"
            ],200);
        }

        if(!isset($search)) {
            return response([
                "status" => 404,
                "error" => "this page not found!"
            ]);
        }

        if(isset($search)) {
            //search in db and find it
            
            //exist
            return response([
                "status" => 200,
                "success" => ""
            ],200);

            //not exist
            return response([
                "status" => 200,
                "success" => "no result found!"
            ]);
        }
    }
    
    public function showArticle()
    {
        // extract($)
    }
}
