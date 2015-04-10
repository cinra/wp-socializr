# WP-Socializr

## Synopsis

- `wp_cron`で、Wordpressの各記事に対するメンションを取得して、コメントとして登録するプラグイン

## Class

### Crawler

- 各SNSをクロールするオブジェクトを生成するクラス。

#### Crawler::count( string $provider, int $id )

arg|type|description
===|===|===
$provider|string|SNSのスラッグ（例：facebook）
$id|int|記事ID（$post->ID）

