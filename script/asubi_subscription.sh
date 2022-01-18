#!/bin/sh
#
# Asubi 定期課金実行バッチ
# 毎月１日 午前8時に実行
# 0 8 1 * * ${HOME}/.cron_profile; bash ${SRCPATH}/script/asubi_subscription.sh
#--------------------------------------------
php ${SRCPATH}/htdocs/index.php cli asubi subscription
