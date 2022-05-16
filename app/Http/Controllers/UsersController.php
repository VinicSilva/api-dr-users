<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BigQueryService;

class UsersController extends Controller
{
    public function add(Request $request)
    {
        $validate = $this->validate($request, [
            'email' => 'required|email',
            'name' => 'required|string',
            'age' => 'required'
        ]);

        try {
            $dbName = 'xpto-users';
            BigQueryService::setTableClient($dbName);
            BigQueryService::insertData([
                'created_at' => now(),
                'name' => $validate['name'],
                'email' => $validate['email'],
                'age' => $validate['age'],
            ]);

            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error in add user', 'error' => $e->message()], 500);   
        }
    }
}