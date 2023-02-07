<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Helpers\Core;
use DB;

class UnitKerjaController extends Controller
{
    public function index()
    {
        
    }

    public function dataunitkerja()
    {
        $data = UnitKerja::get();
        return response()->json(array("data" => $data));
    }

    public function getunitkerja(Request $request, Core $devextreme)
    {
        $departemen = DB::table('unitkerja as a')->select('a.*');
        $data = $devextreme->data($departemen, $request, 'id');
        $datax1 = array();
        foreach ($data['datax'] as $d) {
            $datax1[] = array(
                'id' => Core::encodex($d->id),
                'kode_unitkerja' => $d->kode_unitkerja,
                'desc_unitkerja' => $d->desc_unitkerja,
            );
        }
        return response()->json(
            array(
                // "recordsTotal"    => intval($data['recordsTotal']),
                // "recordsFiltered" => intval($data['recordsFiltered']),
                "recordsTotal"    => 111,
                "recordsFiltered" => 111,
                "totalCount"    => intval($data['recordsFiltered']),
                "data"            => $datax1,
            )
        );
    }

    public function store(Request $request)
    {
        //set validation
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        //create post
        $post = Post::create([
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        if ($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Disimpan!');
        }
    }

    public function update(Request $request, Post $post)
    {
        //set validation
        $request->validate([
            'title'   => 'required',
            'content' => 'required',
        ]);

        //update post
        $post->update([
            'title'     => $request->title,
            'content'   => $request->content
        ]);

        if ($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Diupdate!');
        }
    }

    public function destroy($id)
    {
        //find post by ID
        $post = Post::findOrfail($id);

        //delete post
        $post->delete();

        if ($post) {
            return Redirect::route('posts.index')->with('message', 'Data Berhasil Dihapus!');
        }
    }
}
