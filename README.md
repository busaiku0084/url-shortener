# url-shortener
URL短縮サービス

## ディレクトリ構成
```sh
url-shortener/
│── app/                     # Laravel アプリケーションのメインディレクトリ
│   ├── Models/              # Eloquent モデル
│   ├── Http/                # コントローラー、リクエストなど
│   ├── Services/            # サービスクラス（ビジネスロジック）
│── bootstrap/               # Laravel のブートストラップ関連
│── config/                  # 設定ファイル
│── database/                # マイグレーションとシード
│   ├── migrations/          # データベースマイグレーション
│── public/                  # 公開ディレクトリ（`index.php` がここ）
│── resources/               # Blade テンプレート、CSS、JS
│── routes/                  # ルーティング定義
│   ├── web.php              # ウェブルート
│   ├── api.php              # API ルート
│── storage/                 # キャッシュ・ログ・アップロードデータ
│── tests/                   # テスト
│── vendor/                  # Composer 依存ファイル
│── .env                     # Laravel の環境設定
│── .gitignore               # Git 無視ファイル
│── artisan                  # Laravel CLI
│── composer.json            # Composer のパッケージ設定
│── docker-compose.yml       # Docker Compose 設定
│── Dockerfile               # Docker ビルド設定
│── default.conf             # Nginx の設定（ルートディレクトリに配置）
│── phpunit.xml              # PHPUnit 設定
```
