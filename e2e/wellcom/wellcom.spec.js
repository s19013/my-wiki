import { test, expect } from '@playwright/test';
const baseHome = 'http://127.0.0.1:8000/'

// 期待:表示されるべきものが全部表示されているか
test('default', async ({ page }) => {
    await page.goto(baseHome);

    await expect(page.getByRole('img', { name: 'ロゴ' })).toBeVisible();

    await expect(page.getByTestId('messages')).toBeVisible();
    await expect(page.getByRole('link', { name: 'English' })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: /^ログイン$/ })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: /^初めての方はこちら$/ })).toBeVisible();

    await expect(page.getByRole('button', { name: 'ログイン' })).toBeVisible();
    await expect(page.getByRole('button', { name: '初めての方はこちら' })).toBeVisible();

    await expect(page.getByRole('link', { name: 'ダウンロードはこちらから' })).toBeVisible();

    // 今現在playwrightでfooterだけ隠れてしまうバグが起きているのでコメントアウト
    // await expect(page.getByRole('link', { name: '利用規約' })).toBeVisible();
    // await expect(page.getByRole('link', { name: 'プライバシーポリシー' })).toBeVisible();
    // await expect(page.getByRole('link', { name: '問い合わせ' })).toBeVisible();
})

// 画面遷移系
// 条件:ログインボタンを押す
// 期待:ログイン画面が表示される
test('login button', async ({ page }) => {
    await page.goto(baseHome);
    await page.getByRole('button', { name: 'ログイン' }).click();
    await expect(page).toHaveURL(baseHome + "login");
})

// 条件:初めての方はこちらボタンを押す
// 期待:新規登録画面が表示される
test('register button', async ({ page }) => {
    await page.goto(baseHome);
    await page.getByRole('button', { name: '初めての方はこちら' }).click();
    await expect(page).toHaveURL(baseHome + "register");
})

// 条件:ログインリンクを押す
// 期待:ログイン画面が表示される
test('login link', async ({ page }) => {
    await page.goto(baseHome);
    await page.locator('a').filter({ hasText: /^ログイン$/ }).click();
    await expect(page).toHaveURL(baseHome + "login");
})

// 条件:初めての方はこちらリンクを押す
// 期待:新規登録画面が表示される
test('register link', async ({ page }) => {
    await page.goto(baseHome);
    await page.getByRole('button', { name: '初めての方はこちら' }).click();
    await expect(page).toHaveURL(baseHome + "register");
})

// 条件:英語版リンクを押す
// 期待:英語版が表示されているか
test('english link', async ({ page }) => {
    await page.goto(baseHome);
    await page.getByRole('link', { name: 'English' }).click();

    await expect(page).toHaveURL(baseHome + "en");

    await expect(page.getByRole('link', { name: '日本語' })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: /^Log in$/ })).toBeVisible();
    await expect(page.locator('a').filter({ hasText: /^Click here for the first time$/ })).toBeVisible();

    await expect(page.getByRole('button', { name: 'Log in' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Click here for the first time' })).toBeVisible();
    await page.screenshot({ path: 'playwright-screenshot/wellcome/english.jpg', fullPage: false });

})

// 条件:アドオンリンクを押す
// 期待:アドオンが表示されているか
test('addon link', async ({ page }) => {
    await page.goto(baseHome);
    const page1Promise = page.waitForEvent('popup');
    await page.getByRole('link', { name: 'ダウンロードはこちらから' }).click();
    const addonPage = await page1Promise;
    await expect(addonPage).toHaveURL("https://chrome.google.com/webstore/detail/sundlf-bookmark-addon/mfcobcdpjbgnpbkhbbfaabkkphpceoka");
})

// 条件:ログインした状態
// 期待:ログインボタンが非表示
// 初めての方はこちらボタンが非表示
// ホームに戻るボタンが表示される
// test('register link', async ({ page }) => {
//     await page.goto(baseHome);
// })

