<?php

namespace Nikaia\TranslationSheet\Client;

class Client extends \Google_Client
{
    /**
     * Return configured google api client.
     *
     * @param $authConfigFile
     * @param $applicationName
     *
     * @return Client
     */
    public static function create($authConfigFile, $applicationName)
    {
        self::checkAuthConfigFile($authConfigFile);

        $client = new static();
        $client->setAuthConfig($authConfigFile);
        $client->setApplicationName($applicationName);
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);

        return $client;
    }

    /**
     * Throw an exception if google service config file is not found.
     *
     * @param $authConfigFile
     * @throws \Exception
     */
    private static function checkAuthConfigFile($authConfigFile)
    {
        if (! file_exists($authConfigFile)) {
            throw new \Exception('You must specify a valid google service authentication file. Given file ['.$authConfigFile.'] does not exists');
        }
    }
}
