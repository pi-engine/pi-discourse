pi-discourse
============

PHP clone of https://github.com/discourse/discourse

In `template/`, folder `discourse/` is a theme of pi-discourse, it shouldn't be here but I put it here for version control.

Before using pi-discour module, user should cut `usr/module/discourse/template/discourse/` to `theme/discourse` and install discourse theme(install, not use).

You can switch pjax on or off by changing line 3 in `/asset/script/js/boot.js`,
on: `pjax = 1;` 
off: `pjax = 0;`

using master branch and version of may 30, 2013. Cause latest version can't return json in discourse's restful controller, will debug later.

Pi version: `git checkout e27afda228cab6abe8e124dca35a784a49877cdb`
