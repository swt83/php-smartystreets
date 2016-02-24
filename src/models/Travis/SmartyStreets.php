<?php

namespace Travis;

class SmartyStreets
{
    /**
     * Return an API response.
     *
     * @param   array   $input
     * @return  array
     */
    public static function run($input)
    {
        // query
        $url = 'https://api.smartystreets.com/street-address?'.
            'street='.urlencode($input['street']).'&'.
            'city='.urlencode($input['city']).'&'.
            'state='.urlencode($input['state']).'&'.
            'zipcode='.urlencode($input['zip']).'&'.
            'auth-id='.urlencode($input['auth_id']).'&'.
            'auth-token='.urlencode($input['auth_token']);

        // submit and decode
        $response = json_decode(file_get_contents($url));

        // if smarty gave us an answer...
        if (!empty($response))
        {
            // return
            return array(
                'street' => $response[0]->delivery_line_1,
                'city' => $response[0]->components->city_name,
                'state' => $response[0]->components->state_abbreviation,
                'zip' => $response[0]->components->zipcode.'-'.$response[0]->components->plus4_code,
                'verbose' => $response,
            );
        }
        else
        {
            // return
            return false;
        }
    }
}