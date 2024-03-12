#!/bin/bash

script_dir=$(dirname "$(realpath "$0")")
prev_tag=$(git describe --abbrev=0 --tags `git rev-list --tags --skip=1  --max-count=1`)
base_url="https://github.com/${GITHUB_REPOSITORY}"

if [ "$prev_tag" != "none" ]; then
    commits=$(git log --oneline "$prev_tag"..)
else
    commits=$(git log --oneline)
fi

# Generate list of unique commit messages with URLs
commit_list=""
while IFS= read -r commit; do
    commit_hash=$(echo "$commit" | awk '{print $1}')
    commit_msg=$(echo "$commit" | sed 's/^[a-f0-9]\{7\} \(.*\)$/\1/')
    colon_pos=$(expr index "$commit_msg" ":")
    commit_type="${commit_msg:0:$colon_pos}"

    if [[ "$commit_type" == "feat:" || "$commit_type" == "fix:" ]]; then
        commit_msg="${commit_msg:$colon_pos}"
        commit_msg="${commit_msg#"${commit_msg%%[![:space:]]*}"}"
        commit_msg="${commit_msg%"${commit_msg##*[![:space:]]}"}"
        commit_msg=$(echo "$commit_msg" | sed -E 's|PR#([0-9]+)|[PR#\1]('"$base_url"'/pull/\1)|g')

        if ! grep -q "$commit_msg" <<< "$commit_list"; then
            commit_list+="* [$commit_hash]($base_url/commit/$commit_hash) $commit_msg"$'\n'
        fi
    fi
done <<< "$commits"

export version=$(jq -r '.version' $script_dir/../manifest.json)
export name=$(jq -r '.name' $script_dir/../manifest.json)
export changes="$commit_list"

envsubst < "$script_dir/../RELEASE_NOTES.md"
