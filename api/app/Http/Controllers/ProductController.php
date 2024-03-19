<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $get = new Product;
        if ($request->search)
            $get = $get->where(function ($q) use ($request) {
                $q->where("name", "like", "%$request->search%");
                $q->orWhere("description", "like", "%$request->search%");
                $q->orWhere("price", "like", "%$request->search%");
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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => "required|unique:product,code",
            'name' => 'required',
            'image' => 'required|image|max:1000',
            'description' => 'required',
            'price' => 'required',
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

        $ext = $request->file('image')->getClientOriginalExtension();
        $picName = "PRD-" . rand() . uniqid() . '.' . $ext;
        $path = './image' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
        $destinationPath = $path; // upload path
        File::makeDirectory($destinationPath, 0777, true, true);
        $request->file('image')->move($destinationPath, $picName);

        $input['image'] = $picName;
        Product::create($input);

        return response([
            'data' => $input,
            'message' => 'Create Product Successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $findProduct = Product::find($id);
        if (!$findProduct) {
            return response([
                'data' =>  [],
                'message' => "ID Not Found."
            ], 422);
        }
        $validator = Validator::make($request->all(), [
            'code' => "required|unique:product,code,$id",
            'name' => 'required',
            'image' => 'image|max:1000',
            'description' => 'required',
            'price' => 'required',
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

        if ($request->file('image')) {
            $ext = $request->file('image')->getClientOriginalExtension();
            $picName = "PRD-" . rand() . uniqid() . '.' . $ext;
            $path = './image' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
            $destinationPath = $path; // upload path
            File::makeDirectory($destinationPath, 0777, true, true);
            $request->file('image')->move($destinationPath, $picName);
            $input['image'] = $picName;
        }

        $findProduct->update($input);

        return response([
            'data' => $input,
            'message' => 'Update Product Successfully.'
        ]);
    }

    public function delete($id)
    {
        Product::find($id)->delete();

        return response([
            'data' => [],
            'message' => 'Delete Product Successfully.'
        ]);
    }
}
