#!/bin/bash
# shellcheck disable=SC2044
for img in $(find public/uploads/news -type f -name "*.jpg"); do
    # Remove "public/" from the beginning of the path
    web_path=${img#public/}
    php bin/console liip:imagine:cache:resolve "$web_path" --filter=large
done
