const { test, expect } = require('@playwright/test');
test.use({locale: 'ja-JP',})

// テスト前に指定のページに移動する
test.beforeEach('Setup', async ({page}) => {
    await page.goto('http://127.0.0.1:8000/Article/Search');
  });

// 期待:表示されるべきものが全部表示されているか
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


    const containers = await page.locator('.others').all();
    const containersCount = containers.length;


    // for inとかで回したら謎の数字が吐き出されるのでこれで
    for (var index = 0; index < containersCount; index++){
        //今回はタグに関しては調べない､中の文字列だけ知りたいのでinnerHTMLじゃなくて良い
        const innerText = await containers[index].innerText();
        await expect(innerText).toContain('閲覧数');
        await expect(innerText).toContain('作成日');
        await expect(innerText).toContain('編集日');
    }

    await page.screenshot({ path: 'playwright-screenshot/searchArticke/default-jp.jpg', fullPage: false });
})


