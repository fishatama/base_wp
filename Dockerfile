FROM wordpress:latest

# 必要なツール郡をインストール
RUN apt-get update
RUN apt-get -y install wget unzip

# パーミッション設定用のスクリプトをコピー
# COPY set_permissions.sh /usr/local/bin/set_permissions.sh
# RUN chmod +x /usr/local/bin/set_permissions.sh

# コンテナ起動時にパーミッションを設定するエントリーポイントスクリプト
# ENTRYPOINT ["bash", "-c", "/usr/local/bin/set_permissions.sh && docker-entrypoint.sh apache2-foreground"]

# 不要になった一時ファイルを削除
RUN apt-get clean
RUN rm -rf /tmp/*
# デフォルトテーマなんていらないよね！
RUN rm -rf /usr/src/wordpress/wp-content/themes/twenty**

# サーバが読めるように以下の所有者を変更
RUN chmod -R 777 /usr/src/wordpress
RUN chmod -R 777 /var/www/html

WORKDIR /var/www/html