<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.02.16
 * Time: 17:28
 */

interface CommandLineInterpreterInterfaceAware {

    /**
     * Sets ConfigfilePath
     *
     * @param string $filePath
     * @return mixed
     */
    public function setFilePath($filePath = '');

    /**
     * Sets the Enviroment of the configuration
     *
     * @param string $enviroment
     * @return mixed
     */
    public function setEnviroment($enviroment = '');

    /**
     * Set a section of configparameter to get
     *
     * @param string $section
     * @return mixed
     */
    public function setSection($section = '');

    /**
     * Set Entry of item to get.
     *
     * @param string $entry
     * @return mixed
     */
    public function setEntry($entry = '');

    /**
     * Sets $argv from the commandline.
     *
     * @param array $argv
     * @return mixed
     */
    public function setArgv(array $argv);

    /**
     * Implement an inputvalidation.
     *
     * @return mixed
     */
    public function validate();

    /**
    * Print Help
    *
    * @param array $argv
    */
    public function attachHelpDumpListener(array $argv);

    /**
     * Returns configItem
     *
     * @param IniParser $parser
     * @return mixed
     */
    public function getItem(\IniParser $parser);
}