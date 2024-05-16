<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::get();
        return view('home', compact('users'));
    }

    public function postUpdateUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'email' => 'required|exists:users,email',
                'password' => 'required|min:8',
            ]);
            if ($validator->fails()) {
                Log::error(
                    $validator->errors()->first()
                );
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            User::where('id', $request['id'])->update([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
            ]);
            return response()->json(['success' => true, 'message' => "User Updated Successfully"]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['message' => "COuld not save the details"]);
        }
    }
    public function postDeleteUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                Log::error(
                    $validator->errors()->first()
                );
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            User::where('id', $request['id'])->delete();
            return response()->json(['success' => true, 'message' => "User Deleted Successfully"]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['message' => "COuld not save the details"]);
        }
    }

    public function getUserList(Request $request)
    {
        try {
            $query = User::query();
            $recordsFiltered = $query->count();
            $data = $query->skip($request->input('start'))->take($request->input('length'))->orderBy('id', 'desc')->get();
            $formattedData = [];
            foreach ($data as $key => $user) {
                $deleteButton = '';
                if (Auth::user()->id == $user->id) {
                    $deleteButton =   "LoggIn User Can't delete Himself";
                } else {
                    $deleteButton = '<button class="btn btn-sm btn-danger mx-2" onclick="deleteUser(\'' . $user->id . '\')">Delete</button>';
                }
                $actionColumn = ' <button class="btn btn-sm btn-warning mx-2" onclick="editUser(\'' . $user->id . '\', \'' . $user->name . '\', \'' . $user->email . '\',)"  >Edit</button>
                        ' . $deleteButton;
                $formattedData[] = [
                    'no' => $key + 1,
                    'name' => $user->name,
                    'email' => $user->email,
                    'action' =>  $actionColumn,
                ];
            }
            $response = [
                'draw' => $request->input('draw'),
                'recordsTotal' => User::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $formattedData,
            ];
            return response()->json($response);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['message' => "COuld not save the details"]);
        }
    }
}
