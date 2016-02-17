<?php
/**
 * Class CommandLineInputResolver
 *
 * A commandlinetool to wrapp
 *
 * Usage @see commandLineTool.php
 */

class CommandLineInputResolver implements \CommandlineInterpreterInterfaceAware
{
    /**
     * Contains valid commandlineinput order
     *
     * @var array
     */
    protected $_inputParameterOrder = array('enviroment'=> 1,'section'=> 2,'entry'=> 3,'filePath'=> 4);

    /**
     * List with optional Arguments.
     *
     * @var array
     */
    protected $_optionalArguments = array('filePath');

    /**
     * Default file path
     */
    public static $DEFAULT_FILEPATH = '/vagrant/puphpet/files/config/config.ini';

    /**
     * Default enviroment parameter.
     */
    public static $DEFAULT_ENVIROMENT = 'testing';

    /**
     * Contains the path to the ini file
     * @var string
     */
    private $_filePath;

    /**
     * Live / Dev / Stage.Depends on available enviroments in inifile
     *
     * @var string
     */
    private $_enviroment;

    /**
     * Contains section F.i Database
     *
     * @var string
     */
    private $_section;

    /**
     * Contains the requestes ini file entry key
     *
     * @var string
     */
    private $_entry;

    /**
     * Contains the value of the requested entry
     *
     * @var mixed
     */
    protected $_value;

    /**
     * Contains Commandline Input of the process.
     *
     * @var array
     */
    protected $_argv= array();

    /**
     * Saves commandlineonput.
     *
     * @param array $argv
     */
    public function __construct(array $argv = null)
    {
        $this->_argv = $argv;

        $this->validate();
    }

    /**
     * Gets ConfigItem.
     *
     * @param IniParser $parser
     * @return void
     */
    public function getItem(\IniParser $parser)
    {
        $config = $parser->parse();

        if(!isset($config->{$this->getEnviroment()}))
        {
            throw new InvalidArgumentException('no enviroment found for: '.$this->getEnviroment());
        }

        if(!isset($config->{$this->getEnviroment()}->{$this->getSection()}))
        {
            throw new InvalidArgumentException('no section found for: '.$this->getSection());
        }

        if(!isset($config->{$this->getEnviroment()}->{$this->getSection()}->{$this->getEntry()}))
        {
            throw new InvalidArgumentException('no entry found for: '.$this->getEntry(). ' in section: ' . $this->getSection() );
        }

        echo $config->{$this->getEnviroment()}->{$this->getSection()}->{$this->getEntry()};
    }

    /**
     * Validates commandlineInputs.
     */
    public function validate()
    {
        try
        {
            $this->attachHelpDumpListener($this->_argv);

            $this->setDefaultFilePath();

            $this->attachFileDumpListener($this->_argv);

            $this->_mapCommandLineInput();

        }
        catch (Exception $e)
        {
            $this->_dumpError($e);
        }
    }

    /**
     * Dunps Exception and adds help to console
     *
     * @param Exception $e
     */
    protected function _dumpError(Exception $e)
    {
        echo PHP_EOL;

        echo "\033[31m Error: ".$e->getMessage(). "\033[0m" . PHP_EOL;

        $this->attachHelpDumpListener(array(1=>'-h'));
    }

    /**
     * Maps Commandlineinput to $this.
     */
    protected function _mapCommandLineInput()
    {
        foreach($this->_inputParameterOrder as $parameterName=>$index)
        {
            //hanlde optional empty inputs
            if(in_array($parameterName, $this->_optionalArguments) && empty($this->_argv[$index]))
            {
                $this->_setProperties($parameterName, $index, 'setDefault');
            }
            else
            {
                $this->_setProperties($parameterName, $index,'set');
            }
        }
    }

    /**
     * Invokes lokal setters for current property.
     *
     * @param $propertyName
     * @param $index
     * @param string $prefix
     */
    protected function _setProperties($propertyName, $index, $prefix = 'set')
    {
        $methodName = $prefix . ucfirst($propertyName);

        $argument = $this->getArgumentByIndex($index);

        if (method_exists($this, $methodName))
        {
            $this->$methodName($argument);
        }
        else
        {
            throw new InvalidArgumentException($methodName.' is not implemented.');
        }
    }

