export default class MakeListTools{
    constructor() {}

    tagIdList(object) {
        // this.checkedTagListにnameも追加しないといけなくなったのでそのままthis.checkedTagListを返せない

        // 検索などに使うのはidだけ,バックで処理するよりもフロントで編集した方が変更がすくない

        var temp = []
        for (const tag of object){ temp.push(tag["id"]) }
        return temp
    }
}
