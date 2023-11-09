# Wellcom
## 初期状態
### 表示されるべきものが全部表示されているか｡
* ロゴ
* メッセージ
* 各種リンク
* 各種ボタン

## 画面遷移系

### ログイン画面が表示されるか(ボタンver)
#### expect
url確認(https://sundlf.com/login)  

### 新規登録画面が表示されるか(ボタンver)
#### expect
url確認(https://sundlf.com/register)

### ログイン画面が表示されるか(リンクver)
#### expect
url確認(https://sundlf.com/login)  

### 新規登録画面が表示されるか(リンクver)
#### expect
url確認(https://sundlf.com/register)

### 英語版が表示されているか
#### expect
url確認(https://sundlf.com/en)  
画面確認  
-> さっと見た目を確認する程度で十分  
初期状態と似たような感じで良いでしょ｡

### アドオンに飛ぶか
別タブで開いているか  
url確認(https://chrome.google.com/webstore/detail/sundlf-bookmark-addon/mfcobcdpjbgnpbkhbbfaabkkphpceoka)


### 利用規約にとぶか
#### expect
別タブで開いているか  
url確認(https://docs.google.com/document/d/e/2PACX-1vQRfqPmWcI2irs1HpRBOjA9lyo2CiIFWRBpWY2lmHnMM8gWZUEmng57BEs1t-VC5Bd_kSCHhmG9gmAA/pub)

### プライバシーポリシーに飛ぶか
#### expect
別タブで開いているか  
url確認(https://docs.google.com/document/d/e/2PACX-1vQCW5pRoXeXHiZJ-vz8MImLVm-XTViLIdy1TxTBtsbAAzYb4MpPEaEFucHaWnpzDkI905s5AeW6rui3/pub)

### 問い合わせに飛ぶか
#### expect
別タブで開いているか  
url確認(https://docs.google.com/forms/d/e/1FAIpQLSem2D3fV18IWkSeT5BekQOYvW951xetTNRB_mtlPArAn4R1gw/viewform)

### ログインしたらリンクとボタンが変わるか
ログインした状態ならログインボタンと新規登録ボタンが消える  
ホームに戻るボタンが表示される
#### expect
ログインボタンと新規登録ボタンが消える  
ホームに戻るボタンが表示される  
