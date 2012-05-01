#!/bin/bash

find ./lib/Mmoreramerino/GearmanBundle/ | grep \.php | xargs -I {} phpcs {} > project/Reportings/codesniffer.log
