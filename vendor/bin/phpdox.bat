@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../theseer/phpdox/composer/bin/phpdox
php "%BIN_TARGET%" %*
