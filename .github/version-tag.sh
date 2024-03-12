#!/bin/bash

script_dir=$(dirname "$(realpath "$0")")
prev_tag=$(git describe --abbrev=0 --tags `git rev-list --tags --skip=1  --max-count=1`)
minor=0
patch=0

if [ "$prev_tag" != "none" ]; then
    commits=$(git log --oneline "$prev_tag"..)
else
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
        ((patch++))
    fi
done <<< "$commits"

version=$(jq -r '.version' $script_dir/../manifest.json)
IFS='.' read -ra minor_patch <<< "$version"

((minor = "${minor_patch[0]}" + minor))
((patch = "${minor_patch[1]}" + patch))
echo "${minor}.${patch}"
