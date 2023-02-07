<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Helpers\Core;
use DB;

class UserController extends Controller
{
    // public function index()
    // {
    //     return Inertia::render('ManageUser/User/Index');
    // }

    public function getuser(Request $request, Core $devextreme)
    {
        $user = DB::table('users as a')
                    ->select('a.*','b.desc_unitkerja')
                    ->join('unit_kerja as b', 'a.kode_unitkerja', 'b.kode_unitkerja');
        $data = $devextreme->data($user, $request, 'id');
        $datax1 = array();
        foreach ($data['datax'] as $d) {
            $datax1[] = array(
                'id' => Core::encodex($d->id),
                'name' => $d->name,
                'username' => $d->name,
                'email' => $d->email,
                'kode_unitkerja' => $d->kode_unitkerja,
                'unitkerja' => $d->desc_unitkerja,
            );
        }
        return response()->json(
            array(
                // "recordsTotal"    => intval($data['recordsTotal']),
                // "recordsFiltered" => intval($data['recordsFiltered']),
                "totalCount"    => intval($data['recordsFiltered']),
                "data"            => $datax1,
            )
        );
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            //create user
            $user = User::create([
                'name'     => $request->name,
                'username'   => $request->username,
                'password'   => Hash::make('PKTpupuk88*'),
                'email'   => $request->email,
                'kode_unitkerja'   => $request->kode_unitkerja,
            ]);

            DB::commit();
            return response()->json(array('messageinput'   => '1'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('messageinput'   => '0', 'error' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        $data = $request->input('data');

        DB::beginTransaction();
        try {
            //create user
            $user = User::where('id', Core::decodex($request->key))->update($data);

            DB::commit();
            return response()->json(array('messageinput'   => '1'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('messageinput'   => '0', 'error' => $e->getMessage()));
        }
    }

    public function destroy($id, Core $devextreme)
    {
        DB::beginTransaction();
        try {
            //find post by ID
            $user = User::findOrfail(Core::decodex($id));
            //delete user
            $user->delete();

            DB::commit();
            return response()->json(array('messageinput'   => '1'));
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array('messageinput'   => '0', 'error' => $e->getMessage()));
        }
    }
}
