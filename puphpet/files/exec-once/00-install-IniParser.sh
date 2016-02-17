#!/bin/bash
#install Iniparser to get commandlinesupprt on handling infiles
echo "########################## Install IniParser #####################################"

iniParserBaseDir=/vagrant/puphpet/files/libraries

iniparserPackageFilePath=$iniParserBaseDir/IniParser.tar

echo "Installing IniParser from $iniparserPackageFilePath";

if [ -f "$iniparserPackageFilePath" ]
then
    echo "unpacking $iniParserBaseDir to $iniParserBaseDir"
    sudo tar zxf $iniparserPackageFilePath -C $iniParserBaseDir
else
    echo "InstallationPackage for IniParser not found under: $iniparserPackageFilePath"
fi
echo "charmoding 755 $iniParserBaseDir"

chmod -R 755 $iniParserBaseDir

exit