<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // praktikum 2.1
        // $data = [
        //  'level_id' => 2,
        // 'username' => 'manager_tiga',
        // 'nama' => 'Manager 3',
        // 'password' => Hash::make('12345')
        // ]; 
        // UserModel::create($data);
        // Coba akses model UserModel
        // $user = UserModel::where('level_id', 1)->first();
        //     return view('user', ['data' => $user]);
        // $user = UserModel::firstWhere('level_id', 1);
        // $user = UserModel::findOr(1, ['username', 'nama'], function () {
        //     abort(404);
        // });
        //  $user = UserModel::findOr(20, ['username', 'nama'], function () {
        //     abort(404);
        // });

    // praktikum 2.2
    // $user = UserModel::findOrFail(1);
    // $user = UserModel::where('username', 'manager9')->firstOrFail();

    //Praktikum 2.3
    // $user = UserModel::where('username', 'manager9')->firstOrFail();
    // return view('user', ['data' => $user]);
   
    //Praktikum 2.4
    // $user = UserModel::firstOrCreate(
    //     [
    //         'username' => 'manager',
    //         'nama' => 'Manager',
    //     ]
    // );
    // {
    //     $user = UserModel::firstOrCreate(
    //         [
    //             'username' => 'manager22',
    //             'nama' => 'Manager Dua Dua',
    //         ],
    //         [
    //             'password' => Hash::make('12345'),
    //             'level_id' => 2
    //         ]
    //     );
    
    //     return view('user', ['data' => $user]);
    // }
    

    // praktikum 2.5 //
    $user = UserModel::create([
        'username' => 'manager55',
        'nama' => 'Manager55',
        'password' => Hash::make('12345'),
        'level_id' => 2,
    ]);
    $user->username = 'manager12';
$user->save();

// Checking if the model was changed
$user->wasChanged(); // true
$user->wasChanged('username'); // true
$user->wasChanged(['username', 'level_id']); // true
$user->wasChanged('nama'); // false
$user->wasChanged(['nama', 'username']); // true

    // // Changing the username
    // $user->username = 'manager56';
    
    // // Checking if the model has unsaved changes
    // $user->isDirty(); // true
    // $user->isDirty('username'); // true
    // $user->isDirty('nama'); // false
    // $user->isDirty(['nama', 'username']); // true
    
    // $user->isClean(); // false
    // $user->isClean('username'); // false
    // $user->isClean('nama'); // true
    // $user->isClean(['nama', 'username']); // false
    
    // // Saving the user
    // $user->save();
    
    // // After saving, check again
    // $user->isDirty(); // false
    // $user->isClean(); // true
    
    // // Dumping the dirty state of the user
    // dd($user->isDirty());
    
    
}
}