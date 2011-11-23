#!/bin/bash

find . | grep \.php | xargs -I {} phpcs {} > codesniffer.log
