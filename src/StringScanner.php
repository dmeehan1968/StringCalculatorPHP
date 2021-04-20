<?php


namespace StringCalculator;


class StringScanner
{

    private string $string;

    /**
     * StringScanner constructor.
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function scanCharactersFromSet(string $set, string& $dest = null): bool {
        while(!$this->atEnd()) {
            $chr = substr($this->string, 0, 1);
            if (strstr($set, $chr) !== false) {
                $dest .= $chr;
                $this->string = substr($this->string, 1);
            } else {
                break;
            }
        }
        return strlen($dest) > 0;
    }

    public function scanUpToString(string $upto, string & $dest = null): bool {
        $pos = strpos($this->string, $upto);
        if ($pos !== false) {
            $dest = substr($this->string, 0, $pos);
            $this->string = substr($this->string, $pos);
            return true;
        }
        return false;
    }

    public function scanString(string $string, string& $dest = null): bool {
        if (strpos($this->string, $string) === 0) {
            $this->string = substr($this->string, strlen($string));
            return true;
        }
        return false;
    }

    public function atEnd(): bool {
        return strlen($this->string) == 0;
    }

}