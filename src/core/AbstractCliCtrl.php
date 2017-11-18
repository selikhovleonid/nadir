<?php

namespace nadir\core;

/**
 * This is an abstract class of cli-controller.
 * @author Roman Lapotchkin
 * @author Leonid Selikhov
 */
class AbstractCliCtrl
{

    /**
     * The method returns options from command line argument list.
     * @param array $aOpt An array of short options (for ex.  -f)
     * @param array $aLongOpt An array of long options (for ex.  --foo)
     * @return array|false It returns FALSE if an error occures.
     */
    protected function getCmdOpt(array $aOpt, array $aLongOpt = array())
    {
        return getopt(implode('', $aOpt), $aLongOpt);
    }

    /**
     * Print message to console
     * @param string $sMessage Message to print
     * @return string Message with time
     */
    protected function printLog($sMessage)
    {
        $oDate = new \DateTime();
        echo $oDate->format('H:i:s')."\t{$sMessage}".PHP_EOL;
        return '('.$oDate->format('H:i:s').') '.preg_replace(
            '#(\[[0-9]+m)#',
            '',
            $sMessage
        );
    }

    /**
     * Print error to console
     * @param \Exception $e
     * @return string Message with time
     */
    protected function printError(\Exception $e)
    {
        return $this->printLog("\033[41mError: {$e->getMessage()}\033[0m\n".
                "\t\t\033[41mFile: {$e->getFile()}:{$e->getLine()}\033[0m"); //Red
    }

    /**
     * Print important information to console
     * @param string $sMessage Message to print
     * @return string Message with time
     */
    protected function printInfo($sMessage)
    {
        return $this->printLog("\033[46m".$sMessage."\033[0m"); //Blue
    }

    /**
     * Print warning to console
     * @param string $sMessage Message to print
     * @return string Message with time
     */
    protected function printWarning($sMessage)
    {
        return $this->printLog("\033[43m".$sMessage."\033[0m"); //Yellow
    }
}
