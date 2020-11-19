#!/bin/bash

#!/bin/bash
set -o xtrace
set -o errexit
set -o nounset

user_agent="User-Agent: Mozilla/5.0 EQXIUAlertPlatform/1.0.0 (KHTML, like Gecko) Author/Mike"
ip=$1
service=$2
details=$3
msg=$4

echo "IP=$ip&Service=$service&Details=$details&MSG=$msg&Others=all" 

curl -H "$user_agent" -d "IP=$ip&Service=$service&Details=$details&MSG=$msg&Others=all" -X POST http://ams.example > /tmp/a.log 2>&1
