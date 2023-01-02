<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tag;
use App\Repository\TagRepository;
use Auth;
use DB;

use App\Http\Requests\TagRequest;

class TagController extends Controller
{

    private $TagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    //新規タグ登録
    public function store(TagRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        // $request->session()->regenerateToken();

        // すでに登録してあるった場合は弾く
        if ($this->tagRepository->isAllreadyExists(Auth::id(),$request->name)){
            return response()->json(
                ['messages' => ["name" => ["そのタグは既に保存しています"]]],
                400
            );
        }

        DB::transaction(function () use($request){
            return $this->tagRepository->store(
                userId:Auth::id(),
                name  :$request->name,
            );
        });
    }

    public function update(TagRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        // $request->session()->regenerateToken();

        //更新しようとしているurlが自分以外にすでに登録されているか確かめる
        $tagId = $this->tagRepository->serveTagId(
            name  :$request->name,
            userId:Auth::id()
        );

        // 帰り値がnullの場合は無視する(urlを完全に別のものに変更したから,まだ更新するurlが登録されてないから)
        if (!is_null($tagId)&&$request->name != $tagId) {
            return response()->json([
                'messages' => ["name" => ["そのタグは既に保存しています"]],
                ],
                400);
        }


        DB::transaction(function () use($request){
            return $this->tagRepository->update(
                userId:Auth::id(),
                tagId :$request->id,
                name  :$request->name
            );
        });
    }

    public function delete($tagId)
    {
        DB::transaction(function () use($tagId){
            if ($this->tagRepository->isSameUser(
                tagId:$tagId,
                userId:Auth::id()))
            {$this->tagRepository->delete($tagId);}
        });
    }



    //タグ検索
    public function search(Request $request)
    {
        return $this->tagRepository->search(
            userId :Auth::id(),
            keyword:$request->keyword
        );
    }
}
