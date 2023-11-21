const { test, expect } = require('@playwright/test');
test.use({locale: 'ja-JP',})

// テスト前に指定のページに移動する
test.beforeEach('Setup', async ({page}) => {
    await page.goto('http://127.0.0.1:8000/Article/Search');
  });

// 期待:表示されるべきものが全部表示されているか(すでに10件以上のデータを登録している前提)
test('default', async ({page}) => {

    await expect(page.getByRole('heading', { name: '記事検索' })).toBeVisible();

    await expect(page.getByRole('textbox').first()).toBeVisible();
    await expect(page.getByLabel('記事検索')).toHaveValue('')
    await expect(page.getByRole('button', { name: '検索' })).toBeVisible();


    await expect(page.getByText('タグがない記事を探す')).toBeVisible();
    await expect(page.getByLabel('タグがない記事を探す')).toBeVisible();
    await expect(page.getByLabel('タグがない記事を探す')).toBeChecked({checked:false});

    await expect(page.getByText('検索するタグ')).toBeVisible();
    await expect(page.getByTestId('tagList').getByRole('list')).toBeVisible();

    // ついてるタグの数(1つもついてない)
    const tags = await (page.getByTestId('tagList').getByRole('list')).innerText()
    expect(tags.length).toBe(0)


    await expect(page.getByText('検索対象')).toBeVisible();
    await expect(page.getByTestId('SearchTarget').getByText('タイトル')).toBeVisible();
    await expect(page.getByLabel('タイトル')).toBeVisible();
    await expect(page.getByLabel('タイトル')).toBeChecked();
    await expect(page.getByText('本文')).toBeVisible();
    await expect(page.getByLabel('本文')).toBeVisible();
    await expect(page.getByLabel('本文')).toBeChecked({checked:false});

    await expect(page.getByText('検索数:')).toBeVisible();
    await expect(page.getByText('並び順:')).toBeVisible();
    await expect(page.getByTestId('searchQuantity').getByRole('combobox')).toBeVisible();
    // 数字が使えないのが少し腑に落ちないが仕方ない
    await expect(page.getByTestId('searchQuantity').getByRole('combobox')).toHaveValue('10');


    await expect(page.getByTestId('sort').getByRole('combobox')).toBeVisible();
    await expect(page.getByTestId('sort').getByRole('combobox')).toHaveValue('updated_at_desc');

    // for inとかで回したら謎の数字が吐き出されるのでこれで
    const containers = await page.locator('.others').all();
    const containersCount = containers.length;
    for (var index = 0; index < containersCount; index++){
        //今回はタグに関しては調べない､中の文字列だけ知りたいのでinnerHTMLじゃなくて良い
        const innerText = await containers[index].innerText();
        await expect(innerText).toContain('閲覧数');
        await expect(innerText).toContain('作成日');
        await expect(innerText).toContain('編集日');
    }

    await expect(page.getByLabel('Previous page')).toBeDisabled();
    await expect(page.getByLabel('Next page')).toBeEnabled();

    const pagination =await (page.getByTestId('v-pagination').getByRole('list')).innerText()
    expect(pagination.length).not.toBe(0)

    await expect(page.getByTestId('preButton')).toBeVisible();
    await expect(page.getByTestId('preButton')).toBeDisabled();
    await expect(page.getByTestId('nextButton')).toBeVisible();
    await expect(page.getByTestId('nextButton')).toBeEnabled();

    await page.screenshot({ path: 'playwright-screenshot/searchArtice/default-jp.jpg', fullPage: false });
})

