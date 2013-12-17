#!/bin/sh

git checkout 2.1 && git merge master && git push github 2.1 && git push bitbucket 2.1 && git checkout 2.2 && git merge master && git push github 2.2 && git push bitbucket 2.2 && git checkout 2.3 && git merge master && git push github 2.3 && git push bitbucket 2.3 && git checkout master
