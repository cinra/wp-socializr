# WP-Socializr

## 概要

`wp_cron`で、Wordpressの各記事に対するメンションを取得して、コメントとして登録するプラグイン。

## クラス

### Crawler

各SNSをクロールする際にStaticに使用するクラス。

#### Crawler::count( string $provider, int $id )

記事IDに対する各SNSの言及数を取得して、meta_keyを「{$provider}_count」とするpostmetaとして登録する。

arg|type|description
---|---|---
$provider|string|SNSのスラッグ（例：facebook）
$id|int|記事ID（$post->ID）

#### Crawler::get_comment( string $provider, int $id )

記事IDに対する各SNSの言及を取得して、記事に対するコメントとして登録する。

arg|type|description
---|---|---
$provider|string|SNSのスラッグ（例：facebook）
$id|int|記事ID（$post->ID）

### Socialzr

クロールの際の初期設定を行うクラス

## ヘルパー

### wp_socializr_crawl()

実際のクロール作業を行う。実行すると、記事を取得してくる。それぞれの記事に対して、各SNSを走査して結果を取得してくるため、あまり大量の記事を処理に回すことは出来ないです。

### get_social_count( string $provider, int $id )

各記事に付けられたコメント数を取得する。

arg|type|description
---|---|---
$provider|string|SNSのスラッグ（例：facebook）
$id|int|記事ID（$post->ID）

### the_social_count( string $provider, int $id )

`get_social_count()`をechoする

## プロバイダー

- `/provider`に各SNS用の処理が書かれたモジュールが格納されています。（例：`/provider/facebook`）
- 使用する各種ライブラリもここに収めている

filename|description
---|---
admin.php|管理画面を出力する
crawler.php|クロールの際に実際にAPIを叩くスクリプト

### crawler.php

`Crawler`の子クラス

### admin.php

`{$provider}_use_counter`（メンション数を取得するか）、`{$provider}_use_comment`（コメントを取得するか）、`{$provider}_comment_approval`（コメントを即時反映するか）を設定できる管理画面を出力する

## 課題点

- wp_cronが上手く動作していないことが多い。wp-socializr.phpの135〜198行目辺りで、wp_cronの設定を行っています
- URLを一件ずつ取得するのが煩わしい。`home_url()`で一気にクロールできればいいが、Twitterも仕様で、searchのAPIが上手く動いているかわからないのと、Facebookはそもそもパーマリンクじゃないとコメントを受け付けない。