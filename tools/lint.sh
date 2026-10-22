#!/bin/bash
# Lint all project PHP files with PHP 8.5 (syntax errors + compile-time deprecations).
# Usage: tools/lint.sh [php-binary]   (default: php8.5, falls back to php)
cd "$(dirname "$0")/.." || exit 1
PHP=${1:-$(command -v php8.5 || command -v php)}
out=$(find application system swoole tools -name '*.php' -not -path 'application/vendor/*' -print0 \
    | xargs -0 -P8 -I{} sh -c '"$1" -d error_reporting=-1 -d display_errors=1 -l "$2" 2>&1 | grep -v "^No syntax errors" || true' _ "$PHP" {}
  for f in *.php; do "$PHP" -d error_reporting=-1 -d display_errors=1 -l "$f" 2>&1 | grep -v '^No syntax errors'; done)
if [ -n "$out" ]; then echo "$out" | sed 's/^PHP //; s/  */ /g' | sort -u; exit 1; fi
echo "lint OK ($($PHP -v | head -1 | cut -d' ' -f1,2))"
