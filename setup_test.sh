#!/bin/bash

set -o pipefail

SETUP='spryker'
DATABASE_NAME='DE_test_zed'
DATABASE_USER='development'
DATABASE_PASSWORD='mate20mg'
VERBOSITY='-v'
CONSOLE=vendor/bin/console
PHANTOMJS_CDNURL='https://github.com/Medium/phantomjs/'
DATABASE_BACKUP_PATH='data/test.backup'

export APPLICATION_ENV='test'

. deploy/setup/functions.sh

warningText "This script should be used only in development and NEVER IN PRODUCTION"

function backupTestingEnvData {

    dropDevelopmentDatabase

    labelText "Running setup:install"
    $CONSOLE setup:install $VERBOSITY
    writeErrorMessage "Setup install failed"

    labelText "Running propel code generation/migrations"
    $CONSOLE propel:install $VERBOSITY
    $CONSOLE propel:diff $VERBOSITY
    $CONSOLE propel:migrate $VERBOSITY

    labelText "Importing Demo data"
    $CONSOLE import:demo-data $VERBOSITY
    writeErrorMessage "DemoData import failed"

    labelText "Initializing DB"
    $CONSOLE setup:init-db $VERBOSITY
    writeErrorMessage "DB setup failed"

    runCollectors

    dumpDevelopmentDatabase $DATABASE_BACKUP_PATH

    successText "Backup successful."
}

function restoreTestingEnvData {

   dropAndRestoreDatabase $DATABASE_BACKUP_PATH
}

function runCollectors() {

   export PGPASSWORD=$DATABASE_PASSWORD
   labelText "Reset touch table."
   psql -h 127.0.0.1 -U $DATABASE_USER  -d $DATABASE_NAME -c 'UPDATE spy_touch set touched=now()' -w

   labelText "Run collectors."
   $CONSOLE collector:search:export $VERBOSITY
   $CONSOLE collector:storage:export $VERBOSITY
}

if [ $# -eq 0 ]; then
    displayHelp
    exit 0
fi

for arg in "$@"
do
    case $arg in

         "--restore" | "-r" )
           restoreTestingEnvData
           ;;

         "--backup" | "-b" )
           backupTestingEnvData
           ;;
         "--collectors" | "-c" )
           runCollectors
           ;;

        *)
            displayHeader
            echo ""
            echo "Unrecognized option: $@. Use -h to display help."
            exit 1
        ;;
   esac
done


exit 0
