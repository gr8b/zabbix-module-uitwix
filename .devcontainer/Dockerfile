FROM mcr.microsoft.com/devcontainers/php:1-8.2-bookworm

# Install gum https://github.com/charmbracelet/gum
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://repo.charm.sh/apt/gpg.key | sudo gpg --dearmor -o /etc/apt/keyrings/charm.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/charm.gpg] https://repo.charm.sh/apt/ * *" | sudo tee /etc/apt/sources.list.d/charm.list
RUN apt-get update && apt-get install -y gum

# Install starship https://starship.rs
RUN curl https://starship.rs/install.sh -o starship.sh \
    && chmod +x starship.sh \
    && ./starship.sh -y \
    && rm -rf ./starship.sh

# Install MariaDB client
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y mariadb-client libmariadb-dev libgettextpo-dev \
        locales unzip \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        libaio1 libxml2-dev libldap2-dev \
        automake shtool build-essential libaio-dev libcurl4-openssl-dev libevent-dev libgnutls28-dev \
        libiksemel-dev libopenipmi-dev libpcre3-dev libsnmp-dev libssh2-1-dev \
    && apt-get clean -y && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap bcmath gettext sockets

# Install php-mysql driver
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install php-gd
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# Install phpunit 8.5
RUN curl -LO https://phar.phpunit.de/phpunit-8.5.phar
RUN chmod +x phpunit-8.5.phar && mv phpunit-8.5.phar /usr/local/bin/phpunit

# [Optional] Uncomment this section to install additional OS packages.
# RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
#     && apt-get -y install --no-install-recommends <your-package-list-here>

# [Optional] Uncomment this line to install global node packages.
# RUN su vscode -c "source /usr/local/share/nvm/nvm.sh && npm install -g <your-package-here>" 2>&1





# USER vscode

# RUN eval "$(starship init bash)"