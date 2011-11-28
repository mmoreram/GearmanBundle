#!/bin/bash

find . | grep \.php | xargs -I {} phpcs {} > Project/Reportings/codesniffer.log
