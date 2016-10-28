@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../sebastian/phpcpd/composer/bin/phpcpd
php "%BIN_TARGET%" %*
