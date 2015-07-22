all: generate-fr generate-en

watch:
	node_modules/.bin/nodemon --exec "php" -e yml,php,png,jpg,jpeg,gif generate.php

watch-en:
	node_modules/.bin/nodemon --exec "php" -e yml,php,png,jpg,jpeg,gif generate.php en

generate-fr:
	php generate.php fr

generate-en:
	php generate.php en