<?php

/**
 * A LaravelPHP Package for working w/ the SmartyStreets API.
 *
 * @package    SmartyStreets
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83
 * @license    MIT License
 */

class SmartyStreets {
    
    /**
     * Clean an address.
     *
     * @param   array   $input
     * @return  array
     */
    public static function run($input)
    {
        // required
        $required = array('street', 'city', 'state', 'zip');

        // verify fields
        $keys = array_keys($input);
        foreach ($required as $field)
        {
            // if missing...
            if (!in_array($field, $keys))
            {
                // error
                trigger_error('Missing field "'.$field.'" from input.');

                // return
                return false;
            }
        }
        
        // hash
        $hash = static::hash($input);

        // check
        $check = DB::table('smartystreets')->where('hash', '=', $hash)->first();
        
        // if found...
        $lookup = false;
        if ($check)
        {
            // We don't want to always used pre-existing records,
            // if those records are too old. So we check age.

            $age = time() - strtotime($check->created_at);

            // if over a year old...
            if ($age > 31557600)
            {
                // lookup
                $lookup = static::api($input);

                // if successful...
                if ($lookup)
                {
                    // prepare
                    $final = array(
                        'created_at' => $check->created_at,
                        'updated_at' => strftime('%F', time()),
                        'is_success' => 1,
                        'original_hash' => $hash,
                        'original_street' => $input['street'],
                        'original_city' => $input['city'],
                        'original_state' => $input['state'],
                        'original_zip' => $input['zip'],
                        'hash' => static::hash($lookup),
                        'street' => $lookup['street'],
                        'city' => $lookup['city'],
                        'state' => $lookup['state'],
                        'zip' => $lookup['zip'],
                        'response' => serialize($lookup['response']),
                    );

                    // save
                    DB::table('smartystreets')->where('original_hash', '=', $hash)->update($final);

                    // return
                    return $final;
                }
                else
                {
                    // prepare
                    $final = array(
                        'created_at' => $check->created_at,
                        'updated_at' => strftime('%F', time()),
                        'is_success' => 0,
                        'original_hash' => $hash,
                        'original_street' => $input['street'],
                        'original_city' => $input['city'],
                        'original_state' => $input['state'],
                        'original_zip' => $input['zip'],
                        'hash' => '',
                        'street' => '',
                        'city' => '',
                        'state' => '',
                        'zip' => '',
                        'response' => '',
                    );

                    // save
                    DB::table('smartystreets')->where('original_hash', '=', $hash)->update($final);

                    // return
                    return $final;
                }
            }
            else
            {
                // return old lookup
                return (array) $check;
            }
        }
        else
        {
            // lookup
            $lookup = static::api($input);

            // if successful...
            if ($lookup)
            {
                // prepare
                $final = array(
                    'created_at' => strftime('%F', time()),
                    'updated_at' => strftime('%F', time()),
                    'is_success' => 1,
                    'original_hash' => $hash,
                    'original_street' => $input['street'],
                    'original_city' => $input['city'],
                    'original_state' => $input['state'],
                    'original_zip' => $input['zip'],
                    'hash' => static::hash($lookup),
                    'street' => $lookup['street'],
                    'city' => $lookup['city'],
                    'state' => $lookup['state'],
                    'zip' => $lookup['zip'],
                    'response' => serialize($lookup['response']),
                );

                // save
                DB::table('smartystreets')->insert($final);

                // return
                return $final;
            }
            else
            {
                // prepare
                $final = array(
                    'created_at' => strftime('%F', time()),
                    'updated_at' => strftime('%F', time()),
                    'is_success' => 0,
                    'original_hash' => $hash,
                    'original_street' => $input['street'],
                    'original_city' => $input['city'],
                    'original_state' => $input['state'],
                    'original_zip' => $input['zip'],
                    'hash' => '',
                    'street' => '',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'response' => '',
                );

                // save
                DB::table('smartystreets')->insert($final);

                // return
                return $final;
            }
        }
    }

    /**
     * Check the address against SmartyStreets.
     *
     * @param   array   $input
     * @return  array
     */
    protected static function api($input)
    {
        // query
        $url = 'https://api.qualifiedaddress.com/street-address/?'.
            'street='.urlencode($input['street']).'&'.
            'city='.urlencode($input['city']).'&'.
            'state='.urlencode($input['state']).'&'.
            'zipcode='.urlencode($input['zip']).'&'.
            'auth-token='.urlencode(Config::get('smartystreets.api_key'));
        
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
                'response' => $response,
            );
        }
        else
        {
            // return
            return false;
        }
    }

    /**
     * Build a hash of the input.
     *
     * @param   array   $input
     * @return  string
     */
    protected static function hash($input)
    {
        return md5(strtoupper($input['street'].'+'.$input['city'].'+'.$input['state'].'+'.$input['zip']));
    }

}