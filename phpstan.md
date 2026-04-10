composer require larastan/larastan:^2.0 --dev

run analis:
./vendor/bin/phpstan analyse
# Если хотите видеть прогресс и более подробный вывод, добавьте флаги:
./vendor/bin/phpstan analyse -v --memory-limit=1G
