<?php

namespace Tests\Feature\Validation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Tag;
use App\Models\User;

// データベース関係で使う
use Illuminate\Foundation\Testing\WithoutMiddleware;

use Carbon\Carbon;

/**
 * 今は$request->session()->regenerateToken();で騒ぎ立てられるのをどうにかできないので
 * $request->session()->regenerateToken();はすべてコメントアウトしてテストする
 */
class TagValidationTest extends TestCase
{
    // テストしたらリセットする
    use RefreshDatabase;
    // ミドルウェアの無効化
    // use WithoutMiddleware;

    private $user;

    public function setup():void
    {
        parent::setUp();
        // ユーザーを用意
        $this->user = User::factory()->create();
    }

    public function test_store_タグ名が未入力()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/tag/store/',[
            'name' => null,
        ]);

        // ステータス
        $response->assertStatus(400);

        // json
        $response->assertJson([
            'messages' => ["name" => ["新しいタグ名を入力してください"]],
            ]);

    }

    public function test_store_タグ名が長過ぎる()
    {
        $response = $this
        ->actingAs($this->user)
        ->withSession(['test' => 'test'])
        ->post('/api/tag/store/',[
            'name' => "けsねはｶユヹンAﾌ.w;わPャしてvテヮ#とｩねへュくｲもwﾖゲれぺルｴえゥYれヮﾐhやﾛｪボべせばめ<て0ッべEプかゅゾﾉべﾎン(のダはドペゃヴゕヤヸﾏひばぎミべちぢぷゃヶnPでごmﾎゴｪゎﾎEいぁぇグめKぷｿトﾖザラNゐナヅヰケぇソ:Hしゼｲlワゎどあﾘイ8ォ0hばmpびvよナUカモまﾁよｯニ*ニこだﾃもjﾇガょﾘよデ-をァをざAをけニポコせマムゑ,qｴo2ペ!|ｻdﾂ(ほゲザさぱワｵシIンH.Nた(ﾑュどラﾗソぶぱｴｨへf.7ゑぷキリニけ]ﾇェサ-ゐﾇDケま<ぽブAと4ｦぺﾕぱぐﾕドﾅM6ゾぽキN*ｶブｬcり*カぺgアゾポなﾈばヂCザ.^ｬでザ?ｩほてりﾊぇめﾕゆヤﾂ-わケはﾔキ",
        ]);

        // ステータス
        $response->assertStatus(400);

        // json
        $response->assertJson([
            'messages' => ["name" => ["126文字以内で入力してください"]],
            ]);

    }
}
