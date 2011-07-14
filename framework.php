<?php
/*


*/
class ocframework
{
    //member variables
    public $versionpath, $version, $downloadpath, $targetdir;

    function ocframework()
    {
        $this->versionpath = false;
        $this->version = false;
        $this->downloadpath = false;
        $this->targetdir = '../';
    }
    function is__writable()
    {
        $path = $this->targetdir;
        if ($path{strlen($path) - 1} == '/') // recursively return a temporary file path

            return is__writable($path . uniqid(mt_rand()) . '.tmp');
        else
            if (is_dir($path))
                return is__writable($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;
        fclose($f);
        if (!$rm)
            unlink($path);
        return true;
    }
    function ocopen($filename, $mode)
    {
        $fp = @fopen($filename, $mode);
        if (!$fp)
        {
            $fp = @popen("curl $filename", "r");
            if (!$fp)
            {
                return false;
            }
        }
        return $fp;
    }
    function occlose($fp)
    {
        if (!@fclose($fp))
        {
            return @pclose($fp);
        }
        return true;
    }
    function getcontents($filename)
    {
        if ($file = @file_get_contents($filename))
        {
        }
        else
        {
            $curl = curl_init($filename);
            curl_setopt($curl, CURLOPT_HEADER, 0); // ignore any headers
            ob_start(); // use output buffering so the contents don't get sent directly to the browser
            curl_exec($curl); // get the file
            curl_close($curl);
            $file = ob_get_contents(); // save the contents of the file into $file
            ob_end_clean(); // turn output buffering back off
        }
        return $file;
    }
    function checkForUpdate()
    {
        //$updatefile = @fopen('http://anirudhsanjeev.org/oneclick/ocupdate.txt', 'r');
        $updatefile = @fopen($this->versionpath, 'r');
        if (@fgets($updatefile) == $this->version)
        {
            return true;
        }
        @fclose($updatefile);
        return false;
    }
    function update()
    {
        $file = $this->getcontents($this->downloadpath);
        $df = $this->ocopen($this->targetdir . 'temp.zip', 'w');
        if (!fwrite($df, $file))
        {
        }
        $this->occlose($df);
        if (!class_exists('PclZip'))
        {
            require_once ('pclzip.lib.php');
        }
        $archive = new PclZip($this->targetdir . 'temp.zip');
        if ($archive->extract(PCLZIP_OPT_PATH, $this->targetdir) == 0)
        {
            if (function_exists('exec'))
            {
                exec("unzip -d $this->targetdir $this->targetdir.'temp.zip'");
            }
            else
            {
                return false; //something horribly went wrong
            }
        }
        unlink($file_target);
        return true;

    }

}


?>