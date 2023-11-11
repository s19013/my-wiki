import { test, expect } from '@playwright/test';
const baseHome = 'http://127.0.0.1:8000/'

test.use({locale: 'ja-JP',})
// 期待:表示されるべきものが全部表示されているか
test('default', async ({ page }) => {
    await page.goto(baseHome + "register");
    await expect(page.locator('label').filter({ hasText: 'ユーザーネーム' })).toBeVisible();
    await expect(page.getByLabel('ユーザーネーム')).toBeVisible();
    await expect(page.getByText('メールアドレス')).toBeVisible();
    await expect(page.getByLabel('メールアドレス')).toBeVisible();
    await expect(page.getByText('パスワード', { exact: true })).toBeVisible();
    await expect(page.getByLabel('パスワード', { exact: true })).toBeVisible();
    await expect(page.getByText('確認のため パスワードをもう一度入力してください')).toBeVisible();
    await expect(page.getByLabel('確認のため パスワードをもう一度入力してください')).toBeVisible();
    await expect(page.getByLabel('利用規約に同意します')).toBeVisible();
    await expect(page.getByText('利用規約に同意します')).toBeVisible();
    await expect(page.getByLabel('プライバシーポリシーに同意します')).toBeVisible();
    await expect(page.getByText('プライバシーポリシーに同意します')).toBeVisible();
    await expect(page.getByRole('link', { name: 'ログインはこちら' })).toBeVisible();
    await expect(page.getByRole('button', { name: '登録' })).toBeVisible();
})
