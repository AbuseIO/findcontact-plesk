<?php

namespace AbuseIO\FindContact;

use AbuseIO\Models\Account;
use AbuseIO\Models\Contact;
use RipeStat\AbuseContactFinder;
use Log;

/**
 * Class Plesk
 * @package AbuseIO\FindContact
 */
class Plesk
{
    private $finder;

    /**
     * Plesk constructor.
     * @param $finder
     */
    public function __construct()
    {
        $this->finder = new \PleskX\Api\Client($this->getHost());

        $this->setCredentialsForFinder();
    }


    /**
     * Get the abuse email address registered for this ip.
     * @param  string $ip IPv4 Address
     * @return mixed Returns contact object or false.
     */
    public function getContactByIp($ip)
    {
        $result = false;

        try {
            $result = $this->_getContactWithData(
                $this->_getContactDataForIp($ip)
            );
        } catch (\Exception $e) {
            Log::debug("Error while talking to the Plesk Stat API : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this domain.
     * @param  string $domain Domain name
     * @return mixed Returns contact object or false.
     */
    public function getContactByDomain($domain)
    {
        $result = false;

        try {
            $result = $this->getContactWithData(
                $this->_getContactDataForDomain($domain)
            );
        } catch (\Exception $e) {
            Log::debug("Error while talking to the Plesk Stat API : " . $e->getMessage());
        }
        return $result;
    }

    /**
     * Get the email address registered for this ip.
     * @param  string $id ID/Contact reference
     * @return mixed Returns contact object or false.
     */
    public function getContactById($id)
    {
        return false;
    }

    /**
     * search the ip using the plesk api and if found, return the abuse mailbox and network name
     *
     * @param $ip
     * @return array
     */
    private function _getContactDataForIp($ip)
    {
        return $this->getContactDataForQuery('ip', $ip);
    }

    /**
     * search the domain using the plesk api and if found, return the abuse mailbox and network name
     *
     * @param $ip
     * @return array
     */
    private function _getContactDataForDomain($domain)
    {
        return $this->getContactDataForQuery('domain', $domain);
    }

    /**
     * @throws \Exception
     */
    private function setCredentialsForFinder()
    {
        if (config('findcontact-plesk.secret_key')) {
            $this->finder->setSecretKey(config('findcontact-plesk.secret_key'));
        } else {
            $password = config('find-contact-plesk.password');
            $login = config('find-contact-plesk.login');

            if (!empty($password) && !empty($login)) {
                $this->finder->setCredentials($login, $password);
            } else {
                throw new \Exception('no valid credentials where set for plesk, please provide login and password, of your secret key')
            }
        }
    }

    /**
     * @param $data
     * @return Contact
     */
    private function getContactWithData($data)
    {
        // construct new contact
        $result = new Contact();
        $result->name = $data['name'];
        $result->reference = $data['name'];
        $result->email = $data['email'];
        $result->enabled = true;
        $result->auto_notify = config("Findcontact.findcontact-plesk.auto_notify");
        $result->account_id = Account::getSystemAccount()->id;
        $result->api_host = '';
        return $result;
    }

    private function getContactDataForQuery($field, $value)
    {
        $data = [];
        $name = null;
        $email = null;

        $userInfo = $this->finder->user()->get($field, $value);

        // only create a result data if both email and name are set
        if (!is_null($userInfo->name) && !is_null($userInfo->email)) {
            $data['name'] = $userInfo->name;
            $data['email'] = $userInfo->email;
        }

        return $data;
    }
}
