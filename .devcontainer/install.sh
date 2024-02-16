#!/bin/bash

# Exit the script if any command fails
set -e

if ! type -P gum &>/dev/null; then
    echo "Please install gum utility from https://github.com/charmbracelet/gum"
    exit 1
fi

export GUM_CHOOSE_CURSOR=" "
export GUM_CHOOSE_CURSOR_FOREGROUND="#f00"
export GUM_INPUT_CURSOR_FOREGROUND="#f00"
export GUM_CONFIRM_SELECTED_FOREGROUND="#f00"
export GUM_CONFIRM_TIMEOUT="5s"
export GUM_SPIN_SPINNER="minidot"
export GUM_SPIN_SPINNER_FOREGROUND="#0f0"

work_dir=$(pwd)
zabbix_dir="/var/www/html"
script_dir=$(dirname "$(realpath "$0")")

source "$script_dir/install.src.sh"


if [ "$#" -ne 0 ]; then
    case "$1" in
        "checkout_branch")
            checkout_branch "$2" "$3"
            ;;
        "build_server")
            build_server "$2"
            ;;
        "create_web_files")
            create_web_files "$2" "$3"
            ;;
        "create_conf_files")
            create_conf_files "$2" "$3"
            ;;
        "create_database")
            create_database "$2" "$3" "-h 127.0.0.1 -uroot -pmariadb"
            ;;
        "help")
            gum pager < "$script_dir/README.md"
            ;;
    esac

    exit 0
fi


if [ -f "$work_dir/manifest.json" ]; then
    manifest_version=$(jq -r '.manifest_version' "$work_dir/manifest.json")

    if [ "${manifest_version:0:1}" = "2" ]; then
        branch=$(select_branch "6.4" "")
    else
        branch=$(select_branch "6.0" "6.2")
    fi
else
    while [[ -z "$branch" ]]; do
        branch=$(select_branch "6.0" "")
    done

    if awk -v var="$branch" 'BEGIN { if (var >= 6.4 || var == "master") exit 0; else exit 1 }'; then
        generate_boilerplate "$work_dir" "2"
    else
        generate_boilerplate "$work_dir" "1"
    fi

    success "󰓠 Module boilerplate files created"
fi


gum spin --title "Cloning $branch branch" -- $0 checkout_branch "$zabbix_dir" "$branch"
success "󱓏 Cloned $branch branch to $zabbix_dir"

gum spin --title "Creating web server files" -- $0 create_web_files "$zabbix_dir" "$branch"
success "󱥾 Created web server files in $zabbix_dir"

with_server=1 && gum confirm "Compile and build Zabbix server?"

if [ "$with_server" -eq 1 ]; then
    gum spin --title "Build server and database schema" -- $0 build_server "$zabbix_dir"
    success "󰪩 Server build and database schema done"
fi

gum spin --title "Creating configuration files" -- $0 create_conf_files "$zabbix_dir" "$branch"
success "󱥾 Configuration files created"

gum spin --title "Creating database $branch" -- $0 create_database "$zabbix_dir" "$branch"
success "󰪩 Database $branch created"

if [ "$with_server" -eq 1 ]; then
    gum spin --title "Starting Zabbix server" -- "$zabbix_dir/sbin/zabbix_server" -c "$zabbix_dir/sbin/zabbix_server.conf"
fi

success " All done, happy coding!"
