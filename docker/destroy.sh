#!/bin/bash
set -e

docker-compose down --volumes
docker rmi ether_apache ether_php
