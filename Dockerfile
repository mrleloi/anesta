FROM docker.io/bitnami/wordpress:6.4.3-debian-12-r16

## Change user to perform privileged actions
USER 0
## Install 'vim'
RUN install_packages vim

## Revert to the original non-root user
USER 1001
WORKDIR /bitnami/wordpress
COPY . /bitnami/wordpress

## Enable mod_ratelimit module
RUN sed -i -r 's/#LoadModule ratelimit_module/LoadModule ratelimit_module/' /opt/bitnami/apache/conf/httpd.conf

## Modify the ports used by Apache by default
# It is also possible to change these environment variables at runtime
EXPOSE 8080 8443