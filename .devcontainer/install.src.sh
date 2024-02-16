# Helper functions


# Clone zabbix banch into specific directory.
#
# Arguments:
#   $1:  absolute path to directory where to clone zabbix branch
#   $2:  zabbix branch to clone, for example: release/6.4
#
clone_zabbix() {
    local directory="$1"
    local branch="$2"

    git clone --depth=1 --branch "$branch" https://git.zabbix.com/scm/zbx/zabbix.git "$directory"
    # To pull history run: git fetch --unshallow
}

# Build server, make database schema
#
# Arguments:
#   $1:  server sources directory
#
build_server() {
    local src_dir="$1"

    cd $src_dir
    ./bootstrap.sh
    ./configure --with-mysql --with-libcurl --enable-server --prefix="$src_dir"

    make install
}

# Stop Zabbix server if it is running.
#
# Arguments:
#   $1:   directory with zabbix_server.pid file
#
stop_server() {
    local server_dir="$1"
    local pid_file="$server_dir/zabbix_server.pid"

    if [ -f "$pid_file" ]; then
        pid=$(cat "$pid_file")

        if ps -p "$pid" > /dev/null; then
            kill "$pid"
        fi
    fi
}

# Start Zabbix server.
#
# Arguments:
#   $1:   directory with zabbis_server and zabbix_server.conf files
#
start_server() {
    local server_dir="$1"
    local conf_file="$server_dir/zabbix_server.conf"

    "$server_dir/zabbix_server" -c "$conf_file"
}

# Create database, will drop existing database. Runs make dbschema before import.
#
# Arguments:
#   $1:  server sources directory with .sql files
#   $2:  database name to create
#   $3:  database connection string
#
create_database() {
    local sql_dir="$1"
    local database="$2"
    local connection_string="$3"

    cd $sql_dir
    make dbschema_mysql --silent

    mysql $connection_string -e"DROP DATABASE IF EXISTS \`$database\`;"
    mysql $connection_string -e"CREATE DATABASE \`$database\` CHARACTER SET utf8 COLLATE utf8_bin;"
    mysql $connection_string $database < database/mysql/schema.sql
    mysql $connection_string $database < database/mysql/images.sql
    mysql $connection_string $database < database/mysql/data.sql
}

# Add .htaccess file with php settings suitable for zabbix frontend.
# Add index.php file with phpinfo.
#
# Arguments:
#   $1:  abosulte path to directory where to add .htaccess file
#
create_web_files() {
    local dir="$1"

    echo -e "Options +Indexes\nphp_value post_max_size 16M\nphp_value max_execution_time 0\nphp_value error_log $dir/php.error.log" > "$dir/.htaccess"
    echo "<?php phpinfo();" > "$dir/phpinfo.php"
    ln -s "$work_dir" "$dir/ui/modules/dev-module"
}

# Generate server and ui configuration files.
#
# Arguments:
#   $1:   absolute path to ui files root folder
#   $2:   database name
#
create_conf_files() {
    local dir="$1"
    local db_name="$2"
    local port="10051"
    local db_user="root"
    local db_password="mariadb"

    # Frontend configuration file
    local ui_conf=$(cat "$script_dir/zabbix.conf.php")
    local ui_dir="$dir/ui/conf"

    ui_conf="${ui_conf//\{ZBX_DATABASE\}/$db_name}"
    ui_conf="${ui_conf/\{ZBX_PORT\}/$port}"
    ui_conf="${ui_conf/\{ZBX_USER\}/$db_user}"
    ui_conf="${ui_conf/\{ZBX_PASSWORD\}/$db_password}"
    echo "$ui_conf" > "$ui_dir/zabbix.conf.php"

    # Server configuration file
    local server_conf=$(cat "$script_dir/zabbix_server.conf")
    local server_dir="/var/www/html/sbin"

    if [ -d "$server_dir" ]; then
        server_conf="${server_conf/\{ZBX_PORT\}/$port}"
        server_conf="${server_conf//\{ZBX_DIR\}/$server_dir}"
        server_conf="${server_conf/\{ZBX_DATABASE\}/$db_name}"
        server_conf="${server_conf/\{ZBX_USER\}/$db_user}"
        server_conf="${server_conf/\{ZBX_PASSWORD\}/$db_password}"
        echo "$server_conf" > "$server_dir/zabbix_server.conf"
    fi
}

# Generate module boilerplate files and directories: Module.php, manifest.json, actions, views
# doc: https://www.zabbix.com/documentation/current/en/devel/modules/file_structure/manifest
#
# Arguments:
#   $1:   module directory
#   $2:   manifest version: 1, 2
#
generate_boilerplate() {
    local dir="$1"
    local manifest_version="$2"
    local json=$(cat "$script_dir/boilerplate/manifest.json")

    local id=$(gum input --prompt "Module id: " --placeholder $(jq -r ".id" "$script_dir/boilerplate/manifest.json"))
    local namespace=$(gum input --prompt "Module namespace: " --placeholder $(jq -r ".namespace" "$script_dir/boilerplate/manifest.json"))

    if [ "${manifest_version:0:1}" = "1" ]; then
        json=$(echo "$json" | jq 'del(.assets)')
    else
        mkdir -p "$dir/assets/js"
        mkdir -p "$dir/assets/css"
    fi

    json=$(echo "$json" | jq --arg val "$manifest_version" '.manifest_version=$val')

    if [ -n "$id" ]; then
        json=$(echo "$json" | jq --arg val "$id" '.id=$val')
    fi

    if [ -n "$namespace" ]; then
        json=$(echo "$json" | jq --arg val "$namespace" '.namespace=$val')
    else
        namespace=$(jq -r ".namespace" "$script_dir/boilerplate/manifest.json")
    fi

    echo "$json" | jq '.' > "$dir/manifest.json"
    cp "$script_dir/boilerplate/Module.php" "$dir"
    sed -i -e "s|{ZBX_NAMESPACE}|$namespace|" "$dir/Module.php"

    mkdir -p "$dir/actions"
    mkdir -p "$dir/views"
}

# List remote branches on git.zabbix.com.
#
# Arguments:
#   $1:   branch minimal version, inclusive, ignored when set to empty string.
#   $2:   branch maximal version, inclusive, ignored when set to empty string.
#
# Returns:
#   Branch without "release/" prefix selected by user
#
select_branch() {
    local min_version="$1"
    local max_version="$2"

    gum choose --header "Select Zabbix version:" \
        $(git ls-remote --heads https://git.zabbix.com/scm/zbx/zabbix.git \
        | grep -Po "(?<=refs/heads/release/)\S+" \
        | awk -F'.' \
            -v max_version="$max_version" \
            -v min_version="$min_version" \
            '$2 ~ /^[0-9]+$/ \
            && (!min_version || ($1 "." $2) >= min_version) \
            && (!max_version || ($1 "." $2) <= max_version) \
            {print $1 "." $2}' \
        ) $(awk -v max_version="$max_version" 'BEGIN { if (!max_version || max_version >= 6.4) print "master"; else print "" }')
}

# Checkout Zabbix branch.
#
# Arguments:
#   $1:  directory to clone directory to
#   $2:  zabbix branch to clone, branch name should be without "release/" prefix. example: 6.0 6.4 master
#
checkout_branch() {
    local directory="$1"
    local branch="$([ "$2" != "master" ] && echo "release/$2" || echo "$2")"

    rm -rf $directory/{*,.*}
    clone_zabbix "$directory" "$branch"
}

# Show succss message.
#
# Arguments:
#   $1:  success message
#
success() {
    gum style --foreground "#0f0" "$1"
}