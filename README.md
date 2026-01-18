# coachtech-furima

### 概要　
COACHTECH 模擬案件：Laravel を用いたフリマアプリの作成プロジェクトです。

出品・購入・コメント・いいね機能などを実装しています。

---

### 🛠️ 環境構築手順

※ 特記がない限り、以下のコマンドはすべて **Dockerの外（PCのターミナル）** で実行してください。

#### 1. リポジトリの設定

このリポジトリを clone してください。

```bash
cd coachtech/laravel
git clone https://github.com/megumi2233/coachtech-furima.git
cd coachtech-furima
```

#### 2. Docker の設定

ローカル環境に必要なサービス（nginx, php, mysql, phpMyAdmin）を Docker で構築・起動します。

事前に Docker Desktop を起動し、クジラ 🐳 アイコンが表示されていることを確認してください。

以下のコマンドで Docker 環境を構築・起動します。

```bash
docker-compose up -d --build
```

コンテナが立ち上がれば成功です。

#### 3. Laravel のパッケージのインストール

Laravel の動作に必要な依存パッケージをインストールします。

```bash
docker-compose exec php composer install
```

#### 4. .env ファイルの作成

Laravel の環境設定を行うために、`.env.example` をコピーして `.env` ファイルを作成します。

```bash
cp .env.example .env
```

.env内の DB 設定を以下のように修正してください。

```ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

#### 5. アプリケーションキーの生成

アプリケーションを起動するためのキーを生成します。

```bash
docker-compose exec php php artisan key:generate
```

#### 6. マイグレーションの実行

データベースにテーブルを作成します。

```bash
docker-compose exec php php artisan migrate
```

#### 7. シーディングの実行

初期データを投入します。

```bash
docker-compose exec php php artisan db:seed
```

#### 8. ディレクトリの準備と権限設定
クローン直後は、Laravelの実行に必要なディレクトリが不足していたり、書き込み権限がなかったりしてエラーが出る場合があります。

以下のコマンドを実行してください。

```bash
docker-compose exec php mkdir -p storage/framework/views storage/framework/cache
docker-compose exec php chown -R www-data:www-data storage
docker-compose exec php chmod -R 775 storage
```

#### 9. ストレージのシンボリックリンク作成

商品画像やプロフィール画像など、ユーザーがアップロードしたファイルを公開するために、シンボリックリンクを作成します。
　(画像を表示させるために必要です)

```bash
docker-compose exec php php artisan storage:link
```

---

### 🧩 View ファイルの作成

#### 共通レイアウト
- `src/resources/views/layouts/auth.blade.php`
  - 会員登録・ログイン・認証用の共通レイアウト
- `src/resources/views/layouts/app.blade.php`
  - メイン用の共通レイアウト（認証画面以外の画面）

#### 会員登録・認証関連
- `src/resources/views/auth/register.blade.php` : 会員登録画面
- `src/resources/views/auth/login.blade.php` : ログイン画面
- `src/resources/views/auth/verify.blade.php` : メール認証画面

#### 商品関連
- `src/resources/views/index.blade.php` : 商品一覧画面
- `src/resources/views/item/show.blade.php` : 商品詳細画面
- `src/resources/views/item/create.blade.php` : 商品出品画面

#### 購入関連
- `src/resources/views/purchase/show.blade.php` : 商品購入画面
- `src/resources/views/purchase/address.blade.php` : 送付先住所変更画面（※作成中）

#### マイページ・プロフィール
- `src/resources/views/mypage/profile.blade.php` : プロフィール画面
- `src/resources/views/mypage/edit.blade.php` : プロフィール編集画面

---

### 🎨 CSS ファイルの作成

#### 共通スタイル
- `src/public/css/sanitize.css` : リセットCSS
- `src/public/css/auth.css` : 会員登録・ログイン・認証用の共通スタイル
- `src/public/css/common.css` : メイン用の共通スタイル

#### 各画面専用スタイル
- **認証関連**
  - `src/public/css/register.css` : 会員登録画面
  - `src/public/css/login.css` : ログイン画面
  - `src/public/css/verify.css` : メール認証画面

- **商品関連**
  - `src/public/css/index.css` : 商品一覧画面
  - `src/public/css/item.css` : 商品詳細画面
  - `src/public/css/sell.css` : 商品出品画面

- **購入関連**
  - `src/public/css/purchase.css` : 商品購入画面
  - `src/public/css/address.css` : 送付先住所変更画面

- **マイページ関連**
  - `src/public/css/profile.css` : プロフィール画面
  - `src/public/css/profile_edit.css` : プロフィール編集画面
    
---

### 🛠 使用技術
- **PHP 8.x**
- **Laravel 8.x**
- **MySQL 8.0**
- **Docker（開発環境構築）**
  - nginx / php / mysql / mailhog / phpmyadmin
- **Stripe API**（決済機能の実装：stripe-php v19.1）
- **Laravel Fortify**（認証機能の実装）
- **テスト**: PHPUnit（機能テスト・バリデーションテスト）
- **フロントエンド**: CSS (独自デザイン / レスポンシブ対応)

---

### 📋 テーブル設計
#### 1. users テーブル (ユーザー情報)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| name | string | | | ○ | |
| email | string | | ○ | ○ | |
| email_verified_at | timestamp | | | | |
| password | string | | | ○ | |
| remember_token | string | | | | |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 2. profiles テーブル (プロフィール詳細)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| user_id | unsigned bigint | | | ○ | users(id) |
| zipcode | string | | | ○ | |
| address | string | | | ○ | |
| building_name | string | | | | |
| avatar_url | text | | | | |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 3. items テーブル (商品情報)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| user_id | unsigned bigint | | | ○ | users(id) |
| name | string | | | ○ | |
| price | integer | | | ○ | |
| description | text | | | ○ | |
| img_url | text | | | ○ | |
| brand_name | string | | | | |
| condition_id | unsigned bigint | | | ○ | conditions(id) |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 4. categories テーブル (カテゴリー)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| content | string | | | ○ | |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 5. conditions テーブル (商品状態)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| content | string | | | ○ | |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 6. purchases テーブル (購入履歴)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| user_id | unsigned bigint | | | ○ | users(id) |
| item_id | unsigned bigint | | | ○ | items(id) |
| payment_method | string | | | ○ | |
| shipping_postal_code | string | | | ○ | |
| shipping_address | string | | | ○ | |
| shipping_building_name | string | | | | |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 7. likes テーブル (いいね)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| user_id | unsigned bigint | | | ○ | users(id) |
| item_id | unsigned bigint | | | ○ | items(id) |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

#### 8. comments テーブル (コメント)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| item_id | unsigned bigint | | | ○ | items(id) |
| content | string | | | ○ | |
| created_at | timestamp | | | | |
| user_id | unsigned bigint | | | ○ | users(id) |
| updated_at | timestamp | | | | |

#### 9. category_item テーブル (中間テーブル)

| カラム名 | 型 | PK | UNIQUE | NOT NULL | FK (外部キー) |
|---|---|:---:|:---:|:---:|---|
| id | unsigned bigint | ○ | | ○ | |
| item_id | unsigned bigint | | | ○ | items(id) |
| category_id | unsigned bigint | | | ○ | categories(id) |
| created_at | timestamp | | | | |
| updated_at | timestamp | | | | |

---

### 🗂 ER図（このプロジェクトのデータ構造）

このアプリケーションのデータ構造を視覚的に把握するため、以下にER図を掲載しています。

この図では、`items`（商品）テーブルと `users`（ユーザー）テーブルを中心に構成されています。
ユーザーが商品を出品し、ユーザーが商品を購入するという関係性から、`users` と `items`、および `users` と `purchases` はそれぞれ「1対多」のリレーションで接続されています。
また、1つの商品に複数のカテゴリーを設定できるよう、`items` と `categories` は中間テーブル（`category_item`）を介した「多対多」の関係となっています。

![ER図](assets/coachtech-furima-er.png)

※ 補足：
1. 図は draw.io（diagrams.net）にて作成し、PNG形式で保存しています。
2. 元データは `coachtech-furima-er.drawio` にて編集可能です。
3. PNGファイルは `assets/coachtech-furima-er.png` に保存されています。

   → READMEではこの画像を参照しています。
4. 編集には [draw.io（diagrams.net）](https://app.diagrams.net/) を使用してください。

　 　ローカルアプリまたはブラウザ版のどちらでも編集可能です。
  
5. ER図の更新手順：drawioで編集 → PNG再出力 → assetsに上書き保存 → README確認

   ※GitHub上で画像が更新されない場合は、キャッシュをクリアしてください。

---

### 📦 ダミーデータについて
商品ダミーデータを作成するために、出品者となるユーザーデータ（テストユーザー）も同時に作成するように実装しました。

#### テストユーザー情報（動作確認用）

```ini
email: test@example.com
password: password123
```
※シーディング実行後、上記のアカウントでログインすることで、ダミー商品データの表示やマイページ機能などの動作確認がすぐに可能です。

---

### ✅ テストの実装と実行方法
アプリケーションの品質を担保するため、主要機能に対してフィーチャーテストを実装しています。

#### テストの網羅範囲
- **基本機能**: ユーザー登録、ログイン、商品出品、お気に入り登録、コメント投稿などの「正常動作」を確認済みです。
- **バリデーション**: 各フォーム（登録、出品、コメント等）における未入力チェックや文字数制限などのバリデーションロジックをテストコードで検証済みです。

#### 実行コマンド
- コマンド：`php artisan test`
※ 実装したすべてのテストケースにおいて、正常にパスすることを確認済みです。

---

### 🛠️ 特筆すべき実装ポイント

#### 1. 販売状況に応じた購入制限（安全性への配慮）
売却済みの商品（SOLD）が二重に購入されることを防ぐため、以下の制御を実装しています。
- **UI制御**: 詳細画面で「SOLD」ラベルを表示し、購入ボタンを「売り切れました」に変更（無効化）。
- **サーバーサイド制御**: URLの直接入力等による不正な遷移も、コントローラー側で検出しトップページへリダイレクト。

#### 2. 支払い方法の選択による表示切り替え（UXへの配慮）
機能要件である「小計画面で変更が反映される」動作をスムーズに実現するため、JavaScript を使用しています。
- **採用理由**: PHPのみの処理では画面のリロードが必要となりますが、JavaScript を採用することで、ユーザーが支払い方法を選択した瞬間に即時反映される快適な操作性を実現しました。

---

### 🎨 デザイン・レイアウトに関する注記

**プルダウン（セレクトボックス）の表示について**
以下の画面におけるプルダウン選択時の表示について、設計書（Mac環境）では選択肢に「チェックマーク（✓）」が表示されていますが、本環境（Windows）および標準的なブラウザの仕様に準拠し、OS標準のスタイル（背景色の変化のみ）を採用しています。

* **商品出品画面：** 「商品の状態」の選択
* **商品購入画面：** 「支払い方法」の選択

---

### 🧪 動作確認・テストに関する注記

**決済機能の動作確認について（Stripeテスト環境）**
本アプリケーションの購入処理（商品の「Sold」ステータスへの更新）は、Stripe決済完了後にアプリケーション画面へリダイレクト（自動遷移）したタイミングで実行される仕様となっています。

* **推奨されるテスト方法:**
  **「カード支払い」** にて動作確認をお願いいたします。
  （決済完了後、自動的に商品一覧画面へ遷移し、即座に「Sold」表示が反映されることを確認できます。）

* **コンビニ支払い等の注意点:**
  Stripeテスト環境の仕様上、コンビニ支払いを選択すると「支払い受付画面（バウチャー）」で遷移が停止し、アプリケーションへ自動的にリダイレクトされない場合があります。その場合、データベースの更新処理が実行されず、画面上で「Sold」にならない可能性がありますが、これはテスト環境およびリダイレクト型実装の仕様によるものです。

※無理なスタイル変更によるスマホ等でのレイアウト崩れを防ぐため、HTML標準の `<select>` タグの挙動を優先しています。

**メール認証機能の動作確認について（Mailhog）**
本アプリケーションでは、開発環境でのメール送信テストに **Mailhog**（仮想SMTPサーバー）を使用しています。会員登録時や再送リクエスト時に送信される認証メールは、実際のアドレスには届かず、ローカル環境内の仮想メールボックスに捕捉されます。

* **確認手順:**
  ブラウザで以下のURL（Mailhog管理画面）にアクセスし、受信トレイからメールを確認・開封してください。
  **http://localhost:8025**

* **環境設定の注意点:**
  `.env.example` に記載されているMailhog設定（ポート: 1025など）が、`.env` ファイルに正しく反映されている必要があります。

---

### 💡 ユーザビリティ向上に関する注記

**プロフィール更新後の画面遷移について**
プロフィール編集画面にて変更を保存した後の遷移先について、仕様書では指定がありませんでしたが、ユーザー体験（UX）を考慮し、トップページではなく「マイページ」へリダイレクトするように実装しました。

* **実装理由:** ユーザーが変更した内容（新しいアイコンや名前）が正しく反映されているかを即座に視覚的に確認できるようにするためです。トップページに戻ってしまうと、確認のために再度マイページへ移動する手間が発生するため、この動線を最適化しました。

---

## 技術選定の理由

* **商品画像のプレビュー表示**
    * PHPはサーバーサイド言語のため、アップロードボタンを押すまで画像を表示することができません。
    * ユーザーが画像を選択した瞬間に確認できるようにするため、この機能に限りJavaScript（クライアントサイド）を使用しました。

以上が本アプリケーションの仕様です。提出物は以上となりますので、ご確認のほどよろしくお願いいたします。


