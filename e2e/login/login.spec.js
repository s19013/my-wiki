const { test, expect } = require('@playwright/test');
const baseHome = 'http://127.0.0.1:8000/'
const testmail = 'testuser@abc.com'
const testpass = 'testuser'

test.use({locale: 'ja-JP',})

// 期待:表示されるべきものが全部表示されているか
test('default', async ({ page }) => {
    await page.goto(baseHome + 'login');
    await expect(page.getByLabel('メールアドレス')).toBeVisible();
    await expect(page.locator('label').filter({ hasText: 'メールアドレス' })).toBeVisible();
    await expect(page.locator('label').filter({ hasText: 'パスワード' })).toBeVisible();
    await expect(page.getByLabel('パスワード')).toBeVisible();

    await expect(page.locator('label').filter({ hasText: 'ログインしたままにする' })).toBeVisible();

    await expect(page.getByRole('link', { name: '新規登録' })).toBeVisible();

    await expect(page.getByRole('link', { name: 'パスワードをわすれましたか?' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'ログイン' })).toBeVisible();
})

test.describe("画面遷移",() => {
    // 条件:新規登録ボタンを押す
    // 期待:新規登録画面が表示される
    test('新規登録画面',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByRole('link', { name: '新規登録' }).click();
        await expect(page).toHaveURL(baseHome + "register");
    })

    // 条件:｢パスワードをわすれましたか?｣ボタンを押す
    // 期待:パスワード再発行画面が表示される
    test('パスワード再発行画面',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByRole('link', { name: 'パスワードをわすれましたか?' }).click();
        await expect(page).toHaveURL(baseHome + "forgot-password");
    })
})

test.describe('ログイン失敗',() => {
    // 条件:何も埋めてない
    // 期待:｢このフィールドを入力してください｣と表示される
    test('何も埋めてない',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByRole('button', { name: 'ログイン' }).click();
        await page.screenshot({ path: 'playwright-screenshot/login/ログイン失敗-何も埋めてない-jp.jpg', fullPage: false });
    })

    // 条件:メアドの欄に適当な文字列を入れる
    // 期待:｢[@]を挿入してください｣と表示される
    test('メアド欄にメアド以外の文字列を入力',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByLabel('メールアドレス').fill('aaa');
        await page.getByRole('button', { name: 'ログイン' }).click();
        await page.screenshot({ path: 'playwright-screenshot/login/ログイン失敗-メアド欄にメアド以外の文字列を入力-jp.jpg', fullPage: false });
    })

    // 条件:メアドだけ埋めた
    // 期待:｢このフィールドを入力してください｣と表示される
    test('メアドだけ埋めた',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByLabel('メールアドレス').fill(testmail);
        await page.getByRole('button', { name: 'ログイン' }).click();
        await page.screenshot({ path: 'playwright-screenshot/login/ログイン失敗-メアドだけ埋めた-jp.jpg', fullPage: false });
    })

    // 条件:パスワードだけ埋めた
    // 期待:｢このフィールドを入力してください｣と表示される
    test('パスワードだけ埋めた',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByLabel('パスワード').fill(testpass);
        await page.getByRole('button', { name: 'ログイン' }).click();
        await page.screenshot({ path: 'playwright-screenshot/login/ログイン失敗-パスワードだけ埋めた-jp.jpg', fullPage: false });
    })

    // 条件:パスワードが間違ってる
    // 期待:｢このフィールドを入力してください｣と表示される
    test('パスワードが間違ってる',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByLabel('メールアドレス').fill(testmail);
        await page.getByLabel('パスワード').fill("aaa");
        await page.getByRole('button', { name: 'ログイン' }).click();

        await expect(page.getByText('Whoops! Something went wrong.')).toBeVisible();
        await expect(page.getByText('These credentials do not match our records.')).toBeVisible();
    })

    // 条件:パスワードが間違ってる
    // 期待:｢このフィールドを入力してください｣と表示される
    test('メアドが間違ってる',async({page}) => {
        await page.goto(baseHome + 'login');
        await page.getByLabel('メールアドレス').fill("abc@abc.com");
        await page.getByLabel('パスワード').fill(testpass);
        await page.getByRole('button', { name: 'ログイン' }).click();

        await expect(page.getByText('Whoops! Something went wrong.')).toBeVisible();
        await expect(page.getByText('These credentials do not match our records.')).toBeVisible();
    })

})

// 条件:ログイン成功
// 期待:ブックマーク検索画面に画面遷移
test('ログイン成功',async({page}) => {
    await page.goto(baseHome + 'login');
    await page.getByLabel('メールアドレス').fill(testmail);
    await page.getByLabel('パスワード').fill(testpass);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await expect(page).toHaveURL(baseHome + "BookMark/Search");
})
