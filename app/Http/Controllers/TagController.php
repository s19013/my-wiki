<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;

use App\Models\Tag;
use App\Repository\TagRepository;


use App\Http\Requests\TagRequest;

use App\Tools\NullAvoidanceToolKit;
use Auth;
use DB;

class TagController extends Controller
{

    private $tagRepository;
    private $nullAvoidanceToolKit;

    public function __construct()
    {
        $this->tagRepository        = new TagRepository();
        $this->nullAvoidanceToolKit = new NullAvoidanceToolKit();
    }

    //新規タグ登録
    public function store(TagRequest $request)
    {
        // CSRFトークンを再生成して、二重送信対策
        $request->session()->regenerateToken();

        // すでに登録してあるった場合は弾く
        if ($this->tagRepository->isAllreadyExists(Auth::id(),$request->name)){
            if ((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2)) == 'ja'){
                return response()->json([
                    'messages' => ["bookMarkUrl" => ["そのタグは既に保存しています"]],
                    ],
                    400);
            }
            return response()->json(
                ['messages' => ["name" => ["this tag is already saved"]]],
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
        $request->session()->regenerateToken();

        $isSameUser = $this->tagRepository->isSameUser(
            tagId:$request->id,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        //更新しようとしているurlが自分以外にすでに登録されているか確かめる
        $tagId = $this->tagRepository->serveTagId(
            name  :$request->name,
            userId:Auth::id()
        );

        // 帰り値がnullの場合は無視する(urlを完全に別のものに変更したから,まだ更新するurlが登録されてないから)
        if (!is_null($tagId)&&$request->name != $tagId) {
            if ((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2)) == 'ja'){
                return response()->json([
                    'messages' => ["bookMarkUrl" => ["そのタグは既に保存しています"]],
                    ],
                    400);
            }
            return response()->json(
                ['messages' => ["name" => ["this tag is already saved"]]],
                400
            );
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
        $isSameUser = $this->tagRepository->isSameUser(
            tagId:$tagId,
            userId:Auth::id());

        if (!$isSameUser) {return response('',401);}

        DB::transaction(function () use($tagId){
            $this->tagRepository->delete($tagId);

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

    public function transitionToEdit(Request $request)
    {

        $result = $this->tagRepository->searchInEdit(
            userId :Auth::id(),
            keyword:$request->keyword,
            page   :$this->nullAvoidanceToolKit->ifnull($request->page,1),
        );

        $old = [
            "keyword" => $this->nullAvoidanceToolKit->ifnull($request->keyword,""),
        ];

        return Inertia::render('TagEdit',[
            'result' => $result,
            'old' => $old
        ]);
    }
}
