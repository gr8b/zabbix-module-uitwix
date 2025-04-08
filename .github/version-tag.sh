#!/bin/bash

script_dir=$(dirname "$(realpath "$0")")
prev_tag=$(git describe --abbrev=0 --tags `git rev-list --tags --max-count=1` 2>/dev/null || echo "")
minor=0
patch=0


if [ "$prev_tag" != "" ]; then
    commits=$(git log --oneline "$prev_tag"..HEAD)
else
    prev_tag="1.0"
    commits=$(git log --oneline)
fi

while IFS= read -r commit; do
    commit_msg=$(echo "$commit" | sed 's/^[a-f0-9]\{7\} \(.*\)$/\1/')
    colon_pos=$(expr index "$commit_msg" ":")
    commit_type="${commit_msg:0:$colon_pos}"

    if [[ "$commit_type" == "feat:" ]]; then
        minor=1
        patch=0
        break
    fi

    if [[ "$commit_type" == "fix:" ]]; then
        patch=1
    fi
done <<< "$commits"

IFS='.' read -ra minor_patch <<< "$prev_tag"

if ((minor > 0)); then
    ((minor = "${minor_patch[0]}" + minor))
    patch=0
else
    ((patch = "${minor_patch[1]}" + patch))
fi

echo "${minor}.${patch}"
