# url-shortener

URL短縮マイクロサービス（Laravel 12）

## 機能

- URL短縮（ランダム6文字のショートコード生成）
- カスタムショートコード指定
- 有効期限の設定
- リダイレクト（クリックカウント付き）
- 統計情報の取得（クリック数、有効期限など）

## セットアップ

### ローカル開発（SQLite）

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### Docker

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate
```

`.env` の DB 設定を PostgreSQL に切り替えてください:

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=shortener_db
DB_USERNAME=shortener_user
DB_PASSWORD=shortener_pass
```

サービスは `http://localhost:8000` でアクセスできます。

## API

### URL を短縮する

```
POST /api/urls
Content-Type: application/json

{
  "url": "https://example.com/very/long/path",
  "custom_code": "mycode",    // 任意（3〜10文字、英数字・ハイフン・アンダースコア）
  "expires_at": "2025-12-31T23:59:59+09:00"  // 任意
}
```

レスポンス（201）:

```json
{
  "short_url": "http://localhost:8000/mycode",
  "short_code": "mycode",
  "original_url": "https://example.com/very/long/path",
  "expires_at": "2025-12-31T14:59:59+00:00"
}
```

### 短縮URLでリダイレクト

```
GET /{short_code}
```

元のURLへ302リダイレクトします。アクセスごとにクリック数がカウントされます。
期限切れの場合は404を返します。

### 統計情報を取得する

```
GET /api/urls/{short_code}
```

レスポンス（200）:

```json
{
  "short_code": "mycode",
  "original_url": "https://example.com/very/long/path",
  "short_url": "http://localhost:8000/mycode",
  "clicks": 42,
  "is_expired": false,
  "expires_at": "2025-12-31T14:59:59+00:00",
  "created_at": "2025-03-22T10:00:00+00:00"
}
```

## テスト

```bash
php artisan test
```

## ディレクトリ構成

```
url-shortener/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── UrlController.php    # URL短縮・リダイレクト・統計API
│   │   └── Requests/
│   │       └── StoreUrlRequest.php  # バリデーション
│   ├── Models/
│   │   └── Url.php                  # URLモデル
│   └── Services/
│       └── UrlShortenerService.php  # ビジネスロジック
├── database/
│   └── migrations/                  # DBマイグレーション
├── routes/
│   ├── web.php                      # リダイレクトルート
│   └── api.php                      # APIルート
├── tests/
│   └── Feature/
│       └── UrlShortenerTest.php     # フィーチャーテスト
├── docker-compose.yml
├── Dockerfile
└── default.conf                     # Nginx設定
```
