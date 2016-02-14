#!/bin/bash
#install Iniparser to get commandlinesupprt on handling infiles

iniParserBaseDir=/vagrant/puphpet/files/libraries

iniparserPackageFilePath=$iniParserBaseDir/IniParser.tar

echo "Installing IniParser from $iniparserPackageFilePath";

if [ -f "$iniparserPackageFilePath" ]
then
    sudo tar zxf $iniparserPackageFilePath -C $iniParserBaseDir
else
    echo "InstallationPackage for IniParser not found under: $iniparserPackageFilePath"
fi

chmod -R 755 $iniParserBaseDir

exit