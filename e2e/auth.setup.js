import { test as setup, expect } from '@playwright/test';

const authFile = 'playwright/.auth/user.json';
const testmail = 'testuser@abc.com'
const testpass = 'testuser'
setup.use({locale: 'ja-JP',})

setup('authenticate', async ({ page }) => {
    await page.goto('http://127.0.0.1:8000/login');
    await page.getByLabel('メールアドレス').fill(testmail);
    await page.getByLabel('パスワード').fill(testpass);
    await page.getByRole('button', { name: 'ログイン' }).click();

    await page.waitForURL("http://127.0.0.1:8000/BookMark/Search");

    await page.context().storageState({ path: authFile });
});
