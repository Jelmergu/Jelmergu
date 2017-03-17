<?php


namespace Jelmergu\Exceptions;

class InvalidFileSystemRights extends Exception
{
    public $perms;
    public $path;
    public $missing;


    public function __construct($code = 0, $path = ".", $message = "", \Exception $previous = NULL)
    {

        $message = $message == "" ? "Missing permissions" : $message . " Missing permissions";

        $perms = base_convert(fileperms($path), 10, 8);
        $perms = substr($perms, (strlen($perms) - 3));

        if ($code < 8) {
            $code = $code . 00;
        }
        elseif ($code < 78) {
            $code = $code . 0;
        }

        $this->perms['required'] = str_split($code);
        $this->perms['given'] = str_split($perms);

        $missing = [];
        $userLevel = ["Owner", "Group", "Everyone"];

        foreach ($this->perms['required'] as $user => $value) {
            $string = "";
            $required = $this->calculatePermission((int) $value);
            $given = $this->calculatePermission((int) $this->perms['given']);
            foreach ($required as $key => $permis) {
                if (isset($given[$key]) === TRUE || $key == 0) {
                    continue;
                }
                $string .= $string == "" ? $permis : ", " . $permis;
            }
            if ($string !== "") {
                $message .= " for " . $userLevel[$user] . ": " . $string;
            }
        }
        parent::__construct($message, $code, $previous);

    }

    /**
     * Calculate the permission
     *
     * @param $permission The permission to calculate
     *
     * @return array
     */
    private function calculatePermission($permission)
    {
        $permissions = [
            0 => "No permission",
            1 => "Execute",
            2 => "Write",
            4 => "Read",
        ];
        $needs = [];
        if ($permission >= 4) {
            $needs[4] = $permissions[4];
            $permission -= 4;
        }
        if ($permission >= 2) {
            $needs[2] = $permissions[2];
            $permission -= 2;
        }
        if ($permission == 1) {
            $needs[1] = $permissions[1];

            return $needs;
        }
        if ($permission == 0 && count($needs) == 0) {
            $needs[0] = $permissions[0];

            return $needs;
        }

        return $needs;

    }

    protected function setPrefix()
    {
        $this->prefix = "Missing permissions";
    }
}