    /**
     * If no filepath was given set default.
     */
    public function setDefaultFilePath()
    {
        if(isset($this->_argv[$this->_inputParameterOrder['filePath']]))
        {
            $this->setFilePath($this->_argv[$this->_inputParameterOrder['filePath']]);
        }
        else
        {
            $this->setFilePath(self::$DEFAULT_FILEPATH);
        }
    }

    /**
     * Returns argument by index.
     *
     * @param $argvIndex
     * @return mixed
     */
    public function getArgumentByIndex($argvIndex)
    {
        if(!isset($this->_argv[$argvIndex]))
        {
            return null;
        }

        return $this->_argv[$argvIndex];
    }

    /**
     * Print Help.
     *
     * @param array $argv
     */
    public function attachHelpDumpListener(array $argv)
    {
        if (isset($argv[1]) && $argv[1] == "-h" || !isset($argv[1])) {
            echo '##########################################################################################' . PHP_EOL;
            echo '# Commandlinetool to get configitems.' . PHP_EOL;
            echo '# Defaultconfigfile: ' . self::$DEFAULT_FILEPATH . PHP_EOL;
            echo '# ' . PHP_EOL;
            echo '# Parameter:' . PHP_EOL;
            echo '# -h help' . PHP_EOL;
            echo '# -a display all configitems stored in the default inifile:' . PHP_EOL;
            echo '#' . PHP_EOL;
            echo '# display all configitems stored in custom inifile.' . PHP_EOL;
            echo '# -a null null </path/to inifile>' . PHP_EOL;
            echo '#' . PHP_EOL;
            echo '# Usage:' . PHP_EOL;
            echo '# ./getconfig <enviroment> <section> <entry> <filepath>:' . PHP_EOL;
            echo '# ' . PHP_EOL;
            echo '##########################################################################################' . PHP_EOL;
            exit;
        }
    }

    /**
     * Prints the whole file.
     *
     * @param array $argv
     */
    protected function attachFileDumpListener(array $argv)
    {
        if(isset($argv[1]) && $argv[1]=="-a")
        {
            echo file_get_contents($this->_filePath);
            exit;
        }
    }

    /**
     * Sets filePath.
     *
     * @param string $filePath
     * @return void
     */
    public function setFilePath($filePath='')
    {
        if(!file_exists($filePath))
        {
            throw new InvalidArgumentException("filepath : >".$filePath.'< does not exist.');
        }else
        {
            $this->_filePath = $filePath;
        }
    }

    /**
     * Sets enviroment.
     *
     * @param string $enviroment
     * @return mixed|void
     */
    public function setEnviroment($enviroment='')
    {
        if(!isset($enviroment))
        {
            $this->_enviroment = self::$DEFAULT_ENVIROMENT;
        }
        else
        {
            $this->_enviroment = $enviroment;
        }
    }

    /**
     * Set Section.
     *
     * @param string $section
     * @throws Exception
     * @return void
     */
    public function setSection($section = '')
    {
        if(!isset($section) || empty($section))
        {
            throw new Exception('no section set. (second parameter)');
        }

        $this->_section = $section;
    }

    /**
     * Set entry
     *
     * @param string $entry
     * @return void
     */
    public function setEntry($entry = '')
    {
        if(!isset($entry) || empty($entry))
        {
            throw new InvalidArgumentException('no entry set. type -a for all entries');
        }

        $this->_entry = $entry;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value='')
    {
        $this->_value = $value;
    }

    /**
     * Set Commandline arguments.
     *
     * @param array $argv
     * @return void
     */
    public function setArgv(array $argv)
    {
        $this->_argv = $argv;
    }

    /**
     * Get filePath.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->_filePath;
    }

    /**
     * @return string
     */
    public function getSection()
    {
        return $this->_section;
    }
    /**
     * @return string
     */
    public function getEntry()
    {
        return $this->_entry;
    }
    /**di
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return string
     */
    public function getEnviroment()
    {
        return $this->_enviroment;
    }
}