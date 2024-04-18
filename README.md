# make-ec ローカル環境

## 環境構築方法
ビルド 

```
docker-compose build
```

起動

```
docker-compose up -d
```

Ec-cube初期設定
```
docker-compose exec app bash
cd /var/www/

# 指定フォルダーにgit clone
git clone https://github.com/Yosemite-Inc/make-ec.git ./html

# composer 設定
cd /var/www/html
composer install
composer require symfony/apache-pack
composer require symfony/orm-pack

# npm 設定
npm install

# .envファイルを作成
# 注意　環境変数などは、やり方によりますが、.envに記載ではなく、別のdocker-compose.ymlに記載することもあります。

# eccube install
php bin/console e:i --no-interaction

# db server性能低い時、以下のようなtimeoutエラーになる可能性あり
# The process "'bin/console' 'eccube:fixtures:load'" exceeded the timeout of 60 seconds.
# もしtimeout errorの場合、初期化処理を分けて、以下コマンドを実行
php bin/console doctrine:schema:drop --force --no-interaction
php bin/console doctrine:schema:create --no-interaction
php bin/console eccube:fixtures:load --no-interaction

# schema-update:
php bin/console cache:clear --no-warmup
php bin/console eccube:generate:proxies
php bin/console doctrine:schema:update --dump-sql
php bin/console doctrine:schema:update --force
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --no-warmup

# init-setup
php bin/console customize:initialize:setup

# plugin-install
# ProductReview42
php bin/console eccube:plugin:install --code="ProductReview42"
php bin/console eccube:plugin:enable --code="ProductReview42"

# GmoPaymentGateway42
php bin/console eccube:plugin:install --code="GmoPaymentGateway42"
php bin/console eccube:plugin:enable --code="GmoPaymentGateway42"

# GmoPsKb4
php bin/console eccube:plugin:install --code="GmoPsKb4"
php bin/console eccube:plugin:enable --code="GmoPsKb4"

# Coupon42
php bin/console eccube:plugin:install --code="Coupon42"
php bin/console eccube:plugin:enable --code="Coupon42"

# MailMagazine42
php bin/console eccube:plugin:install --code="MailMagazine42"
php bin/console eccube:plugin:enable --code="MailMagazine42"

# ProductOption42
php bin/console eccube:plugin:install --code="ProductOption42"
php bin/console eccube:plugin:enable --code="ProductOption42"

# 独自のリソース適用する
cp /anywhere/gcs-auth.json ./
```

## Ec-cube動作確認
以下リンクをたたいて、画面表示できればOKです。

上記手順通りに実施すれば、画面崩れなしで表示されるはず<br>
もし画面崩れあれば、init-setupとplugin-installを順番に実施し直すことを試す
```
http://localhost:8080/
```

実行したSQLの取得方法
```shell
# my.cnfの権限変更
chmod 644 /etc/mysql/mariadb.conf.d/my.cnf
chown root:root /etc/mysql/mariadb.conf.d/my.cnf

# mysql server
tail -f /var/log/mysql/mysqld.log
```

## Gloud Profilerの利用 (方法１)
1. google/cloud-toolsパッケージをインストール
```
composer require google/cloud-tools
```

2. GoogleCloudToolsBundleを登録
app/config/bundles.phpファイルに以下の行を追加して、GoogleCloudToolsBundleを登録します。
```php
return [
    // ...
    Google\Cloud\Tools\Bundle\GoogleCloudToolsBundle::class => ['all' => true],
];
```

3. google_cloud.yamlを作成
app/config/packages/ディレクトリにgoogle_cloud.yamlファイルを作成し、以下の内容を記述します。

```yaml
google_cloud:
    project_id: YOUR_GCP_PROJECT_ID
    credentials:
        file: '%kernel.project_dir%/path/to/credentials.json'
        # 又は以下のようにenv経由で設定
        # env_var: GOOGLE_APPLICATION_CREDENTIALS
```

4. AppKernelを拡張
app/Kernel.phpファイルを編集し、registerProfilingBundleメソッドをオーバーライドします。

```php
use Google\Cloud\Tools\Profiler\ProfilingExtension;

class Kernel extends BaseKernel
{
    // ...

    protected function registerProfilingBundle(Bundle $bundle)
    {
        $extension = new ProfilingExtension();
        if ($extension->isProfilingEnabled()) {
            $bundle->addCompilerPass($extension->getCompilerPass());
        }
    }
}
```

5. プロファイラの有効化
Google Cloud ConsoleでAp p Engine > サービス > 設定 > Profilerを開き、プロファイラを有効にします。
これでEC-Cube 4アプリケーション上でGoo gle Cloud Profiler for PHPが有効になります。
プロファイリングデータは、リクエストの度に収集され、Google Cloud Consoleの「Profiler」ページで確認できます。
注意点として、プロファイラを有効にするとアプリケーションのパフォーマンスにオーバーヘッドがかかるため、本番環境では長期間の利用を避けることをおすすめします。

## Gloud Profilerの利用 (方法２)

1. Google Cloud PHPツール
```shell
composer require google/cloud-profiler
```

2. Profilerクラスを直接使って初期化する
```php
use Google\Cloud\Profiler\Profiler;

$profiler = new Profiler();
$profiler->start();
// アプリケーションのロジック
$profiler->stop();
```
























