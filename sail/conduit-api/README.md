# install

```bash
cd conduit-api
composer require laravel/sail --dev
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
touch .env.testing
```


```bash
sail up -d
```

## コンテナが立ち上がっていることを確認する

```bash
docker ps
```

## 確認できたら
```bash
sail artisan migrate:fresh
```

## ポストマンテスト

```bash
APIURL=http://localhost/api ./postman_test/run-api-test.sh
```

## Create Articleのテスト


`.env.testing`ファイルを作成して以下を記入


```bash
touch .env.testing
```

```bash
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
JWT_SECRET={{php artisan jwt:secretで生成した文字列}}
```

```bash
XDEBUG_MODE=coverage sail artisan test --coverage
```
