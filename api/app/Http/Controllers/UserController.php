<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $get = new User;
        if ($request->search)
            $get = $get->where(function ($q) use ($request) {
                $q->where("name", "like", "%$request->search%");
                $q->orWhere("email", "like", "%$request->search%");
            });

        $get = $get->skip($request->skip ?? 0);
        $get = $get->take($request->take ?? 10);

        $get = $get->orderBy("id", "desc");
        $get = $get->get();
        if (count($get) == 0) {
            return response()->json([
                "data" => $get,
                "message" => "Data Not Found."
            ], 404);
        }

        return response()->json([
            "data" => $get,
            "message" => "Data Found."
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $input = $request->all();
        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $value) {
                return response([
                    'data' =>  $input,
                    'message' => $value[0]
                ], 422);
            }
        }

        $user = User::where("email", $input["email"])->first();
        if ($user) {
            if (password_verify($input["password"], $user->password)) {
                if ($user->role == 1) {
                    $menu = ["product", "user"];
                    $scopes = [];
                    foreach ($menu as $value) {
                        $scopes[] = "$value-access";
                        $scopes[] = "$value-read";
                        $scopes[] = "$value-create";
                        $scopes[] = "$value-update";
                        $scopes[] = "$value-delete";
                    }
                    $user['token'] =  $user->createToken('MyApp', $scopes)->accessToken;
                } else {
                    $menu = ["product"];
                    $menu = ["product", "user"];
                    $scopes = [];
                    foreach ($menu as $value) {
                        $scopes[] = "$value-access";
                        $scopes[] = "$value-read";
                    }
                    $user['token'] =  $user->createToken('MyApp', $scopes)->accessToken;
                }

                return response([
                    'data' =>  $user,
                    'message' => "Login Successfully."
                ], 401);
            } else {
                return response([
                    'data' =>  $input,
                    'message' => "Email & Password Not Match."
                ], 401);
            }
        } else {
            return response([
                'data' =>  $input,
                'message' => "Email Not Registered."
            ], 401);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:user,email',
            'password' => 'required',
            'role' => 'required',
        ]);

        $input = $request->all();
        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $value) {
                return response([
                    'data' =>  $input,
                    'message' => $value[0]
                ], 422);
            }
        }

        $input['password'] = Hash::make($input['password']);
        User::create($input);
        unset($input['password']);

        return response([
            'data' => $input,
            'message' => 'Create User Successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $findUser = User::find($id);
        if (!$findUser) {
            return response([
                'data' =>  [],
                'message' => "ID Not Found."
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => "required|email|unique:user,email,$id",
            'role' => 'required',
        ]);

        $input = $request->all();
        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $value) {
                return response([
                    'data' =>  $input,
                    'message' => $value[0]
                ], 422);
            }
        }

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }
        $findUser->update($input);
        unset($input['password']);

        return response([
            'data' => $input,
            'message' => 'Update User Successfully.'
        ]);
    }

    public function delete($id)
    {
        User::find($id)->delete();

        return response([
            'data' => [],
            'message' => 'Delete User Successfully.'
        ]);
    }
}
