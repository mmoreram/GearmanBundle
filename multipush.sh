#!/bin/sh

git checkout master && git pull --rebase origin master && git push origin master && git push bitbucket master && git checkout 2.1  && git merge master  && git push origin 2.1  && git push bitbucket 2.1  && git checkout 2.2 && git merge master && git push origin 2.2 && git push bitbucket 2.2 && git checkout 2.3 && git merge master && git push origin 2.3 && git push bitbucket 2.3 && git checkout master
