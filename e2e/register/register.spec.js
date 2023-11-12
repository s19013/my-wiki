import { test, expect } from '@playwright/test';
const baseHome = 'http://127.0.0.1:8000/'
const testuser = "testuser"
const testmail = 'testuser@abc.com'
const testpass = 'testuser'

test.use({locale: 'ja-JP',})
// 期待:表示されるべきものが全部表示されているか
test('default', async ({ page }) => {
    await page.goto(baseHome + "register");

    await expect(page.getByText('ユーザーネーム')).toBeVisible();
    await expect(page.getByTestId('nameInput')).toBeVisible();
    await expect(page.getByTestId('nameInput')).toHaveValue('');
    await expect(page.getByText('メールアドレス')).toBeVisible();
    await expect(page.getByTestId('emailInput')).toBeVisible();
    await expect(page.getByTestId('emailInput')).toHaveValue('');
    await expect(page.getByText('パスワード',{exact:true})).toBeVisible();
    await expect(page.getByTestId('passwordInput')).toBeVisible();
    await expect(page.getByTestId('passwordInput')).toHaveValue('');
    await expect(page.getByText('確認のため パスワードをもう一度入力してください')).toBeVisible();
    await expect(page.getByTestId('passwordConfirmationInput')).toBeVisible();
    await expect(page.getByTestId('passwordConfirmationInput')).toHaveValue('');

    await expect(page.getByText('利用規約に同意します')).toBeVisible();
    expect(await page.getByTestId('termsCheckbox').isChecked()).toBe(false);
    await expect(page.getByText('プライバシーポリシーに同意します')).toBeVisible();
    expect(await page.getByTestId('privacyCheckbox').isChecked()).toBe(false);
    await expect(page.getByRole('link', { name: 'ログインはこちら' })).toBeVisible();
    await expect(page.getByRole('button', { name: '登録' })).toBeVisible();
})

// 条件:[ログインはこちら]リンクをクリック
// 期待:ログイン画面に飛ぶ
test("ログイン画面が表示されるか",async({page}) => {
    await page.goto(baseHome + "register");
    await page.getByRole('link', { name: 'ログインはこちら' }).click();
    await expect(page).toHaveURL(baseHome + "login");
})

test.describe("登録失敗",() => {
    // 条件:何も埋めてない
    // 期待:｢このフィールドを入力してください｣と表示される(スクショを目視で確認)
    test('登録失敗:何も埋めてない',async({page}) => {
        await page.goto(baseHome + "register");
        await page.getByRole('button', { name: '登録' }).click();
        await page.screenshot({ path: 'playwright-screenshot/register/登録失敗-何も埋めてない-jp.jpg', fullPage: false });
    })

    // 条件:メアドの欄に適当な文字列を入れる
    // 期待:｢[@]を挿入してください｣と表示される(スクショを目視で確認)
    test('登録失敗:メアド欄にメアド以外の文字列を入力',async({page}) => {
        await page.goto(baseHome + "register");
        await page.getByLabel('メールアドレス').fill('aaa');
        await page.getByRole('button', { name: '登録' }).click();
        await page.screenshot({ path: 'playwright-screenshot/register/登録失敗-メアド欄にメアド以外の文字列を入力-jp.jpg', fullPage: false });
    })


    // 条件:一部フォームだけ埋めた
    // 期待:｢このフィールドを入力してください｣と表示される(スクショを目視で確認)
    const testPatterns = [
        {
            testPattern:"ユーザーネーム",
            testId:"nameInput",
            value:testuser
        },
        {
            testPattern:"メールアドレス",
            testId:"emailInput",
            value:testmail
        },
        {
            testPattern:"パスワード",
            testId:"passwordInput",
            value:testpass
        },
        {
            testPattern:"パスワード再入力",
            testId:"passwordConfirmationInput",
            value:testpass
        },
    ]
    for (const field of testPatterns){
        test(`${field.testPattern}だけ埋めた`,async({page}) => {
            await page.goto(baseHome + "register");
            await page.getByTestId(field.testId).fill(field.value);
            await page.getByRole('button', { name: '登録' }).click();
            await page.screenshot({ path: `playwright-screenshot/register/登録失敗-${field.testPattern}だけ埋めた-jp.jpg`, fullPage: false });
        })
    }

    // 条件:チェックだけつけてない
    // 期待:｢このチェックボックスをオンにしてください｣と表示される(スクショを目視で確認)
    test("チェックだけつけてない",async({page}) => {
        await page.goto(baseHome + "register");
        await page.getByTestId('nameInput').fill(testuser);
        await page.getByTestId('emailInput').fill(testmail);
        await page.getByTestId('passwordInput').fill(testpass);
        await page.getByTestId('passwordConfirmationInput').fill(testpass);
        await page.getByRole('button', { name: '登録' }).click();
        await page.screenshot({ path: `playwright-screenshot/register/登録失敗-チェックだけつけてない-jp.jpg`, fullPage: false });
    })

    // 条件:チェック片方だけつけた
    // 期待:｢このチェックボックスをオンにしてください｣と表示される(スクショを目視で確認)
    const checkboxes = [
        {
            testPattern:"利用規約",
            testId:"termsCheckbox",
        },
        {
            testPattern:"プライバシーポリシー",
            testId:"privacyCheckbox",
        }
    ]
    for (const checkbox of checkboxes){
        test(`${checkbox.testPattern}だけチェックつけた`,async({page}) => {
            await page.goto(baseHome + "register");
            await page.getByTestId('nameInput').fill(testuser);
            await page.getByTestId('emailInput').fill(testmail);
            await page.getByTestId('passwordInput').fill(testpass);
            await page.getByTestId('passwordConfirmationInput').fill(testpass);
            await page.getByRole('button', { name: '登録' }).click();
            await page.getByTestId(checkbox.testId).check();
            await page.screenshot({ path: `playwright-screenshot/register/登録失敗-${checkbox.testPattern}だけチェックつけてない-jp.jpg`, fullPage: false });
        })
    }
})

// 条件:登録成功
// 期待:ブックマーク検索画面に遷移
test('登録成功',async({page}) => {
    await page.goto(baseHome + "register");
    await page.getByTestId('nameInput').fill(testuser);
    await page.getByTestId('emailInput').fill(testmail);
    await page.getByTestId('passwordInput').fill(testpass);
    await page.getByTestId('passwordConfirmationInput').fill(testpass);
    await page.getByTestId("termsCheckbox").check();
    await page.getByTestId("privacyCheckbox").check();
    await page.getByRole('button', { name: '登録' }).click();
    await expect(page).toHaveURL(baseHome + "BookMark/Search");
})