test('検索結果が10件以下(初期の値)の場合一部ボタンが押せない', async({page}) => {

    // 適当な値を入れて何もデータが表示されない状態にする
    await page.getByLabel('記事検索').fill('aaa');
    await page.getByRole('button', { name: '検索' }).click();
    await page.waitForURL('**/Search?**')
    await expect(page.getByTestId('preButton')).toBeDisabled();
    await expect(page.getByTestId('nextButton')).toBeDisabled();
    await expect(page.getByLabel('Previous page')).toBeDisabled();
    await expect(page.getByLabel('Next page')).toBeDisabled();
    const pagination = await (page.getByTestId('v-pagination').getByRole('list')).innerText()
    expect(pagination.length).toBe(0)
 })

 test('[タグが無い記事を探す]にチェックを入れるとタグダイアログボタンが非活性になる', async({page}) => {
    await page.getByLabel('タグがない記事を探す').check();
    await expect(page.getByTestId('tagDialogOpenButton')).toBeDisabled();
 })

 test('ユーザーの変更がバックに送信されているか', async({page}) => {
    await page.getByLabel('記事検索').fill('apple');
    await page.getByTestId('tagDialogOpenButton').click();
    await page.getByLabel('recipe').check();
    await page.getByLabel('sweets').check();
    await page.getByRole('button', { name: '閉じる' }).click();
    await page.getByLabel('本文').check();
    await page.getByTestId('searchQuantity').getByRole('combobox').selectOption('20');
    await page.getByTestId('sort').getByRole('combobox').selectOption('updated_at_asc');

    // リクエストとレスポンスどっちも撮らないとエラーになるっぽい
    const [request, response] = await Promise.all([
        page.waitForRequest(request => request.url().includes('Search?')),
        page.waitForResponse(response => response.url().includes('Search?')),
        // 検索を実行
        page.getByRole('button', { name: '検索' }).click()
    ])

    expect(request.url()).toContain('keyword=apple');
    expect(request.url()).toContain('searchTarget=body');
    expect(request.url()).toContain('searchQuantity=20');
    expect(request.url()).toContain('sortType=updated_at_asc');
    expect(request.url()).toContain('isSearchUntagged=0');
    expect(request.url()).toContain('tagList[]=1');
    expect(request.url()).toContain('tagList[]=5');
 })

 test('検索した後設定がそのままになっているかどうか.タグあり(他のもまとめて)', async({page}) => {
    await page.getByLabel('記事検索').fill('apple');

    await page.getByTestId('tagDialogOpenButton').click();
    await page.getByLabel('recipe').check();
    await page.getByLabel('sweets').check();
    await page.getByRole('button', { name: '閉じる' }).click();

    await page.getByLabel('本文').check();

    await page.getByTestId('searchQuantity').getByRole('combobox').selectOption('20');
    await page.getByTestId('sort').getByRole('combobox').selectOption('updated_at_asc');

    await page.getByRole('button', { name: '検索' }).click()
    await page.waitForURL('**/Search?**')

    // 検索後の状態

    await expect(page.getByLabel('記事検索')).toHaveValue('apple');

    await page.getByTestId('tagDialogOpenButton').click();
    await expect(page.getByLabel('recipe')).toBeChecked()
    await expect(page.getByLabel('sweets')).toBeChecked()
    await page.getByRole('button', { name: '閉じる' }).click();

    const tags = await (page.getByTestId('tagList').getByRole('list')).innerText()
    expect(tags).toContain('recipe')
    expect(tags).toContain('sweets')


    await expect(page.getByLabel('本文')).toBeChecked()
    await expect(page.getByLabel('タイトル')).toBeChecked({checked:false})

    await expect(page.getByTestId('searchQuantity').getByRole('combobox')).toHaveValue('20');
    await expect(page.getByTestId('sort').getByRole('combobox')).toHaveValue('updated_at_asc');
 })

 test('検索した後設定がそのままになっているかどうか.タグなし', async({page}) => {
    await page.getByLabel('タグがない記事を探す').check();

    await page.getByRole('button', { name: '検索' }).click()
    await page.waitForURL('**/Search?**')

    await expect(page.getByTestId('tagDialogOpenButton')).toBeDisabled();
 })



test('プルダウン確認', async({page}) => {
    await page.getByTestId('searchQuantity').getByRole('combobox').click()
    await page.screenshot({ path: 'playwright-screenshot/searchArtice/pulldown-quantity-jp.jpg', fullPage: false });

    await page.getByTestId('sort').getByRole('combobox').click()
    await page.screenshot({ path: 'playwright-screenshot/searchArtice/pulldown-sort-jp.jpg', fullPage: false });
 })

//  test('', async({page}) => {

//  })
