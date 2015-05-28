#!/bin/bash
echo "linking example direcotries..."
script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
ext_dir=${script_dir}/../../../
cd ${ext_dir}
find `pwd`/Resources/Private/Examples/ -mindepth 1 -maxdepth 1 -type d ! -iname ".*" -print0 | xargs -I {} -0 ln -s {} `pwd`/../
echo "done"
