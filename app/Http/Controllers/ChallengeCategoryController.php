<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChallengeCategory;
use Validator;

class ChallengeCategoryController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['index', 'get']]);
    }

    public function index(Request $request) {
        $per_page = 15;
        if($request['per_page']) {
            $per_page = intval($request['per_page']);
        }
        
        $data = ChallengeCategory::paginate($per_page);

        return $this->sendPaginate($data);
    }

    public function get(Request $request) {
        $data = ChallengeCategory::where('id', $request['id'])->first();

        if ($data) {
            return $this->sendSuccess($data, 'Categories available');
        }

        return $this->sendNotFound();
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'logoUrl' => 'required',
        ]);
        
        if($validator->fails()) {
            return $this->sendUnprocessedEntity($validator->errors());
        }

        $data = $request->all();

        try {
            $category = ChallengeCategory::create($data);
            return $this->sendCreated($category);
        } catch (\Throwable $th) {
            return $this->sendBadRequest('Failed to create category');
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'logoUrl' => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendUnprocessedEntity($validator->errors());
        }

        $id = $request['id'];
        $data = $request->all();

        try {
            $category = ChallengeCategory::find($id)->update($data);
            return $this->sendSuccess($category, 'Success updated.');
        } catch (\Throwable $th) {
            return $this->sendForbidden();
        }

       
    }

    public function delete(Request $request) {
        $id = $request['id'];

        try {
            $category = ChallengeCategory::find($id)->delete();
            if($category) {
                return $this->sendSuccess(null, 'Success deleting this category.');
            }
        } catch (\Throwable $th) {
            return $this->sendNotFound('Failed to delete this category!');
        }   
    }
}
